<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

include "../../db/config.php";

// Handling user deletion (only for super admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_SESSION['role'] == 1) {
        switch ($_POST['action']) {
            case 'delete':
                $user_id_to_delete = $_POST['user_id'];
                if ($user_id_to_delete != $_SESSION['user_id']) {
                    $delete_sql = "DELETE FROM users WHERE user_id = ?";
                    $stmt = $conn->prepare($delete_sql);
                    $stmt->bind_param("i", $user_id_to_delete);

                    if ($stmt->execute()) {
                        $_SESSION['message'] = "User successfully deleted.";
                    } else {
                        $_SESSION['error'] = "Error deleting user.";
                    }
                    $stmt->close();
                    break;
                }
                else {
                    // Optional: Add a message about not deleting own account
                    $_SESSION['error'] = "Cannot delete your own account.";
                }

            case 'edit':
                $user_id = $_POST['user_id'];
                $name = $_POST['name'];
                $email = $_POST['email'];

                $update_sql = "UPDATE users SET fname = ?, email = ? WHERE user_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ssi", $name, $email, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "User updated successfully.";
                } else {
                    $_SESSION['error'] = "Error updating user.";
                }
                $stmt->close();
                break;
        }

        header("Location: users.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-orange: #FF8C00;    /* Dark orange */
            --secondary-orange: #FFA500;   /* Regular orange for hover */
            --dark-gray: #333333;         /* Lighter shade of black */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: var(--dark-gray);
            color: white;
            padding: 1rem;
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .header h1 {
            flex: 1;
            text-align: center;
            margin-right: 3rem;
        }

        .menu-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 1rem;
            width: 3rem;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--primary-orange);
            padding-top: 4rem;
            transition: 0.3s;
            z-index: 100;
        }

        .sidebar.collapsed {
            left: -250px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: var(--secondary-orange);
        }

        .sidebar a i {
            width: 1.5rem;
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px;
            padding: 5rem 1rem 1rem;
            transition: 0.3s;
            background-color: #FFF8F0;  /* Very light orange background */
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Message styles */
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* User management container */
        .user-management-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Table styles */
        .user_table {
            width: 100%;
            border-collapse: collapse;
        }

        .user_table th, .user_table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .user_table th {
            background-color: var(--primary-orange);
            color: white;
        }

        /* Action buttons */
        .edit-btn, .delete-btn, .view-btn {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
            border-radius: 3px;
        }

        .edit-btn:hover, .delete-btn:hover, .view-btn:hover {
            background-color: var(--secondary-orange);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1500;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }

        .modal-content .form-group {
            margin-bottom: 15px;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-content .btn-submit {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-content .btn-submit:hover {
            background-color: var(--secondary-orange);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="header">
    <button id="menuToggle" class="menu-btn">☰</button>
    <h1>User Activity</h1>
</header>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <a href="../../index.php"><i class="fas fa-home"></i>Home</a>
    <a href="dashboard.php"><i class="fas fa-chart-bar"></i>Dashboard</a>
    <a href="manage_quizzes.php"><i class="fas fa-question-circle"></i>Quizzes</a>
    <a href="users.php"><i class="fas fa-users"></i>Users</a>
</nav>

<!-- Main Content -->
<main class="main-content" id="main">
    <div class="user-management-container">
        <?php
        // Display success or error messages
        if (isset($_SESSION['message'])) {
            echo "<div class='success-message'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }

        if ($_SESSION['role'] == 1) {
            $sql = "SELECT user_id, fname, lname, email, role, registration_date FROM users";
            $result = $conn->query($sql);
        }
        elseif ($_SESSION['role'] == 2) {
            $sql = "SELECT user_id, fname, lname, email, role, registration_date FROM users WHERE user_id = ?";
            $statement = $conn->prepare($sql);
            $statement->bind_param("i", $_SESSION['user_id']);
            $statement->execute();
            $result = $statement->get_result();
        }
        else {
            echo "<p>You are not logged in</p>";
            echo "<button>Sign In Here!</button>";
        }
        ?>

        <?php if ($_SESSION['role'] == 1): ?>
            <h2>All Users</h2>
        <?php else: ?>
            <h2>My Profile</h2>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="user_table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <?php if ($_SESSION['role'] == 1): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fname"] . " " . $row["lname"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["registration_date"]) . "</td>";

                    if ($_SESSION['role'] == 1) {
                        echo "<td>
                                <button class='edit-btn' data-id='" . $row["user_id"] . "' 
                                        data-fname='" . htmlspecialchars($row["fname"]) . "' 
                                        data-email='" . htmlspecialchars($row["email"]) . "'><i class='fas fa-edit'></i></button>
                                <button class='delete-btn' data-id='" . $row["user_id"] . "'><i class='fas fa-trash'></i></button>
                                <button class='view-btn' data-id='" . $row["user_id"] . "'><i class='fas fa-eye'></i></button>
                            </td>";
                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit User</h2>
        <form id="editUserForm" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="editUserId" name="user_id">

            <div class="form-group">
                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name" required>
                <span id="nameError" class="error"></span>
            </div>

            <div class="form-group">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>
                <span id="emailError" class="error"></span>
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</div>

<!-- View More Modal -->
<div id="viewMoreModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>User Details</h2>
        <div id="userDetails"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main');

        // Menu toggle
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Edit Modal Handling
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editUserForm');
        const editButtons = document.querySelectorAll('.edit-btn');
        const closeEditModal = editModal.querySelector('.close');

        editButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const userId = e.currentTarget.dataset.id;
                const name = e.currentTarget.dataset.fname;
                const email = e.currentTarget.dataset.email;

                document.getElementById('editUserId').value = userId;
                document.getElementById('editName').value = name;
                document.getElementById('editEmail').value = email;
                editModal.style.display = 'block';
            });
        });

        closeEditModal.addEventListener('click', () => {
            editModal.style.display = 'none';
        });

        // Form Validation
        editForm.addEventListener('submit', (e) => {e.preventDefault();
            if (validateForm()) {
                editForm.submit();
            }
        });

        function validateForm() {
            let isValid = true;
            const name = document.getElementById('editName');
            const email = document.getElementById('editEmail');
            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');

            nameError.textContent = '';
            emailError.textContent = '';

            if (name.value.trim() === '') {
                nameError.textContent = 'Name is required';
                isValid = false;
            }

            if (email.value.trim() === '') {
                emailError.textContent = 'Email is required';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                emailError.textContent = 'Invalid email format';
                isValid = false;
            }

            return isValid;
        }

        // Delete User Handling
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const userId = e.currentTarget.dataset.id;
                if (confirm('Are you sure you want to delete this user?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user_id" value="${userId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // View More Modal
        const viewMoreModal = document.getElementById('viewMoreModal');
        const closeViewMoreModal = viewMoreModal.querySelector('.close');
        const userDetails = document.getElementById('userDetails');
        const viewButtons = document.querySelectorAll('.view-btn');

        viewButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const userId = e.currentTarget.dataset.id;

                // In a real application, you'd fetch user details via AJAX
                const dummyData = {
                    id: userId,
                    name: 'Sample User',
                    email: 'user@example.com',
                    joinDate: '2023-01-15',
                    lastLogin: '2023-05-20',
                    recipesShared: 5
                };

                userDetails.innerHTML = `
                    <p><strong>ID:</strong> ${dummyData.id}</p>
                    <p><strong>Name:</strong> ${dummyData.name}</p>
                    <p><strong>Email:</strong> ${dummyData.email}</p>
                    <p><strong>Join Date:</strong> ${dummyData.joinDate}</p>
                    <p><strong>Last Login:</strong> ${dummyData.lastLogin}</p>
                    <p><strong>Recipes Shared:</strong> ${dummyData.recipesShared}</p>
                `;

                viewMoreModal.style.display = 'block';
            });
        });

        closeViewMoreModal.addEventListener('click', () => {
            viewMoreModal.style.display = 'none';
        });

        // Close modals when clicking outside
        window.addEventListener('click', (event) => {
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === viewMoreModal) {
                viewMoreModal.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
<?php $conn->close(); ?>