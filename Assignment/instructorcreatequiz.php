<?php
// Start session
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

// Get quiz_id from URL
if (!isset($_GET['quiz_id'])) {
    echo "<script>alert('No quiz selected.'); window.location.href = 'instructorprofile.rphp';</script>";
    exit;
}

$quiz_id = $_GET['quiz_id']; // Quiz ID from URL

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch quiz details
$sql = "SELECT title, question FROM quiz WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

if (!$quiz) {
    echo "<script>alert('Quiz not found.'); window.location.href = 'instructorprofile.php';</script>";
    exit;
}

$title = $quiz['title'];
$max_questions = $quiz['question']; // Max questions allowed for this quiz
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - <?php echo htmlspecialchars($title); ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        .question-block {
            margin-bottom: 20px;
            position: relative;
        }
        .question-block textarea,
        .question-block input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .correct-answer {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .save-status {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 12px;
            color: green;
        }
        .save-btn, .submit-btn {
            padding: 8px 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #28a745;
            width: 100%;
        }
        .save-btn:hover, .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Quiz: <?php echo htmlspecialchars($title); ?></h1>
        <form id="quizForm">
            <?php for ($i = 0; $i < $max_questions; $i++): ?>
                <div class="question-block" data-question-id="<?php echo $i; ?>">
                    <label for="question-<?php echo $i; ?>">Question <?php echo $i + 1; ?>:</label>
                    <textarea name="questions[<?php echo $i; ?>]" id="question-<?php echo $i; ?>" rows="3"></textarea>
                    <label>Options:</label>
                    <input type="text" name="options[<?php echo $i; ?>][0]" placeholder="Option A">
                    <input type="text" name="options[<?php echo $i; ?>][1]" placeholder="Option B">
                    <input type="text" name="options[<?php echo $i; ?>][2]" placeholder="Option C">
                    <input type="text" name="options[<?php echo $i; ?>][3]" placeholder="Option D">
                    <div class="correct-answer">
                        <label>Correct Answer:</label>
                        <select name="correct_answers[<?php echo $i; ?>]">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <button type="button" class="save-btn" onclick="saveQuestion(<?php echo $i; ?>)">Save Question</button>
                    <span class="save-status" id="status-<?php echo $i; ?>"></span>
                </div>
            <?php endfor; ?>
            <button type="button" class="submit-btn" onclick="submitQuiz()">Submit Quiz</button>
        </form>
    </div>

    <script>
        function saveQuestion(index) {
            const questionBlock = $(`[data-question-id="${index}"]`);
            const formData = questionBlock.find('textarea, input, select').serializeArray();
            const quizId = '<?php echo $quiz_id; ?>';

            $.ajax({
                url: 'save_question.php',
                method: 'POST',
                data: {
                    quiz_id: quizId,
                    question_data: formData,
                    question_index: index
                },
                success: function(response) {
                    $(`#status-${index}`).text(response); // Display the response (e.g., "Question saved with ID: Q01")
                },
                error: function() {
                    $(`#status-${index}`).text('Error Saving');
                }
            });
        }

        function submitQuiz() {
            const quizId = '<?php echo $quiz_id; ?>';
            $.ajax({
                url: 'submit_quiz.php',
                method: 'POST',
                data: { quiz_id: quizId },
                success: function() {
                    $('body').html('<div style="display:flex;justify-content:center;align-items:center;height:100vh;background-color:#d3d3d3;"><h1>Quiz setup has been successfully created.</h1></div>');
                    setTimeout(function() {
                        window.location.href = 'instructorquiz.php';
                    }, 5000);
                },
                error: function() {
                    alert('Error submitting quiz');
                }
            });
        }
    </script>
</body>
</html>
