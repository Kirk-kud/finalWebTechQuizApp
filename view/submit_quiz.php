<?php
session_start();
include "../db/config.php";

if (!isset($_SESSION['user_id']) || !isset($_POST)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = null;
$score = 0;
$total_questions = 0;
$answers = [];

// Start transaction
$conn->begin_transaction();

try {
    // Get all submitted answers
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = substr($key, 9); // Remove 'question_' prefix

            // Get quiz_id from the first question if not set
            if ($quiz_id === null) {
                $quiz_query = "SELECT quiz_id FROM questions WHERE id = ?";
                $stmt = $conn->prepare($quiz_query);
                $stmt->bind_param("i", $question_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $quiz_data = $result->fetch_assoc();
                $quiz_id = $quiz_data['quiz_id'];
            }

            // Store answer
            $answers[$question_id] = $value;
        }
    }

    // Verify all questions are answered
    $questions_query = "SELECT id, correct_option FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($questions_query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();

    $total_questions = $questions_result->num_rows;
    if (count($answers) !== $total_questions) {
        throw new Exception('All questions must be answered');
    }

    // Calculate score
    while ($question = $questions_result->fetch_assoc()) {
        if (isset($answers[$question['id']]) &&
            $answers[$question['id']] == $question['correct_option']) {
            $score++;
        }
    }

    // Create user progress entry
    $progress_query = "INSERT INTO user_progress (user_id, quiz_id, score) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($progress_query);
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    $stmt->execute();
    $progress_id = $conn->insert_id;

    // Store individual answers in user_sessions
    foreach ($answers as $question_id => $selected_option) {
        $session_query = "INSERT INTO user_sessions (user_id, quiz_id, question_id, selected_option) 
                         VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($session_query);
        $stmt->bind_param("iiii", $user_id, $quiz_id, $question_id, $selected_option);
        $stmt->execute();
    }

    // Update leaderboard if this is a high score
    $leaderboard_query = "INSERT INTO leaderboard (user_id, quiz_id, high_score)
                         VALUES (?, ?, ?)
                         ON DUPLICATE KEY UPDATE high_score = 
                         CASE WHEN VALUES(high_score) > high_score 
                         THEN VALUES(high_score) ELSE high_score END";
    $stmt = $conn->prepare($leaderboard_query);
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'session_id' => $progress_id,
        'score' => $score,
        'total' => $total_questions
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>