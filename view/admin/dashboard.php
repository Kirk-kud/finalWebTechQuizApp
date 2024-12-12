<?php
session_start();

if (!isset($_SESSION['user_id']) && ($_SESSION['role'] != 1)){
    header("Location: ../../index.php");
    exit();
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
    <h2>Welcome to Dashboard</h2>
    <p>Your content goes here...</p>
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