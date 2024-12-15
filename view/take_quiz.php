<?php
session_start();
include "../db/config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: quizzes.php");
    exit();
}

$quiz_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check for existing attempts
$attempts_query = "SELECT COUNT(*) as attempt_count FROM user_progress WHERE user_id = ? AND quiz_id = ?";
$stmt = $conn->prepare($attempts_query);
$stmt->bind_param("ii", $user_id, $quiz_id);
$stmt->execute();
$attempts_result = $stmt->get_result();
$attempts = $attempts_result->fetch_assoc();
$has_previous_attempt = $attempts['attempt_count'] > 0;

// If confirmed retake, clear previous attempts
if(isset($_GET['confirmed']) && $_GET['confirmed'] == 'true' && $has_previous_attempt) {
    $conn->begin_transaction();
    try {
        // Delete from user_sessions
        $delete_sessions = "DELETE FROM user_sessions WHERE user_id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_sessions);
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();

        // Delete from user_progress
        $delete_progress = "DELETE FROM user_progress WHERE user_id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_progress);
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error clearing previous attempts: " . $e->getMessage());
    }
}

// Fetch quiz details including duration
$quiz_query = "SELECT q.*, c.name as category_name 
               FROM quizzes q 
               JOIN categories c ON q.category_id = c.id 
               WHERE q.id = ?";
$stmt = $conn->prepare($quiz_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz_result = $stmt->get_result();
$quiz = $quiz_result->fetch_assoc();

if(!$quiz) {
    header("Location: quizzes.php");
    exit();
}

// If there's a previous attempt and not confirmed, show confirmation dialog
if($has_previous_attempt && !isset($_GET['confirmed'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Retake Quiz - <?php echo htmlspecialchars($quiz['name']); ?></title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .confirmation-container {
                max-width: 500px;
                margin: 15vh auto;
                padding: 2rem;
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                text-align: center;
            }
            .btn {
                display: inline-block;
                padding: 0.75rem 1.5rem;
                margin: 0.5rem;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                color: white;
                transition: background-color 0.3s;
            }
            .btn-confirm {
                background-color: #4CAF50;
            }
            .btn-confirm:hover {
                background-color: #45a049;
            }
            .btn-cancel {
                background-color: #f44336;
            }
            .btn-cancel:hover {
                background-color: #da190b;
            }
        </style>
    </head>
    <body>
    <div class="confirmation-container">
        <h2>Retake Quiz?</h2>
        <p>You have already attempted this quiz. Taking it again will overwrite your previous score.</p>
        <p>Do you want to continue?</p>
        <div>
            <a href="take_quiz.php?id=<?php echo $quiz_id; ?>&confirmed=true" class="btn btn-confirm">Yes, Retake Quiz</a>
            <a href="quizzes.php" class="btn btn-cancel">No, Go Back</a>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

if(isset($_GET['confirmed']) && $_GET['confirmed'] == 'true' && $has_previous_attempt) {
    $conn->begin_transaction();
    try {
        // Delete from user_sessions
        $delete_sessions = "DELETE FROM user_sessions WHERE user_id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_sessions);
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();

        // Delete from user_progress
        $delete_progress = "DELETE FROM user_progress WHERE user_id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_progress);
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();

        // Delete from leaderboard - only if we want to completely reset
        $delete_leaderboard = "DELETE FROM leaderboard WHERE user_id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_leaderboard);
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error clearing previous attempts: " . $e->getMessage());
    }
}

// Fetch questions for this quiz
$questions_query = "SELECT * FROM questions WHERE quiz_id = ?";
$stmt = $conn->prepare($questions_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$questions = [];
while($question = $questions_result->fetch_assoc()) {
    $questions[] = $question;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($quiz['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
        .quiz-container {
            max-width: 800px;
            margin: 15vh auto 2rem;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .question {
            display: none;
            margin-bottom: 2rem;
        }
        .question.active {
            display: block;
        }
        .options {
            margin: 1rem 0;
        }
        .option {
            display: block;
            padding: 1rem;
            margin: 0.5rem 0;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .option:hover {
            background-color: #f0f0f0;
        }
        .option.selected {
            border-color: #2196F3;
            background-color: #e3f2fd;
        }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        .nav-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        #prevBtn {
            background-color: #666;
            color: white;
        }
        #prevBtn:hover {
            background-color: #555;
        }
        #nextBtn {
            background-color: #4CAF50;
            color: white;
        }
        #nextBtn:hover {
            background-color: #45a049;
        }
        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #ddd;
            margin: 1rem 0;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress {
            height: 100%;
            background-color: #4CAF50;
            border-radius: 5px;
            transition: width 0.3s ease;
        }
        .timer-container {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        .timer.warning {
            color: #ff9800;
        }
        .timer.danger {
            color: #f44336;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
<!--<nav class="navbar">-->
<!--    <div class="logo">-->
<!--        <a href="../index.php">-->
<!--            <img style="width:6rem; height: 6rem;" src="../assets/images/quiz_quest_logo_white.png" alt="Quiz Quest Logo">-->
<!--        </a>-->
<!--    </div>-->
<!--    <div class="links">-->
<!--        <a href="../index.php">Home</a>-->
<!--        <a href="../view/about.html">About</a>-->
<!--        <a href="../view/quizzes.php">Quizzes</a>-->
<!--        <a href="leaderboard.php">Leaderboard</a>-->
<!--        <a href="profile.php">Profile</a>-->
<!--        <a href="../actions/logout.php">Logout</a>-->
<!--    </div>-->
<!--</nav>-->

<div class="timer-container">
    Time Remaining: <span class="timer" id="timer"></span>
</div>

<div class="quiz-container">
    <h1><?php echo htmlspecialchars($quiz['name']); ?></h1>
    <p>Category: <?php echo htmlspecialchars($quiz['category_name']); ?></p>
    <p>Duration: <?php echo htmlspecialchars($quiz['duration_minutes']); ?> minutes</p>

    <div class="progress-bar">
        <div class="progress" style="width: 0%"></div>
    </div>

    <form id="quizForm">
        <?php foreach($questions as $index => $question): ?>
            <div class="question <?php echo $index === 0 ? 'active' : ''; ?>" data-question="<?php echo $index; ?>">
                <h3>Question <?php echo $index + 1; ?></h3>
                <p><?php echo htmlspecialchars($question['question_text']); ?></p>

                <div class="options">
                    <?php
                    $options = json_decode($question['options'], true);
                    foreach($options as $optionIndex => $option):
                        ?>
                        <label class="option">
                            <input type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo $optionIndex; ?>">
                            <?php echo htmlspecialchars($option); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="navigation">
            <button type="button" id="prevBtn" class="nav-btn" style="display: none;">Previous</button>
            <button type="button" id="nextBtn" class="nav-btn">Next</button>
        </div>
    </form>
</div>

<script>
    let currentQuestion = 0;
    const totalQuestions = <?php echo count($questions); ?>;
    const quizDuration = <?php echo $quiz['duration_minutes']; ?> * 60; // Convert to seconds
    let timeRemaining = quizDuration;
    let timerInterval;

    // Start timer when page loads
    startTimer();

    function startTimer() {
        const timerElement = document.getElementById('timer');
        const startTime = Date.now();

        timerInterval = setInterval(() => {
            const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
            timeRemaining = quizDuration - elapsedSeconds;

            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz(true); // Auto-submit when time runs out
                return;
            }

            // Update timer display
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Add warning classes
            if (timeRemaining <= 60) { // Last minute
                timerElement.classList.add('danger');
            } else if (timeRemaining <= 180) { // Last 3 minutes
                timerElement.classList.add('warning');
            }
        }, 1000);
    }

    function updateQuestion() {
        const progress = ((currentQuestion + 1) / totalQuestions) * 100;
        document.querySelector('.progress').style.width = `${progress}%`;

        document.querySelectorAll('.question').forEach(q => q.classList.remove('active'));
        document.querySelector(`[data-question="${currentQuestion}"]`).classList.add('active');

        document.getElementById('prevBtn').style.display = currentQuestion === 0 ? 'none' : 'block';
        document.getElementById('nextBtn').innerText = currentQuestion === totalQuestions - 1 ? 'Submit' : 'Next';
    }

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentQuestion > 0) {
            currentQuestion--;
            updateQuestion();
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentQuestion < totalQuestions - 1) {
            currentQuestion++;
            updateQuestion();
        } else {
            submitQuiz(false);
        }
    });

    function submitQuiz(isTimeUp = false) {
        clearInterval(timerInterval); // Stop the timer

        if (isTimeUp) {
            alert('Time is up! Your quiz will be submitted automatically.');
        }

        const formData = new FormData(document.getElementById('quizForm'));
        fetch('submit_quiz.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = `quiz_results.php?id=${data.session_id}`;
                } else {
                    alert(isTimeUp ? 'An error occurred while submitting your quiz.' : 'Please answer all questions before submitting.');
                    if (!isTimeUp) {
                        startTimer(); // Restart timer if submission failed and it wasn't due to time up
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the quiz.');
                if (!isTimeUp) {
                    startTimer(); // Restart timer if submission failed and it wasn't due to time up
                }
            });
    }

    // Prevent leaving the page accidentally
    window.addEventListener('beforeunload', (e) => {
        e.preventDefault();
        e.returnValue = '';
    });

    // Previous option selection logic remains
    document.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', function() {
            const questionDiv = this.closest('.question');
            questionDiv.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
</script>
</body>
</html>