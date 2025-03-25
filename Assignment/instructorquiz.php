<?php 
// Start session to retrieve logged-in instructor_id
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

$instructor_id = $_SESSION['instructor_id']; // Get instructor_id from session

// Database connection
$conn = new mysqli('localhost', 'root', '', 'quiz4math');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$sql = "SELECT name, balance FROM instructor WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$name = $user['name'];
$balance = $user['balance'];
$stmt->close();

// Fetch quizzes for the instructor
$sql = "SELECT quiz.quiz_id, quiz.title, quiz.question, 
        (SELECT COUNT(*) FROM question WHERE question.quiz_id = quiz.quiz_id) AS question_count 
        FROM quiz 
        WHERE quiz.instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $instructor_id);
$stmt->execute();
$quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz UI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #444;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
        }
        header div {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            padding: 20px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .quiz-section {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* Auto-adjust grid based on screen width */
            gap: 50px; /* Space between cards */
            justify-items: center;
        }
        .quiz-card {
            width: 150px; /* Keep cards compact */
            height: 150px; /* Fixed height for uniformity */
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .quiz-card h2 {
            font-size: 16px;
            margin: 5px 0;
        }
        .quiz-card p {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .quiz-card button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }
        .quiz-card button:hover {
            background-color: #0056b3;
        }
        .setup-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .setup-button:hover {
            background-color: #218838;
        }
        </style>
</head>
<body>
    <header>
        <div>
            <a href="instructorshop.php">SHOP</a>
        </div>
        <div>
            <div>$<?php echo htmlspecialchars($balance); ?></div> <!-- Ensure escaping -->
            <div>
                <a href="instructorprofile.php"><?php echo htmlspecialchars($name); ?></a> <!-- Ensure escaping -->
            </div>
        </div>
    </header>
    <div class="container">
        <h1>Create Quiz</h1>
        <div class="quiz-section">
            <?php foreach ($quizzes as $quiz): ?>
                <?php if ($quiz['question_count'] == 0): ?>
                    <div class="quiz-card">
                        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                        <p>Questions: <?php echo htmlspecialchars($quiz['question']); ?></p>
                        <button onclick="window.location.href='instructorcreatequiz.php?quiz_id=<?php echo htmlspecialchars($quiz['quiz_id']); ?>'">Create Quiz</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <h1>Test Run</h1>
        <div class="quiz-section">
            <?php foreach ($quizzes as $quiz): ?>
                <?php if ($quiz['question_count'] > 0): ?>
                    <div class="quiz-card">
                        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                        <p>Questions: <?php echo htmlspecialchars($quiz['question']); ?></p>
                        <button onclick="window.location.href='instructortestrunquiz.php?quiz_id=<?php echo htmlspecialchars($quiz['quiz_id']); ?>'">Test Run</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <button class="setup-button" onclick="window.location.href='instructorsetupquiz.php'">Setup New Quiz</button>
    </div>
</body>
</html>