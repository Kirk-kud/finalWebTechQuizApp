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
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $query = "INSERT INTO quizzes (category_id, name, description, duration_minutes, is_active) VALUES ('$category_id', '$name', '$description', '$duration', '$is_active')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_quizzes.php?success=Quiz created");
        } else {
            header("Location: manage_quizzes.php?error=Couldn't create quiz");
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
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $query = "UPDATE quizzes SET category_id = '$category_id', name = '$name', description = '$description', duration_minutes = '$duration', is_active = '$is_active' WHERE id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_quizzes.php?success=Quiz updated");
        } else {
            header("Location: manage_quizzes.php?error=Couldn't update quiz");
        }
        exit();
    }

    // Delete quiz
    if (isset($_POST['delete_quiz'])) {
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);
        $query = "DELETE FROM quizzes WHERE id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_quizzes.php?success=Quiz deleted successfully");
        } else {
            header("Location: manage_quizzes.php?error=Failed to delete quiz");
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
    <title>Quiz Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-orange: #FF8C00;
            --secondary-orange: #FFA500;
            --dark-gray: #333333;
            --light-gray: #f5f5f5;
            --border-color: #e0e0e0;
            --text-muted: #666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            background-color: var(--light-gray);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            flex: 1;
            text-align: center;
            margin-right: 3rem;
            font-weight: 500;
        }

        .menu-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 1rem;
            width: 3rem;
            transition: transform 0.2s ease;
        }

        .menu-btn:hover {
            transform: scale(1.1);
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
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
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
            transition: 0.2s;
        }

        .sidebar a:hover {
            background-color: var(--secondary-orange);
            padding-left: 2rem;
        }

        .sidebar a i {
            width: 1.5rem;
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px;
            padding: 5rem 2rem 2rem;
            transition: 0.3s;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Table Styles */
        .quiz-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-top: 1rem;
        }

        .quiz-table th {
            font-weight: 500;
            color: var(--text-muted);
            border: none;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .quiz-table td {
            background: white;
            padding: 16px;
            border: none;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .quiz-table tr td:first-child {
            border-left: 1px solid var(--border-color);
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .quiz-table tr td:last-child {
            border-right: 1px solid var(--border-color);
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .quiz-table tr:hover td {
            background-color: #f8f9fa;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-gray);
        }

        .modal-close {
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: var(--dark-gray);
        }

        .modal-body {
            padding: 24px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-orange);
            color: white;
            margin-top: 1px;
        }

        .btn-primary:hover {
            background-color: var(--secondary-orange);
        }

        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .actions-group {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        /* Alert Styles */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 5rem 1rem 1rem;
            }

            .quiz-table {
                display: block;
                overflow-x: auto;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="header">
    <button id="menuToggle" class="menu-btn">☰</button>
    <h1>Quiz Management</h1>
</header>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <a href="../../index.php"><i class="fas fa-home"></i>Home</a>
    <a href="dashboard.php"><i class="fas fa-chart-bar"></i>Dashboard</a>
    <a href="#"><i class="fas fa-question-circle"></i>Quizzes</a>
    <a href="users.php"><i class="fas fa-users"></i>Users</a>
</nav>

<!-- Main Content -->
<main class="main-content" id="main">
    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="actions-group" style="margin-bottom: 24px;">
            <button class="btn btn-primary" onclick="openModal('createQuizModal')">
                <i class="fas fa-plus"></i> Create New Quiz
            </button>
        </div>

        <table class="quiz-table">
            <thead>
            <tr>
                <th>Quiz Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Status</th>
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
                    <span class="status-badge <?php echo $quiz['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $quiz['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                    </td>
                    <td>
                        <button class="btn btn-edit" onclick="editQuiz(
                        <?php echo $quiz['id']; ?>,
                                '<?php echo htmlspecialchars($quiz['name']); ?>',
                                '<?php echo htmlspecialchars($quiz['category_id']); ?>',
                                '<?php echo htmlspecialchars($quiz['description'] ?? ''); ?>',
                        <?php echo $quiz['duration_minutes']; ?>,
                        <?php echo $quiz['is_active']; ?>
                                )">Edit</button>
                        <button class="btn btn-delete" onclick="deleteQuiz(<?php echo $quiz['id']; ?>)">Delete</button>
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

<div id="createQuizModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Quiz</h3>
            <span class="modal-close" onclick="closeModal('createQuizModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="create_quiz" value="1">
                <div class="form-group">
                    <label class="form-label">Category:</label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quiz Name:</label>
                    <input type="text" name="quiz_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes):</label>
                    <input type="number" name="duration" class="form-control" min="1" max="60" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active Quiz
                    </label>
                </div>
                <div class="actions-group">
                    <button type="button" class="btn btn-primary" onclick="closeModal('createQuizModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editQuizModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Quiz</h3>
            <span class="modal-close" onclick="closeModal('editQuizModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editQuizForm">
                <input type="hidden" name="update_quiz" value="1">
                <input type="hidden" name="quiz_id" id="editQuizId">
                <div class="form-group">
                    <label class="form-label">Category:</label>
                    <select name="category_id" id="editCategoryId" class="form-control" required>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quiz Name:</label>
                    <input type="text" name="quiz_name" id="editQuizName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description:</label>
                    <textarea name="description" id="editDescription" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes):</label>
                    <input type="number" name="duration" id="editDuration" class="form-control" min="1" max="60" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1">
                        Active Quiz
                    </label>
                </div>
                <div class="actions-group">
                    <button type="button" class="btn btn-primary" onclick="closeModal('editQuizModal')">Cancel</button>
                    <button type="submit" class="btn btn-edit">Update Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Sidebar toggle functionality
    const menuBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });

    // Modal functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'block';
        // Trigger reflow
        modal.offsetHeight;
        modal.classList.add('show');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
        // Wait for animation to complete before hiding
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function editQuiz(id, name, categoryId, description, duration, isActive) {
        document.getElementById('editQuizId').value = id;
        document.getElementById('editQuizName').value = name;
        document.getElementById('editCategoryId').value = categoryId;
        document.getElementById('editDescription').value = description;
        document.getElementById('editDuration').value = duration;
        document.getElementById('editIsActive').checked = isActive == 1;
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

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Handle form submissions
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent accidental form submissions on enter key
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            });
        });

        // Add error handling for form submissions
        const createForm = document.querySelector('#createQuizModal form');
        const editForm = document.querySelector('#editQuizForm');

        [createForm, editForm].forEach(form => {
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nameInput = form.querySelector('[name="quiz_name"]');
                    const durationInput = form.querySelector('[name="duration"]');

                    if (nameInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please enter a quiz name');
                        return;
                    }

                    const duration = parseInt(durationInput.value);
                    if (isNaN(duration) || duration < 1 || duration > 60) {
                        e.preventDefault();
                        alert('Duration must be between 1 and 60 minutes');
                        return;
                    }
                });
            }
        });
    });
</script>
</body>
</html>
