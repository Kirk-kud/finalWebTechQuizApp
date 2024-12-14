<?php
session_start();

// Include database connection
include '../../db/config.php';

// Redirect if not an admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1)) {
    header("Location: ../../index.php");
    exit();
}

// Fetch system-wide statistics
$stats = [];

// Total Users
$userQuery = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN role = 2 THEN 1 ELSE 0 END) as regular_users,
    SUM(CASE WHEN role = 1 THEN 1 ELSE 0 END) as admin_users,
    DATE_FORMAT(MIN(registration_date), '%Y-%m-%d') as first_user_date
FROM users";
$userResult = mysqli_query($conn, $userQuery);
$stats['users'] = mysqli_fetch_assoc($userResult);

// Quiz Statistics
$quizQuery = "SELECT 
    COUNT(*) as total_quizzes,
    (SELECT COUNT(*) FROM categories) as total_categories,
    (SELECT COUNT(*) FROM questions) as total_questions
FROM quizzes";
$quizResult = mysqli_query($conn, $quizQuery);
$stats['quizzes'] = mysqli_fetch_assoc($quizResult);

// User Progress and Leaderboard
$progressQuery = "SELECT 
    COUNT(DISTINCT user_id) as users_completed_quizzes,
    COUNT(*) as total_quiz_attempts,
    MAX(score) as highest_score,
    ROUND(AVG(score), 2) as average_score
FROM user_progress";
$progressResult = mysqli_query($conn, $progressQuery);
$stats['progress'] = mysqli_fetch_assoc($progressResult);

// Most Popular Quizzes
$popularQuizQuery = "SELECT 
    q.name, 
    q.category_id,
    c.name as category_name,
    COUNT(up.quiz_id) as attempt_count
FROM quizzes q
JOIN categories c ON q.category_id = c.id
LEFT JOIN user_progress up ON q.id = up.quiz_id
GROUP BY q.id
ORDER BY attempt_count DESC
LIMIT 5";
$popularQuizResult = mysqli_query($conn, $popularQuizQuery);
$stats['popular_quizzes'] = mysqli_fetch_all($popularQuizResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        <?php //include 'dashboard.php'; // Reuse existing styles ?>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: var(--primary-orange);
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--primary-orange);
            padding-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--dark-gray);
        }

        .stat-description {
            color: #666;
            margin-top: 0.5rem;
        }

        .popular-quizzes {
            margin-top: 2rem;
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .popular-quiz-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .popular-quiz-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="header">
    <button id="menuToggle" class="menu-btn">☰</button>
    <h1>Administrator Dashboard</h1>
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
    <h2 style="text-align: center;">System-Wide Statistics</h2>

    <div class="stats-grid">
        <!-- User Statistics -->
        <div class="stat-card">
            <h3><i class="fas fa-users"></i> User Statistics</h3>
            <div class="stat-value"><?php echo $stats['users']['total_users']; ?></div>
            <div class="stat-description">Total Users</div>
            <p>
                Regular Users: <?php echo $stats['users']['regular_users']; ?><br>
                Admin Users: <?php echo $stats['users']['admin_users']; ?><br>
                First User Registered: <?php echo $stats['users']['first_user_date']; ?>
            </p>
        </div>

        <!-- Quiz Statistics -->
        <div class="stat-card">
            <h3><i class="fas fa-question-circle"></i> Quiz Overview</h3>
            <div class="stat-value"><?php echo $stats['quizzes']['total_quizzes']; ?></div>
            <div class="stat-description">Total Quizzes</div>
            <p>
                Categories: <?php echo $stats['quizzes']['total_categories']; ?><br>
                Total Questions: <?php echo $stats['quizzes']['total_questions']; ?>
            </p>
        </div>

        <!-- User Progress -->
        <div class="stat-card">
            <h3><i class="fas fa-chart-line"></i> User Progress</h3>
            <div class="stat-value"><?php echo $stats['progress']['users_completed_quizzes']; ?></div>
            <div class="stat-description">Users Completed Quizzes</div>
            <p>
                Total Quiz Attempts: <?php echo $stats['progress']['total_quiz_attempts']; ?><br>
                Highest Score: <?php echo $stats['progress']['highest_score']; ?><br>
                Average Score: <?php echo $stats['progress']['average_score']; ?>
            </p>
        </div>
    </div>

    <!-- Most Popular Quizzes -->
    <div class="popular-quizzes">
        <h3><i class="fas fa-trophy"></i> Most Popular Quizzes</h3>
        <?php foreach($stats['popular_quizzes'] as $quiz): ?>
            <div class="popular-quiz-item">
                <span><?php echo htmlspecialchars($quiz['name']); ?>
                    <small>(<?php echo htmlspecialchars($quiz['category_name']); ?>)</small>
                </span>
                <strong><?php echo $quiz['attempt_count']; ?> Attempts</strong>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    const menuBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
</script>
</body>
</html>