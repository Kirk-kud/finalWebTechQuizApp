<?php
session_start();

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1)) {
    header("Location: ../../index.php");
    exit();
}

// Database connection
include '../../db/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add question
    if (isset($_POST['add_question'])) {
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);
        $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
        $correct_option = mysqli_real_escape_string($conn, $_POST['correct_option']);

        // Convert options array to JSON
        $options = array_map(function($option) {
            return mysqli_real_escape_string($GLOBALS['conn'], $option);
        }, $_POST['options']);
        $options_json = json_encode($options);

        // Insert question
        $query = "INSERT INTO questions (quiz_id, question_text, options, correct_option) 
                 VALUES ('$quiz_id', '$question_text', '$options_json', '$correct_option')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_questions.php?quiz_id=$quiz_id&success=Question added successfully");
        } else {
            header("Location: manage_questions.php?quiz_id=$quiz_id&error=Failed to add question");
        }
        exit();
    }

    // Update question
    if (isset($_POST['update_question'])) {
        $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);
        $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
        $correct_option = mysqli_real_escape_string($conn, $_POST['correct_option']);

        // Convert options array to JSON
        $options = array_map(function($option) {
            return mysqli_real_escape_string($GLOBALS['conn'], $option);
        }, $_POST['options']);
        $options_json = json_encode($options);

        // Update question
        $query = "UPDATE questions 
                 SET question_text = '$question_text', 
                     options = '$options_json', 
                     correct_option = '$correct_option' 
                 WHERE id = '$question_id' AND quiz_id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_questions.php?quiz_id=$quiz_id&success=Question updated successfully");
        } else {
            header("Location: manage_questions.php?quiz_id=$quiz_id&error=Failed to update question");
        }
        exit();
    }

    // Delete question
    if (isset($_POST['delete_question'])) {
        $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
        $quiz_id = mysqli_real_escape_string($conn, $_POST['quiz_id']);

        $query = "DELETE FROM questions WHERE id = '$question_id' AND quiz_id = '$quiz_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: manage_questions.php?quiz_id=$quiz_id&success=Question deleted successfully");
        } else {
            header("Location: manage_questions.php?quiz_id=$quiz_id&error=Failed to delete question");
        }
        exit();
    }
}

// Get quiz details
$quiz_id = mysqli_real_escape_string($conn, $_GET['quiz_id']);
$quiz_query = "SELECT * FROM quizzes WHERE id = '$quiz_id'";
$quiz_result = mysqli_query($conn, $quiz_query);
$quiz = mysqli_fetch_assoc($quiz_result);

// Get questions for this quiz
$questions_query = "SELECT * FROM questions WHERE quiz_id = '$quiz_id' ORDER BY created_at DESC";
$questions_result = mysqli_query($conn, $questions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - <?php echo htmlspecialchars($quiz['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: black;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .header {
            background-color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-create {
            background-color: #FF8C00;
            color: white;
        }

        .btn-create:hover {
            background-color: #FFA533;
            transform: translateY(-1px);
        }

        .btn-edit {
            background-color: transparent;
            border: 2px solid #FF8C00;
            color: #FF8C00;
        }

        .btn-edit:hover {
            background-color: rgba(255, 140, 0, 0.1);
        }

        .btn-delete {
            background-color: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }

        .btn-delete:hover {
            background-color: rgba(255, 68, 68, 0.1);
        }

        .menu-btn {
            background: none;
            border: none;
            color: #FF8C00;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
        }

        .menu-btn:hover {
            background-color: rgba(255, 140, 0, 0.1);
        }

        /* Question Cards */
        .question-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
        }

        .question-card h3 {
            color: #FF8C00;
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .option {
            padding: 0.75rem 1rem;
            margin: 0.75rem 0;
            border: 1px solid #eee;
            border-radius: 6px;
            background-color: #f8f8f8;
            transition: all 0.2s ease;
        }

        .option.correct {
            background-color: rgba(255, 140, 0, 0.1);
            border-color: #FF8C00;
        }

        .question-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            position: relative;
            border: 1px solid #eee;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            color: #FF8C00;
            margin: 0;
        }

        .modal-close {
            color: #666;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .modal-close:hover {
            color: #FF8C00;
        }

        /* Form Elements */
        input[type="text"],
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            color: black;
            margin-bottom: 1rem;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #FF8C00;
            outline: none;
        }

        .option-input {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .option-input input[type="radio"] {
            accent-color: #FF8C00;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: rgba(255, 140, 0, 0.1);
            border: 1px solid #FF8C00;
            color: #FF8C00;
        }

        .alert-error {
            background-color: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            color: #ff4444;
        }

        .question-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .option {
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .option.correct {
            background-color: #e8f5e9;
            border-color: #4caf50;
        }

        .modal form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .modal label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .modal textarea {
            width: 100%;
            min-height: 100px;
            margin-bottom: 1rem;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .option-input {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .option-input input[type="text"] {
            flex: 1;
        }
    </style>
</head>
<body>
<header class="header">
    <button onclick="window.location.href='manage_quizzes.php'" class="menu-btn">
        <i class="fas fa-arrow-left"></i>
    </button>
    <h1>Manage Questions - <?php echo htmlspecialchars($quiz['name']); ?></h1>
</header>

<main class="main-content">
    <?php if(isset($_GET['success'])): ?>
        <div style="background-color: green; color: white; padding: 1rem; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div style="background-color: red; color: white; padding: 1rem; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-create" onclick="openModal('addQuestionModal')">
        <i class="fas fa-plus"></i> Add New Question
    </button>
    <br><br>
    <div class="questions-list">
        <?php while($question = mysqli_fetch_assoc($questions_result)): ?>
            <div class="question-card">
                <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                <?php
                $options = json_decode($question['options'], true);
                foreach($options as $index => $option):
                    ?>
                    <div class="option <?php echo $index == $question['correct_option']-1 ? 'correct' : ''; ?>">
                        <?php echo htmlspecialchars($option); ?>
                        <?php if($index == $question['correct_option'] - 1): ?>
                            <i class="fas fa-check" style="color: green; float: right;"></i>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="question-actions">
                    <button class="btn btn-edit" onclick="editQuestion(<?php
                    echo htmlspecialchars(json_encode([
                        'id' => $question['id'],
                        'question_text' => $question['question_text'],
                        'options' => $options,
                        'correct_option' => $question['correct_option']
                    ]));
                    ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-delete" onclick="deleteQuestion(<?php echo $question['id']; ?>)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="addQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Question</h3>
                <span class="modal-close" onclick="closeModal('addQuestionModal')">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="add_question" value="1">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

                <label>
                    Question Text:
                    <textarea name="question_text" required></textarea>
                </label>

                <div class="options-container">
                    <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="option-input">
                            <input type="text" name="options[]" placeholder="Option <?php echo $i + 1; ?>" required>
                            <input type="radio" name="correct_option" value="<?php echo $i + 1; ?>" required>
                            <label>Correct</label>
                        </div>
                    <?php endfor; ?>
                </div>

                <button type="submit" class="btn btn-create">Add Question</button>
            </form>
        </div>
    </div>

    <div id="editQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Question</h3>
                <span class="modal-close" onclick="closeModal('editQuestionModal')">&times;</span>
            </div>
            <form method="POST" id="editQuestionForm">
                <input type="hidden" name="update_question" value="1">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <input type="hidden" name="question_id" id="editQuestionId">

                <label>
                    Question Text:
                    <textarea name="question_text" id="editQuestionText" required></textarea>
                </label>

                <div class="options-container" id="editOptionsContainer">
                    <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="option-input">
                            <input type="text" name="options[]" placeholder="Option <?php echo $i + 1; ?>" required>
                            <input type="radio" name="correct_option" value="<?php echo $i + 1; ?>" required>
                            <label>Correct</label>
                        </div>
                    <?php endfor; ?>
                </div>

                <button type="submit" class="btn btn-edit">Update Question</button>
            </form>
        </div>
    </div>
</main>
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function editQuestion(questionData) {
        document.getElementById('editQuestionId').value = questionData.id;
        document.getElementById('editQuestionText').value = questionData.question_text;

        // Fill options
        const optionInputs = document.querySelectorAll('#editOptionsContainer input[type="text"]');
        const radioInputs = document.querySelectorAll('#editOptionsContainer input[type="radio"]');

        questionData.options.forEach((option, index) => {
            optionInputs[index].value = option;
            radioInputs[index].checked = (index === questionData.correct_option);
        });

        openModal('editQuestionModal');
    }

    function deleteQuestion(questionId) {
        if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                    <input type="hidden" name="delete_question" value="1">
                    <input type="hidden" name="question_id" value="${questionId}">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
</script>
</body>
</html>