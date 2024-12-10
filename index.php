<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Quest</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .hero {
            position: relative;
            height: 100vh;
            background: url('https://images.unsplash.com/photo-1501503069356-3c6b82a17d89?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            background: rgba(0,0,0,0.3);
            z-index: 10;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 0.5rem;
            font-weight: bold;
            transition: color 0.3s;
        }
        .navbar a:hover {
            color: #ff9800;
        }
        .navbar .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .hero-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 1rem;
        }
        .hero-text-container {
            background: rgba(0,0,0,0.5);
            border-radius: 20px;
            padding: 2rem;
            max-width: 80%;
            text-align: center;
        }
        .hero-text-container h1 {
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }
        .hero-text-container p {
            font-size: 1.2rem;
        }
        .content-section {
            padding: 2rem 5%;
            background-color: #f4f4f4;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }
            .navbar .links {
                margin-top: 1rem;
            }
            .navbar a {
                margin: 0 0.25rem;
            }
            .hero-text-container {
                max-width: 95%;
                padding: 1rem;
            }
            .hero-text-container h1 {
                font-size: 2rem;
            }
            .hero-text-container p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="page-container">
    <div class="hero">
        <nav class="navbar">
            <div class="logo"><img style="width:6rem; height: 6rem;" src="assets/images/quiz_quest_logo_black.png"></div>
            <div class="links">
                <a href="index.php">Home</a>
                <a href="view/about.html">About</a>
                <?php
                session_start();
                if(!isset($_SESSION['user_id'])){
                    echo "<a href='view/login.php'>Login</a>";
                    echo "<a href='view/signup.php'>Sign Up</a>";
                }
                else{
                    echo "<a href='view/leaderboard.php'>Leaderboard</a>";
                    echo "<a href='view/profile.php'>Profile</a>";
                    echo "<a href='actions/logout.php'>Logout</a>";
                }
                ?>

            </div>
        </nav>
        <div class="hero-content">
            <div class="hero-text-container">
                <h1>Welcome to Quiz Quest</h1>
                <p>Explore a new dimension of learning.</p>
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2>About Our Mission</h2>
        <p>Quiz Quest is dedicated to transforming learning through interactive and engaging quizzes. Our platform helps students, professionals, and lifelong learners test their knowledge and grow.</p>
    </div>

    <div class="content-section">
        <h2>Our Services</h2>
        <p>We offer a wide range of quizzes across multiple disciplines, adaptive learning paths, and personalized feedback to help you achieve your learning goals.</p>
    </div>
</div>
</body>
</html>