<?php
session_start();

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1)) {
    header("Location: ../../index.php");
    exit();
}

// Database connection
include '../../db/config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create new quiz
    if (isset($_POST['create_quiz'])) {
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $name = mysqli_real_escape_string($conn, $_POST['quiz_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);

        $query = "INSERT INTO quizzes (category_id, name, description, duration_minutes) VALUES ('$category_id', '$name', '$description', '$duration')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: dashboard.php?success=Quiz created successfully");
        } else {
            header("Location: dashboard.php?error=Failed to create quiz");
        }
        exit();
    }

    // Update quiz
    if (isset($_POST['update_quiz'])) {
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $name = mysqli_real_escape_string($conn, $_POST['quiz_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);

        $query = "UPDATE quizzes SET category_id = '$category_id', name = '$name', description = '$description', duration_minutes = '$duration' WHERE id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: dashboard.php?success=Quiz updated successfully");
        } else {
            header("Location: dashboard.php?error=Failed to update quiz");
        }
        exit();
    }

    // Delete quiz
    if (isset($_POST['delete_quiz'])) {
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);
        $query = "DELETE FROM quizzes WHERE id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: dashboard.php?success=Quiz deleted successfully");
        } else {
            header("Location: dashboard.php?error=Failed to delete quiz");
        }
        exit();
    }

}




// Fetch quizzes and categories for display
$quizzes_query = "SELECT q.*, c.name as category_name FROM quizzes q JOIN categories c ON q.category_id = c.id ORDER BY c.name, q.name";
$quizzes_result = mysqli_query($conn, $quizzes_query);
$quizzes = [];
while ($row = mysqli_fetch_assoc($quizzes_result)) {
    $quizzes[] = $row;
}

$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        /* Quiz Management Styles */
        .quiz-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .quiz-table th, .quiz-table td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }

        .quiz-table th {
            background-color: var(--primary-orange);
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-create {
            background-color: var(--primary-orange);
            color: white;
            margin-bottom: 1rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 1rem;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            margin-bottom: 1rem;
        }

        .modal-close {
            color: #aaa;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
        }

        .error-message {
            background-color: red;
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
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
    <h1>Quiz Management</h1>
</header>

<nav class="sidebar" id="sidebar">
    <a href="../../index.php"><i class="fas fa-home"></i>Home</a>
    <a href="dashboard.php"><i class="fas fa-chart-bar"></i>Dashboard</a>
    <a href="#" onclick="showQuizManagement()"><i class="fas fa-question-circle"></i>Quizzes</a>
    <a href="users.php"><i class="fas fa-users"></i>Users</a>
</nav>

<main class="main-content" id="main">
    <h2>Welcome to Quiz Management</h2>

    <?php if(isset($_GET['success'])): ?>
        <div style="background-color: green; color: white; padding: 1rem; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div id="quizManagementSection" style="display: none;">
        <button class="btn btn-create" onclick="openModal('createQuizModal')">
            <i class="fas fa-plus"></i> Create New Quiz
        </button>

        <table class="quiz-table">
            <thead>
            <tr>
                <th>Quiz Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Duration (mins)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($quizzes as $quiz): ?>
                <tr>
                    <td><?php echo htmlspecialchars($quiz['name']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['description'] ?? 'No description'); ?></td>
                    <td><?php echo htmlspecialchars($quiz['duration_minutes']); ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editQuiz(
                        <?php echo $quiz['id']; ?>,
                                '<?php echo htmlspecialchars($quiz['name']); ?>',
                                '<?php echo htmlspecialchars($quiz['category_id']); ?>',
                                '<?php echo htmlspecialchars($quiz['description'] ?? ''); ?>',
                        <?php echo $quiz['duration_minutes']; ?>
                                )">Edit</button>
                        <button class="btn btn-delete" onclick="deleteQuiz(<?php echo $quiz['id']; ?>)">Delete</button>
                        <hr>
                        <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-question-circle"></i> Manage Questions
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Create Quiz Modal -->
<div id="createQuizModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Quiz</h3>
            <span class="modal-close" onclick="closeModal('createQuizModal')">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="create_quiz" value="1">
            <label>
                Category:
                <select name="category_id" required>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Quiz Name:
                <input type="text" name="quiz_name" required>
            </label>
            <label>
                Description:
                <textarea name="description"></textarea>
            </label>
            <label>
                Duration (minutes):
                <input type="number" name="duration" min="1" max="60" required>
            </label>
            <button type="submit" class="btn btn-create">Create Quiz</button>
        </form>
    </div>
</div>

<!-- Edit Quiz Modal -->
<div id="editQuizModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Quiz</h3>
            <span class="modal-close" onclick="closeModal('editQuizModal')">&times;</span>
        </div>
        <form method="POST" id="editQuizForm">
            <input type="hidden" name="update_quiz" value="1">
            <input type="hidden" name="quiz_id" id="editQuizId">
            <label>
                Category:
                <select name="category_id" id="editCategoryId" required>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Quiz Name:
                <input type="text" name="quiz_name" id="editQuizName" required>
            </label>
            <label>
                Description:
                <textarea name="description" id="editDescription"></textarea>
            </label>
            <label>
                Duration (minutes):
                <input type="number" name="duration" id="editDuration" min="1" max="60" required>
            </label>
            <button type="submit" class="btn btn-edit">Update Quiz</button>
        </form>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main');
    const quizManagementSection = document.getElementById('quizManagementSection');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });

    function showQuizManagement() {
        quizManagementSection.style.display = 'block';
    }

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function editQuiz(id, name, categoryId, description, duration) {
        document.getElementById('editQuizId').value = id;
        document.getElementById('editQuizName').value = name;
        document.getElementById('editCategoryId').value = categoryId;
        document.getElementById('editDescription').value = description;
        document.getElementById('editDuration').value = duration;
        openModal('editQuizModal');
    }

    function deleteQuiz(id) {
        if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                    <input type="hidden" name="delete_quiz" value="1">
                    <input type="hidden" name="quiz_id" value="${id}">
                `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>