<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Leaderboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #ff9800 0%, #ff9800 50%, white 50%, white 100%);
            background-attachment: fixed;
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
            margin-top: 13rem;
            padding: 2rem 5%;
            align-items: center;
            text-align: center;
        }
        .content h1 {
            color: #333;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .content p {
            color: #333;
            background: rgba(255, 255, 255, 0.9);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .leaderboard-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
        }
        .leaderboard-section h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }
        table {
            width: 80%;
            max-width: 600px;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table th {
            background-color: #ff9800;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }
        table td {
            border-bottom: 1px solid #eee;
            padding: 12px;
            text-align: center;
        }
        table tr:nth-child(even) {
            background-color: rgba(255, 152, 0, 0.1);
        }
        table tr:hover {
            background-color: rgba(255, 152, 0, 0.2);
            transition: background-color 0.3s ease;
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
                margin-top: 16rem;
            }
            table {
                width: 95%;
            }
        }
    </style>
</head>
<body>
<div class="page-container">
    <nav class="navbar">
        <div class="logo"><img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png"></div>
        <div class="links">
            <a href="../index.php">Home</a>
            <a href="../view/about.html">About</a>
            <?php
            session_start();
            if(!isset($_SESSION['user_id'])){
                echo "<a href='../view/login.php'>Login</a>";
                echo "<a href='../view/signup.php'>Sign Up</a>";
            }
            else{
                echo "<a href='quizzes.php'>Quizzes</a>";
                echo "<a href='leaderboard.php'>Leaderboard</a>";
                echo "<a href='profile.php'>Profile</a>";
                echo "<a href='../actions/logout.php'>Logout</a>";
            }
            ?>
        </div>
    </nav>

    <div class="content">
        <h1>Leaderboard</h1>
        <p>Welcome to the Quiz Quest Leaderboard! <br>Here you can see the top performers and track your progress.</p>

        <div class="leaderboard-section">
            <h2>Top Players</h2>
            <table>
                <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Total Points</th>
                </tr>
                </thead>
                <?php
                if (!isset($_SESSION['user_id'])){
                    header("Location: ../index.php");
                    exit();
                }
                include "../db/config.php";

                try {
                    $stmt = $conn->prepare("SELECT l.id, u.fname, u.lname, q.name AS quiz_name, l.high_score, DENSE_RANK() OVER (ORDER BY l.high_score DESC) as rank FROM leaderboard l JOIN users u ON l.user_id = u.user_id JOIN quizzes q ON l.quiz_id = q.id ORDER BY l.high_score DESC LIMIT 10");
                    $stmt->execute();

                    $rank = 1;

                    while ($row = $stmt->fetch()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($rank++) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['high_score']) . "</td>";
                        echo "</tr>";
                    }
                } catch(Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>