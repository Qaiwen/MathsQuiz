<?php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? htmlspecialchars($_GET['quiz_id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f3f3f3;
        }
        header {
            background-color: lightgray;
            padding: 20px;
            font-size: 24px;
        }
        .results {
            margin-top: 50px;
        }
        .score {
            font-size: 32px;
            color: green;
        }
        .buttons {
            margin-top: 30px;
        }
        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .buttons button:hover {
            opacity: 0.9;
        }
        .back {
            background-color: #007BFF;
            color: white;
        }
        .retry {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <header>Quiz Results</header>
    <div class="results">
        <p class="score">You scored <?php echo $score; ?> out of <?php echo $total; ?></p>
    </div>
    <div class="buttons">
        <button class="back" onclick="window.location.href='instructorprofile.php';">Back to Profile</button>
        <button class="retry" onclick="window.location.href='instructortestrunquiz.php?quiz_id=<?php echo $quiz_id; ?>';">Try Again</button>
    </div>
</body>
</html>
