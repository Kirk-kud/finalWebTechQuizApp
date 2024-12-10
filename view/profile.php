<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
}
include "../db/config.php";
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
            background-color: #f5f5f5;
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
            background: rgba(0,0,0,0.5);
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
        .profile-container {
            max-width: 1200px;
            margin: 120px auto 40px;
            padding: 20px;
        }
        .profile-header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #ff9800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }
        .profile-info {
            flex-grow: 1;
        }
        .profile-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .profile-email {
            color: #666;
            margin-bottom: 15px;
        }
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #ff9800;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .activity-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .activity-list {
            list-style: none;
        }
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .quiz-name {
            font-weight: bold;
        }
        .quiz-score {
            color: #ff9800;
            font-weight: bold;
        }
        .quiz-date {
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            .profile-container {
                margin-top: 160px;
            }
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
<?php
    // Fetch user data
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();

    // Fetch user statistics
    $stats_query = "SELECT 
            COUNT(DISTINCT quiz_id) as total_quizzes,
            MAX(score) as highest_score,
            ROUND(AVG(score), 1) as average_score
            FROM user_progress 
            WHERE user_id = ?";
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats_result = $stmt->get_result();
    $stats_data = $stats_result->fetch_assoc();

    // Fetch recent activity
    $activity_query = "SELECT 
            q.name as quiz_name, 
            up.score, 
            up.completed_at
            FROM user_progress up 
            JOIN quizzes q ON up.quiz_id = q.id 
            WHERE up.user_id = ? 
            ORDER BY up.completed_at DESC 
            LIMIT 5";
    $stmt = $conn->prepare($activity_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $activity_result = $stmt->get_result();
?>

<nav class="navbar">
    <div class="logo"><img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png"></div>
    <div class="links">
        <a href="../index.php">Home</a>
        <a href="../view/about.html">About</a>
        <a href="../view/quizzes.php">Quizzes</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Profile</a>
        <a href="../actions/logout.php">Logout</a>
    </div>
</nav>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user_data['fname'], 0, 1)); ?>
        </div>
        <div class="profile-info">
            <div class="profile-name"><?php echo htmlspecialchars($user_data['fname'] . ' ' . $user_data['lname']); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></div>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats_data['total_quizzes'] ?? 0; ?></div>
            <div class="stat-label">Quizzes Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats_data['highest_score'] ?? 0; ?>%</div>
            <div class="stat-label">Highest Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats_data['average_score'] ?? 0; ?>%</div>
            <div class="stat-label">Average Score</div>
        </div>
    </div>

    <div class="recent-activity">
        <div class="activity-header">Recent Activity</div>
        <ul class="activity-list">
            <?php
            if ($activity_result->num_rows > 0) {
                while ($activity = $activity_result->fetch_assoc()) {
                    $formatted_date = date('M d, Y', strtotime($activity['completed_at']));
                    echo "<li class='activity-item'>
                                <div>
                                    <span class='quiz-name'>" . htmlspecialchars($activity['quiz_name']) . "</span>
                                    <br>
                                    <span class='quiz-date'>" . $formatted_date . "</span>
                                </div>
                                <span class='quiz-score'>" . $activity['score'] . "%</span>
                              </li>";
                }
            } else {
                echo "<li class='activity-item'>No quizzes completed yet</li>";
            }
            ?>
        </ul>
    </div>
</div>

<?php
// Close database connection
$stmt->close();
$conn->close();
?>
</body>
</html>