<?php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

$instructor_id = $_POST['instructor_id'];
$quiz_id = $_POST['quiz_id'];
$test_id = $_POST['test_id'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$answers = $_POST['answer'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate score
$correctAnswers = 0;
$totalQuestions = count($answers);
foreach ($answers as $question_id => $answer) {
    $sql = "SELECT correct_answer FROM question WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $question_id);
    $stmt->execute();
    $stmt->bind_result($correct_answer);
    $stmt->fetch();
    $stmt->close();

    if ($answer == $correct_answer) {
        $correctAnswers++;
    }
}

// Save test attempt
$sql = "INSERT INTO testattempt (test_id, admin_id, instructor_id, start_time, end_time, final_score, quiz_id) VALUES (?, NULL, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssds', $test_id, $instructor_id, $start_time, $end_time, $correctAnswers, $quiz_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect to the results page
header("Location: testresults.php?score=$correctAnswers&total=$totalQuestions&quiz_id=$quiz_id");
exit;
?>
