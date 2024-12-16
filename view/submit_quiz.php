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
$correct_answers = 0;
$total_questions = 0;
$answers = [];

// Starting the submission transaction
$conn->begin_transaction();

try {
    // Getting all submitted answers
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = substr($key, 9); // Remove 'question_' prefix

            // Converting zero-based index from frontend to one-based for database
            // database has non-zero-based indices
            $selected_option = intval($value) + 1;

            // Getting quiz_id from the first question if not set
            if ($quiz_id === null) {
                $quiz_query = "SELECT quiz_id FROM questions WHERE id = ?";
                $stmt = $conn->prepare($quiz_query);
                $stmt->bind_param("i", $question_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $quiz_data = $result->fetch_assoc();
                $quiz_id = $quiz_data['quiz_id'];
            }

            // Storing the  answer with adjusted indexing
            $answers[$question_id] = $selected_option;
        }
    }

    // COde to verify all questions are answered
    $questions_query = "SELECT id, correct_option FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($questions_query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();

    $total_questions = $questions_result->num_rows;
    if (count($answers) !== $total_questions) {
        throw new Exception('All questions must be answered');
    }

    // Calculating the  score - no need to adjust indexes here since both values are now 1-based
    while ($question = $questions_result->fetch_assoc()) {
        if (isset($answers[$question['id']]) &&
            $answers[$question['id']] == $question['correct_option']) {
            $correct_answers++;
        }
    }

    // percentage score (rounded to nearest integer)
    $score = $correct_answers;

    // user progress entry
    $progress_query = "INSERT INTO user_progress (user_id, quiz_id, score) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($progress_query);
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    $stmt->execute();
    $progress_id = $conn->insert_id;

    // storing individual answers in user_sessions (answers are already 1-based)
    foreach ($answers as $question_id => $selected_option) {
        $session_query = "INSERT INTO user_sessions (user_id, quiz_id, question_id, selected_option) 
                         VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($session_query);
        $stmt->bind_param("iiii", $user_id, $quiz_id, $question_id, $selected_option);
        $stmt->execute();
    }

    // update the leaderboard if this is a high score
    $leaderboard_query = "
    INSERT INTO leaderboard (user_id, quiz_id, high_score)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    high_score = GREATEST(high_score, VALUES(high_score))";

    $stmt = $conn->prepare($leaderboard_query);
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    $stmt->execute();
    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'session_id' => $progress_id,
        'score' => $score,
        'total' => $total_questions,
        'correct' => $correct_answers
    ]);

} catch (Exception $e) {
    // Rollback submission transaction on error
    $conn->rollback();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
$conn->close();
?>