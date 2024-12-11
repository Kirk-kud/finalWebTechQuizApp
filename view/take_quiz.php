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

// Fetch quiz details
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
        /* Previous styles remain */
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
        }
        #prevBtn {
            background-color: #666;
            color: white;
        }
        #nextBtn {
            background-color: #4CAF50;
            color: white;
        }
        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #ddd;
            margin: 1rem 0;
            border-radius: 5px;
        }
        .progress {
            height: 100%;
            background-color: #4CAF50;
            border-radius: 5px;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <!-- Previous navbar content remains the same -->
</nav>

<div class="quiz-container">
    <h1><?php echo htmlspecialchars($quiz['name']); ?></h1>
    <p>Category: <?php echo htmlspecialchars($quiz['category_name']); ?></p>

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

    function updateQuestion() {
        // Update progress bar
        const progress = ((currentQuestion + 1) / totalQuestions) * 100;
        document.querySelector('.progress').style.width = `${progress}%`;

        // Hide all questions and show current
        document.querySelectorAll('.question').forEach(q => q.classList.remove('active'));
        document.querySelector(`[data-question="${currentQuestion}"]`).classList.add('active');

        // Update buttons
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
            // Submit quiz
            submitQuiz();
        }
    });

    function submitQuiz() {
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
                    alert('Please answer all questions before submitting.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the quiz.');
            });
    }

    // Highlight selected options
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