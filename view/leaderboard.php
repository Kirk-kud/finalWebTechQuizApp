<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include "../db/config.php";

// Get all available quizzes for the dropdown
$quizzes_query = "SELECT id, name FROM quizzes ORDER BY name";
$quizzes_result = $conn->query($quizzes_query);

// Get selected quiz_id from POST or default to null
$selected_quiz_id = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
?>
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
        .quiz-selector {
            margin: 2rem auto;
            max-width: 800px;
            text-align: center;
        }
        .quiz-selector select {
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            min-width: 200px;
            margin-right: 1rem;
        }
        .quiz-selector button {
            padding: 0.5rem 1rem;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .quiz-selector button:hover {
            background-color: #f57c00;
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
            max-width: 800px;
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
        .rank-1 {
            background-color: rgba(255, 215, 0, 0.2) !important;
            font-weight: bold;
        }
        .rank-2 {
            background-color: rgba(192, 192, 192, 0.2) !important;
            font-weight: bold;
        }
        .rank-3 {
            background-color: rgba(205, 127, 50, 0.2) !important;
            font-weight: bold;
        }
        .empty-message {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
            table th, table td {
                padding: 8px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="page-container">
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">
                <img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png" alt="LOGO">
            </a>
        </div>
        <div class="links">
            <a href="../index.php">Home</a>
            <a href="../view/about.html">About</a>
            <?php
            if(!isset($_SESSION['user_id'])){
                echo "<a href='../view/login.php'>Login</a>";
                echo "<a href='../view/signup.php'>Sign Up</a>";
            } else {
                echo "<a href='quizzes.php'>Quizzes</a>";
                echo "<a href='leaderboard.php'>Leaderboard</a>";
                echo "<a href='profile.php'>Profile</a>";
                echo "<a href='../actions/logout.php'>Logout</a>";
            }
            ?>
        </div>
    </nav>

    <div class="content">
        <h1 style="font-weight: 300; font-size: 3.5rem; font-family: 'Inter';">Leaderboard</h1>
        <p>Welcome to the Quiz Quest Leaderboard! <br>Here you can see the top performers and track your progress.</p>

        <!-- Add quiz selector form -->
        <form method="POST" class="quiz-selector">
            <select name="quiz_id" id="quiz_id">
                <option value="">All Quizzes</option>
                <?php
                while ($quiz = $quizzes_result->fetch_assoc()) {
                    $selected = ($selected_quiz_id == $quiz['id']) ? 'selected' : '';
                    echo "<option value='" . $quiz['id'] . "' $selected>" . htmlspecialchars($quiz['name']) . "</option>";
                }
                ?>
            </select>
            <button type="submit">View Rankings</button>
        </form>

        <div class="leaderboard-section">
            <h2>Top Players</h2>
            <table>
                <thead>
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Quiz</th>
                    <th>Score</th>
                </tr>
                </thead>
                <tbody>
                <?php
                try {
                    $query = "
                        WITH RankedScores AS (
                            SELECT 
                                l.user_id, 
                                CONCAT(u.fname, ' ', u.lname) as username, 
                                q.name AS quiz_name, 
                                MAX(l.high_score) as high_score,
                                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as total_questions,
                                DENSE_RANK() OVER (ORDER BY MAX(l.high_score) DESC) as rank
                            FROM leaderboard l 
                            JOIN users u ON l.user_id = u.user_id 
                            JOIN quizzes q ON l.quiz_id = q.id 
                            " . ($selected_quiz_id ? "WHERE q.id = ?" : "") . "
                            GROUP BY l.user_id, q.id, u.fname, u.lname, q.name
                        )
                        SELECT 
                            rank, 
                            username, 
                            quiz_name, 
                            ROUND((high_score / total_questions) * 100, 1) as score_percentage
                        FROM RankedScores 
                        ORDER BY rank ASC, quiz_name ASC 
                        LIMIT 10";

                    $stmt = $conn->prepare($query);

                    if ($selected_quiz_id) {
                        $stmt->bind_param("i", $selected_quiz_id);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();
                    $hasResults = false;

                    while ($row = $result->fetch_assoc()) {
                        $hasResults = true;
                        $rankClass = '';
                        if ($row['rank'] == 1) $rankClass = 'rank-1';
                        else if ($row['rank'] == 2) $rankClass = 'rank-2';
                        else if ($row['rank'] == 3) $rankClass = 'rank-3';

                        echo "<tr class='" . $rankClass . "'>";
                        echo "<td>" . htmlspecialchars($row['rank']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quiz_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['score_percentage']) . "%</td>";
                        echo "</tr>";
                    }

                    if (!$hasResults) {
                        $message = $selected_quiz_id ?
                            "No attempts recorded for this quiz yet. Be the first to make it to the leaderboard!" :
                            "No quiz attempts recorded yet. Be the first to make it to the leaderboard!";
                        echo "<tr><td colspan='4' class='empty-message'>$message</td></tr>";
                    }

                } catch (Exception $e) {
                    echo "<tr><td colspan='4' class='empty-message'>An error occurred while loading the leaderboard. Please try again later.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>