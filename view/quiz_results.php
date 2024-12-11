<?php
session_start();
include "../db/config.php";

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: quizzes.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$progress_id = $_GET['id'];

// Fetch quiz results and details
$query = "
    SELECT 
        up.*, 
        q.name as quiz_name,
        q.description as quiz_description,
        c.name as category_name,
        u.fname,
        u.lname,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as total_questions,
        (SELECT high_score FROM leaderboard WHERE user_id = up.user_id AND quiz_id = q.id) as high_score
    FROM user_progress up
    JOIN quizzes q ON up.quiz_id = q.id
    JOIN categories c ON q.category_id = c.id
    JOIN users u ON up.user_id = u.user_id
    WHERE up.id = ? AND up.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $progress_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz_data = $result->fetch_assoc();

if(!$quiz_data) {
    header("Location: quizzes.php");
    exit();
}

// Calculate percentage
$percentage = ($quiz_data['score'] / $quiz_data['total_questions']) * 100;

// Fetch detailed answers
$answers_query = "
    SELECT 
        q.question_text,
        q.options,
        q.correct_option,
        us.selected_option
    FROM user_sessions us
    JOIN questions q ON us.question_id = q.id
    WHERE us.user_id = ? AND us.quiz_id = ?
    ORDER BY q.id
";

$stmt = $conn->prepare($answers_query);
$stmt->bind_param("ii", $user_id, $quiz_data['quiz_id']);
$stmt->execute();
$answers_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Results - <?php echo htmlspecialchars($quiz_data['quiz_name']); ?></title>
    <style>
        /* Previous styles remain */
        .results-container {
            max-width: 800px;
            margin: 15vh auto 2rem;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .score-card {
            text-align: center;
            padding: 2rem;
            margin: 1rem 0;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .score-percentage {
            font-size: 3rem;
            font-weight: bold;
            color: #2196F3;
        }
        .score-label {
            color: #666;
            margin-top: 0.5rem;
        }
        .high-score {
            color: #4CAF50;
            font-weight: bold;
        }
        .answers-review {
            margin-top: 2rem;
        }
        .question-review {
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .correct {
            background-color: #e8f5e9;
            border-color: #4CAF50;
        }
        .incorrect {
            background-color: #ffebee;
            border-color: #f44336;
        }
        .option-list {
            margin: 1rem 0;
        }
        .option {
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-radius: 4px;
        }
        .selected {
            background-color: #e3f2fd;
        }
        .correct-answer {
            background-color: #c8e6c9;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .action-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            color: white;
        }
        .retry-button {
            background-color: #2196F3;
        }
        .back-button {
            background-color: #666;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <!-- Previous navbar content remains the same -->
</nav>

<div class="results-container">
    <h1><?php echo htmlspecialchars($quiz_data['quiz_name']); ?> Results</h1>
    <p>Category: <?php echo htmlspecialchars($quiz_data['category_name']); ?></p>

    <div class="score-card">
        <div class="score-percentage"><?php echo number_format($percentage, 1); ?>%</div>
        <div class="score-label">
            You scored <?php echo $quiz_data['score']; ?> out of <?php echo $quiz_data['total_questions']; ?> questions
        </div>
        <?php if($quiz_data['score'] === $quiz_data['high_score']): ?>
            <div class="high-score">🏆 New High Score!</div>
        <?php endif; ?>
    </div>

    <div class="answers-review">
        <h2>Detailed Review</h2>
        <?php while($answer = $answers_result->fetch_assoc()): ?>
            <?php
            $options = json_decode($answer['options'], true);
            $is_correct = $answer['selected_option'] == $answer['correct_option'];
            ?>
            <div class="question-review <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                <h3><?php echo htmlspecialchars($answer['question_text']); ?></h3>
                <div class="option-list">
                    <?php foreach($options as $index => $option): ?>
                        <div class="option <?php
                        echo $index == $answer['selected_option'] ? 'selected' : '';
                        echo $index == $answer['correct_option'] ? 'correct-answer' : '';
                        ?>">
                            <?php echo htmlspecialchars($option); ?>
                            <?php if($index == $answer['selected_option']): ?>
                                (Your Answer)
                            <?php endif; ?>
                            <?php if($index == $answer['correct_option']): ?>
                                (Correct Answer)
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="action-buttons">
        <a href="take_quiz.php?id=<?php echo $quiz_data['quiz_id']; ?>" class="action-button retry-button">Try Again</a>
        <a href="quizzes.php" class="action-button back-button">Back to Quizzes</a>
    </div>
</div>
</body>
</html>