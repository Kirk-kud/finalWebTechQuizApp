<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Quest</title>
    <style>
        :root {
            --primary-color: rgba(0, 0, 0, 0.82);
            --secondary-color: #FFA500;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background-color: var(--light-color);
        }
        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .hero {
            position: relative;
            height: 100vh;
            background:
                    url('assets/images/home.jpg') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            color: white;
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
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .navbar a:hover {
            background-color: rgba(255,255,255,0.2);
            transform: scale(1.05);
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
            padding: 1rem;
        }
        .hero-text-container {
            background: rgba(0,0,0,0.6);
            border-radius: 20px;
            padding: 3rem 2rem;
            max-width: 80%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .hero-text-container h1 {
            margin-bottom: 1rem;
            font-size: 3rem;
            color: var(--secondary-color);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .hero-text-container p {
            font-size: 1.5rem;
            color: var(--light-color);
        }
        .content-section {
            padding: 3rem 5%;
            background-color: white;
            margin: 1rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .content-section h2 {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .category-card {
            background: var(--light-color);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-top: 5px solid var(--primary-color);
        }
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        .category-card h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-size: 1.3rem;
        }
        .why-choose-section ul {
            list-style-type: none;
        }
        .why-choose-section ul li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--light-color);
            display: flex;
            align-items: center;
        }
        .why-choose-section ul li:before {
            content: '✓';
            color: var(--secondary-color);
            margin-right: 1rem;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }
            .navbar .links {
                margin-top: 1rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .navbar a {
                margin: 0.25rem;
            }
            .hero-text-container {
                max-width: 95%;
                padding: 2rem 1rem;
            }
            .hero-text-container h1 {
                font-size: 2.5rem;
            }
            .hero-text-container p {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
<div class="page-container">
    <div class="hero">
        <nav class="navbar">
            <div class="logo"><a href="index.php"><img style="width:6rem; height: 6rem;" src="assets/images/quiz_quest_logo_white.png"></a></div>
            <div class="links">
                <a href="index.php">Home</a>
                <a href="view/about.html">About</a>
                <?php
                if(!isset($_SESSION['user_id'])){
                    echo "<a href='view/login.php'>Login</a>";
                    echo "<a href='view/signup.php'>Sign Up</a>";
                }
                else{
                    if ($_SESSION['role'] != '1'){
                        echo "<a href='view/quizzes.php'>Quizzes</a>";
                    }
                    echo "<a href='view/leaderboard.php'>Leaderboard</a>";
                    echo "<a href='view/profile.php'>Profile</a>";
                    if ($_SESSION['role'] == '1'){
                        echo "<a href='view/admin/dashboard.php'>Dashboard</a>";
                    }
                    echo "<a href='actions/logout.php'>Logout</a>";
                }
                ?>
            </div>
        </nav>
        <div class="hero-content">
            <div class="hero-text-container">
                <h1>Welcome to Quiz Quest</h1>
                <p>Explore, Learn, and Challenge Yourself with Interactive Quizzes!</p>
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2>Our Mission</h2>
        <p>Quiz Quest is dedicated to transforming learning through interactive and engaging quizzes. Our platform helps students, professionals, and lifelong learners test their knowledge, discover new insights, and grow intellectually across various disciplines.</p>
    </div>

    <div class="content-section">
        <h2>Explore Our Quiz Categories</h2>
        <div class="categories-grid">
            <div class="category-card">
                <h3>Mathematics</h3>
                <p>Challenge your mathematical skills with quizzes ranging from basic algebra to advanced problem-solving. Test your logical reasoning and computational abilities!</p>
            </div>
            <div class="category-card">
                <h3>Science</h3>
                <p>Dive into the fascinating world of science. Explore physics, chemistry, biology, and more. Discover the wonders of the natural world through our comprehensive quizzes.</p>
            </div>
            <div class="category-card">
                <h3>Computer Programming</h3>
                <p>Sharpen your coding skills with our programming quizzes. From Python basics to JavaScript fundamentals, enhance your technical knowledge and problem-solving skills.</p>
            </div>
            <div class="category-card">
                <h3>History</h3>
                <p>Travel through time and explore different historical periods. Learn about ancient civilizations, world wars, and significant events that shaped our world.</p>
            </div>
            <div class="category-card">
                <h3>Geography</h3>
                <p>Test your knowledge of world geography. Explore capital cities, natural wonders, countries, and international boundaries. Become a global citizen!</p>
            </div>
        </div>
    </div>

    <div class="content-section why-choose-section">
        <h2>Why Choose Quiz Quest?</h2>
        <ul>
            <li>Wide Range of Categories: From science to history, we cover multiple disciplines</li>
            <li>Adaptive Learning: Our quizzes cater to different skill levels</li>
            <li>Instant Feedback: Learn from your mistakes in real-time</li>
            <li>Leaderboard: Track your progress and compete with other learners</li>
            <li>Free Access: Most quizzes are completely free!</li>
        </ul>
    </div>
</div>
</body>
</html>