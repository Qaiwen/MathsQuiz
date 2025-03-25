<?php
if (!isset($_POST['quiz_id'])) {
    http_response_code(400); 
    echo "Quiz ID is required.";
    exit;
}
$quiz_id = $_POST['quiz_id'];
session_start();

if (!isset($_POST['quiz_id']) || !isset($_POST['question_index']) || !isset($_POST['question_data'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$quiz_id = $_POST['quiz_id'];
$question_index = $_POST['question_index'];
$question_data = $_POST['question_data'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
}

// Parse question data
$parsed_data = [];
foreach ($question_data as $data) {
    $parsed_data[$data['name']] = $data['value'];
}

// Generate question_id in the format ques_1, ques_2, etc.
$sql = "SELECT COUNT(*) AS total_questions FROM question WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$question_number = $row['total_questions'] + 1; // Increment to get the next question number
$question_id = 'ques_' . $question_number; // Updated format
$stmt->close();

// Prepare query to save data
$sql = "REPLACE INTO question (question_id, quiz_id, question, option_a, option_b, option_c, option_d, correct_answer)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssssssss',
    $question_id,
    $quiz_id,
    $parsed_data["questions[{$question_index}]"],
    $parsed_data["options[{$question_index}][0]"],
    $parsed_data["options[{$question_index}][1]"],
    $parsed_data["options[{$question_index}][2]"],
    $parsed_data["options[{$question_index}][3]"],
    $parsed_data["correct_answers[{$question_index}]"]
);

if ($stmt->execute()) {
    echo "Question saved successfully with ID: $question_id";
} else {
    http_response_code(500);
    echo "Failed to save question";
}
$stmt->close();
$conn->close();
?>