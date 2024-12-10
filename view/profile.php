<?php
session_start();
if (!isset($_SESSION)) {
    header("Location: ../view/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
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
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            background: rgba(0,0,0,0.7);
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
        .content {
            margin-top: 100px; /* Adjust based on navbar height */
            padding: 0 5%;
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
            .content {
                margin-top: 150px; /* Adjust for mobile navbar height */
            }
        }
        .content {
            margin-top: 13rem; /* Increased from 100px to provide more clearance */
            padding: 0 5%;
            align-items: center;
            text-align: center;
        }
        @media (max-width: 768px) {
            .content {
                margin-top: 16rem; /* Adjusted for mobile view */
            }
        }
        /* Previous styles remain the same */
        .leaderboard-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
        }


    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo"><img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png"></div>
    <div class="links">
        <a href="../index.php">Home</a>
        <a href="../view/about.html">About</a>
        <?php

        if(!isset($_SESSION['user_id'])){
            echo "<a href='../view/login.php'>Login</a>";
            echo "<a href='../view/signup.php'>Sign Up</a>";
        }
        else{
            echo "<a href='leaderboard.php'>Leaderboard</a>";
            echo "<a href='profile.php'>Profile</a>";
            echo "<a href='../actions/logout.php'>Logout</a>";
        }
        ?>
    </div>
</nav>
<h1 style="text-align: center;">Profile</h1>

</body>
</html>