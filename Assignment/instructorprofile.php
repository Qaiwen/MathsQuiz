<?php
// Start session
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

// Fetch instructor details
$conn = new mysqli('localhost', 'root', '', 'quiz4math');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$instructor_id = $_SESSION['instructor_id'];
$sql = "SELECT name, balance FROM instructor WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$name = $user['name'];
$balance = $user['balance'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }
        header {
            background-color: #444;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons a {
            padding: 10px 20px;
            margin: 5px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .quiz-btn {
            background-color: #007BFF;
        }
        .shop-btn {
            background-color: #28a745;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .buttons a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <a href="instructorshop.php">SHOP</a>
        </div>
        <div style="display: flex; gap: 20px; align-items: center;">
            <div>$<?php echo htmlspecialchars($balance); ?></div>
            <div>
                <a href="instructoreditprofile.php" style="color: white; text-decoration: none;"><?php echo htmlspecialchars($name); ?></a>
            </div>
        </div>
    </header>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        <p>Balance: $<?php echo htmlspecialchars($balance); ?></p>
        <div class="buttons">
            <a class="quiz-btn" href="instructorquiz.php">View Quizzes</a>
            <a class="shop-btn" href="instructorshop.php">Visit Shop</a>
            <a class="logout-btn" href="login.php">Logout</a>
        </div>
    </div>
</body>
</html>
