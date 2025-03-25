<?php
session_start();

if (!isset($_POST['quiz_id'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$quiz_id = $_POST['quiz_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
}

// Finalize quiz submission (if any logic required, e.g., marking it as completed)
$sql = "UPDATE quiz SET status = 'completed' WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $quiz_id);

if ($stmt->execute()) {
    echo "Quiz submitted successfully";
} else {
    http_response_code(500);
    echo "Failed to submit quiz";
}
$stmt->close();
$conn->close();
