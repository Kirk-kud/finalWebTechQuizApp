<?php
session_start();
include "../db/config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

// Fetch all categories with their quizzes
$query = "
    SELECT c.*, q.id as quiz_id, q.name as quiz_name, q.description as quiz_description 
    FROM categories c
    LEFT JOIN quizzes q ON c.id = q.category_id
    ORDER BY c.name, q.name
";
$result = $conn->query($query);

// Organize data by categories
$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categoryId = $row['id'];
        if (!isset($categories[$categoryId])) {
            $categories[$categoryId] = [
                'name' => $row['name'],
                'description' => $row['description'],
                'quizzes' => []
            ];
        }
        if ($row['quiz_id']) {
            $categories[$categoryId]['quizzes'][] = [
                'id' => $row['quiz_id'],
                'name' => $row['quiz_name'],
                'description' => $row['quiz_description']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quizzes</title>
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
            background-color: #f4f4f4;
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
        .page-container {
            max-width: 1200px;
            margin: 15vh auto 2rem;
            padding: 2rem;
        }
        .categories-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .category-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        .category-title {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ff9800;
        }
        .category-description {
            color: #666;
            margin-bottom: 1rem;
        }
        .quiz-list {
            list-style: none;
        }
        .quiz-item {
            padding: 1rem;
            border: 1px solid #eee;
            margin-bottom: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .quiz-item:hover {
            background-color: #f8f8f8;
            transform: translateX(5px);
        }
        .quiz-title {
            color: #2196F3;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .quiz-description {
            color: #777;
            font-size: 0.9rem;
        }
        .start-quiz-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 0.5rem;
            transition: background-color 0.3s;
        }
        .start-quiz-btn:hover {
            background-color: #45a049;
        }
        .no-quizzes {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo"><a href="../index.php"><img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png"></a></div>
    <div class="links">
        <a href="../index.php">Home</a>
        <a href="../view/about.html">About</a>
        <a href="../view/quizzes.php">Quizzes</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Profile</a>
        <a href="../actions/logout.php">Logout</a>
    </div>
</nav>

<div class="page-container">
    <br><br><br>
    <h1>Available Quizzes</h1>

    <div class="categories-container">
        <?php if (empty($categories)): ?>
            <p>No categories or quizzes available at the moment.</p>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <h2 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h2>
                    <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>

                    <?php if (empty($category['quizzes'])): ?>
                        <p class="no-quizzes">No quizzes available in this category.</p>
                    <?php else: ?>
                        <ul class="quiz-list">
                            <?php foreach ($category['quizzes'] as $quiz): ?>
                                <li class="quiz-item">
                                    <div class="quiz-title"><?php echo htmlspecialchars($quiz['name']); ?></div>
                                    <div class="quiz-description"><?php echo htmlspecialchars($quiz['description']); ?></div>
                                    <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="start-quiz-btn">Start Quiz</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>