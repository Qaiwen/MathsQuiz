<?php
session_start(); // Start session management

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$database = "quiz4math"; // Replace with your database name

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: Please try again later.");
}

// Get student_id from the session
$student_id = $_SESSION['student_id'];

// Get the quiz_id from the URL
$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;
if (!$quiz_id) {
    echo "No quiz selected!";
    exit();
}

// Fetch quiz data
$quizQuery = "SELECT * FROM quiz WHERE quiz_id = ?";
$stmt = $conn->prepare($quizQuery);
$stmt->bind_param('s', $quiz_id);
$stmt->execute();
$quizResult = $stmt->get_result();

if ($quizResult->num_rows > 0) {
    $quizData = $quizResult->fetch_assoc();
    $currency = $quizData['currency']; // Get the quiz currency
} else {
    echo "Quiz not found!";
    exit();
}

// Fetch questions related to the quiz_id
$questionQuery = "SELECT * FROM question WHERE quiz_id = ?";
$stmt = $conn->prepare($questionQuery);
$stmt->bind_param('s', $quiz_id);
$stmt->execute();
$questionResult = $stmt->get_result();

$questions = [];
while ($row = $questionResult->fetch_assoc()) {
    $questions[] = $row;
}
if (empty($questions)) {
    echo "No questions available for this quiz.";
    exit();
}

// Get the last attempt_id from the database
$attemptQuery = "SELECT attempt_id FROM quizattempt ORDER BY attempt_id DESC LIMIT 1";
$attemptResult = $conn->query($attemptQuery);
$lastAttemptId = $attemptResult->num_rows > 0 ? $attemptResult->fetch_assoc()['attempt_id'] : null;

// Generate new attempt_id (attempt_1, attempt_2, etc.)
if ($lastAttemptId) {
    preg_match('/(\d+)$/', $lastAttemptId, $matches);
    $nextAttemptId = "attempt_" . ($matches[1] + 1);
} else {
    $nextAttemptId = "attempt_1";
}

// Handle form submission for quiz answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer'], $_POST['startTime'], $_POST['endTime']) && is_array($_POST['answer'])) {
        $answers = $_POST['answer'];
        $score = 0; // Initialize score
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];

        $conn->begin_transaction();

        try {
            // Insert quiz attempt record
            $attemptQuery = "
                INSERT INTO quizattempt (attempt_id, quiz_id, student_id, start_time, end_time, final_score)
                VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($attemptQuery);
            $stmt->bind_param('sssssi', $nextAttemptId, $quiz_id, $student_id, $startTime, $endTime, $score);
            $stmt->execute();

            // Get test_id from testattempt table based on quiz_id
            $testQuery = "SELECT test_id FROM testattempt WHERE quiz_id = ?";
            $stmt = $conn->prepare($testQuery);
            $stmt->bind_param('s', $quiz_id);
            $stmt->execute();
            $testResult = $stmt->get_result();
            $testData = $testResult->fetch_assoc();
            $test_id = $testData['test_id'];

            // Iterate over each question and check the student's answers
            foreach ($questions as $question) {
                $correctAnswer = $question['correct_answer'];
                $questionId = $question['question_id'];
                $selectedAnswer = $answers[$questionId] ?? null;

                // Store the value of the selected option in the 'answer' field
                $answerValue = null;
                if ($selectedAnswer === 'A') {
                    $answerValue = $question['option_a'];
                } elseif ($selectedAnswer === 'B') {
                    $answerValue = $question['option_b'];
                } elseif ($selectedAnswer === 'C') {
                    $answerValue = $question['option_c'];
                } elseif ($selectedAnswer === 'D') {
                    $answerValue = $question['option_d'];
                }

                // Generate answer_id format (answer_1, answer_2, etc.)
                $answerQuery = "SELECT answer_id FROM quizanswer ORDER BY answer_id DESC LIMIT 1";
                $answerResult = $conn->query($answerQuery);
                $lastAnswerId = $answerResult->num_rows > 0 ? $answerResult->fetch_assoc()['answer_id'] : null;
                preg_match('/(\d+)$/', $lastAnswerId, $matches);
                $nextAnswerId = $lastAnswerId ? "answer_" . ($matches[1] + 1) : "answer_1";

                // Insert into quizanswer table
                $is_correct = ($answerValue == $correctAnswer) ? 1 : 0;
                $insertAnswerQuery = "
                    INSERT INTO quizanswer (answer_id, attempt_id, question_id, answer, is_correct, test_id)
                    VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertAnswerQuery);
                $stmt->bind_param('ssssis', $nextAnswerId, $nextAttemptId, $questionId, $answerValue, $is_correct, $test_id);
                $stmt->execute();

                // Increment score for correct answer
                if ($is_correct) {
                    $score++;
                }
            }

            // Update the final score in quizattempt table
            $updateScoreQuery = "UPDATE quizattempt SET final_score = ? WHERE attempt_id = ?";
            $stmt = $conn->prepare($updateScoreQuery);
            $stmt->bind_param('is', $score, $nextAttemptId);
            $stmt->execute();

            // Calculate the final balance change based on quiz currency
            $finalBalanceChange = $score * $currency;

            // Update student balance
            $updateBalanceQuery = "UPDATE student SET balance = balance + ? WHERE student_id = ?";
            $stmt = $conn->prepare($updateBalanceQuery);
            $stmt->bind_param('ds', $finalBalanceChange, $student_id);
            $stmt->execute();

            $conn->commit();

            // Display message after submitting the answers
            echo "<script type='text/javascript'>alert('You earned $" . $finalBalanceChange . " for this quiz!');</script>";

            echo "<h3>Your Score: $score</h3>";
            echo "<h3>Your Balance after this quiz: " . ($userData['balance'] + $finalBalanceChange) . "</h3>";
            header("Location: QuizTab.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Failed to submit your answers: Please try again.";
        }
    } else {
        echo "Please select answers for all questions.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            color: black;
        }
        .quiz-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .question {
            margin-bottom: 20px;
        }
        .question ul {
            list-style-type: none;
            padding: 0;
        }
        .question li {
            margin-bottom: 10px;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        #timerDisplay {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        @media (max-width: 480px) {
    .signup-container h2 {
        font-size: 18px;
    }

    .form-group input,
    .signup-button {
        padding: 6px;
    }
    header {
        width:550px;
    }
}
    </style>
    <script>
        let startTime, endTime, timerInterval;

        window.onload = function() {
            startTime = new Date();
            timerInterval = setInterval(updateTimer, 1000);
        };

        function updateTimer() {
            const now = new Date();
            const elapsed = Math.floor((now - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            document.getElementById('timerDisplay').textContent = `Time Elapsed: ${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;
        }

        function stopTimer() {
            endTime = new Date().toISOString();
            document.getElementById('startTime').value = startTime.toISOString();
            document.getElementById('endTime').value = endTime;
            clearInterval(timerInterval);
        }
    </script>
</head>
<body>
    <div class="quiz-container">
        <h2><?php echo htmlspecialchars($quizData['title']); ?></h2>
        <div id="timerDisplay">Time Elapsed: 0m 00s</div>
        <form action="" method="POST" onsubmit="stopTimer()">
            <input type="hidden" name="startTime" id="startTime">
            <input type="hidden" name="endTime" id="endTime">
            <?php foreach ($questions as $question): ?>
                <div class="question">
                    <p><strong><?php echo htmlspecialchars($question['question']); ?></strong></p>
                    <ul>
                        <li><input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="A"> <?php echo htmlspecialchars($question['option_a']); ?></li>
                        <li><input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="B"> <?php echo htmlspecialchars($question['option_b']); ?></li>
                        <li><input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="C"> <?php echo htmlspecialchars($question['option_c']); ?></li>
                        <li><input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="D"> <?php echo htmlspecialchars($question['option_d']); ?></li>
                    </ul>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="submit-btn">Submit Answers</button>
        </form>
    </div>
</body>
</html>
