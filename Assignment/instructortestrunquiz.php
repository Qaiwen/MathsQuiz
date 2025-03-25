<?php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

$instructor_id = $_SESSION['instructor_id'];
$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

if (!$quiz_id) {
    echo "<script>alert('Quiz ID is required.'); window.location.href = 'test_run_page.php';</script>";
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch questions for the quiz
$sql = "SELECT question_id, question, option_a, option_b, option_c, option_d, correct_answer FROM question WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $quiz_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($questions)) {
    echo "<script>alert('No questions available for this quiz.'); window.location.href = 'test_run_page.php';</script>";
    exit;
}

// Generate a unique test_id
$sql = "SELECT COUNT(*) AS count FROM testattempt";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$test_id = 'test_' . ($row['count'] + 1); // Updated format for test_id

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <script>
        let startTime;

        function startClock() {
            startTime = new Date();
            setInterval(() => {
                const now = new Date();
                const elapsed = Math.floor((now - startTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                document.getElementById('clock').textContent = ${minutes}:${seconds < 10 ? '0' + seconds : seconds};
            }, 1000);

            document.getElementById('start_time').value = startTime.toISOString();
        }

        function endQuiz() {
            const endTime = new Date();
            document.getElementById('end_time').value = endTime.toISOString();
        }
    </script>
</head>
<body onload="startClock()">
    <h1>Quiz</h1>
    <div id="clock" style="font-size: 20px; color: green;"></div>
    <form id="quiz-form" action="submittestquiz.php" method="POST" onsubmit="endQuiz()">
        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <input type="hidden" name="instructor_id" value="<?php echo $instructor_id; ?>">
        <input type="hidden" id="start_time" name="start_time">
        <input type="hidden" id="end_time" name="end_time">

        <?php foreach ($questions as $index => $question): ?>
            <div>
                <p><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question']); ?></p>
                <input type="radio" name="answer[<?php echo htmlspecialchars($question['question_id']); ?>]" value="A" required> <?php echo htmlspecialchars($question['option_a']); ?><br>
                <input type="radio" name="answer[<?php echo htmlspecialchars($question['question_id']); ?>]" value="B"> <?php echo htmlspecialchars($question['option_b']); ?><br>
                <input type="radio" name="answer[<?php echo htmlspecialchars($question['question_id']); ?>]" value="C"> <?php echo htmlspecialchars($question['option_c']); ?><br>
                <input type="radio" name="answer[<?php echo htmlspecialchars($question['question_id']); ?>]" value="D"> <?php echo htmlspecialchars($question['option_d']); ?><br>
            </div>
        <?php endforeach; ?>

        <button type="submit">Submit Quiz</button>
    </form>
</body>
</html>