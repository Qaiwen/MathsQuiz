<?php
// Start session to retrieve logged-in instructor_id
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.php';</script>";
    exit;
}

$instructor_id = $_SESSION['instructor_id']; // Get instructor_id from session

// Database connection
$conn = new mysqli("localhost", "root", "", "quiz4math");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Flag to check if quiz was successfully created
$quizCreated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize form inputs
    $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $category = $conn->real_escape_string(trim($_POST['category'] ?? ''));
    $currency = intval($_POST['currency'] ?? 0);
    $question = intval($_POST['question'] ?? 0);

    // Validate all fields
    if ($title && $category && isset($currency) && isset($question)) {
        // Generate the next quiz_id
        $getLastIdQuery = "SELECT quiz_id FROM quiz ORDER BY quiz_id DESC LIMIT 1";
        $lastIdResult = $conn->query($getLastIdQuery);
        $nextQuizId = "Q_01"; // Default value if no quizzes exist

        if ($lastIdResult && $lastIdResult->num_rows > 0) {
            $lastIdRow = $lastIdResult->fetch_assoc();
            $lastQuizId = $lastIdRow['quiz_id'];
            $lastNumericPart = intval(substr($lastQuizId, 2)); // Extract the numeric part after 'Q_'
            $nextNumericPart = $lastNumericPart + 1;
            $nextQuizId = 'Q_' . str_pad($nextNumericPart, 2, '0', STR_PAD_LEFT); // Format as 'Q_XX'
        }

        // Check if the instructor_id exists in the instructor table
        $checkInstructorQuery = "SELECT COUNT(*) AS count FROM instructor WHERE instructor_id = ?";
        $checkStmt = $conn->prepare($checkInstructorQuery);
        $checkStmt->bind_param("s", $instructor_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();

        if ($row['count'] > 0) {
            // Insert into the database
            $sql = "INSERT INTO quiz (quiz_id, title, category, currency, question, instructor_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssiii", $nextQuizId, $title, $category, $currency, $question, $instructor_id);
                if ($stmt->execute()) {
                    $quizCreated = true;
                } else {
                    echo "<script>alert('Database error: " . $stmt->error . "');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Error preparing the SQL statement.');</script>";
            }
        } else {
            echo "<script>alert('Invalid instructor ID. Please log in again.'); window.location.href = 'login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please fill out all fields correctly.');</script>";
    }
}

$conn->close();

if ($quizCreated) {
    // Display success message and redirect
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Success</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #e0e0e0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .message {
                font-size: 24px;
                font-weight: bold;
                color: #000;
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = "instructorquiz.php";
            }, 3000);
        </script>
    </head>
    <body>
        <div class="message">Quiz setup has been successfully created.</div>
    </body>
    </html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Setup</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ccc;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }
        .nav {
            background-color: #444;
            color: #fff;
            width: 100%;
            padding: 10px 20px;
            text-align: right;
            position: absolute;
            top: 0;
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-size: 16px;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .container {
            text-align: center;
            margin-top: 70px;
            width: 100%;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 30px;
        }
        input[type="text"] {
            width: 250px;
            padding: 8px;
            margin: 10px 0 40px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .options {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: nowrap;
            width: 100%;
        }
        .options div {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            width: 180px;
            height: 220px;
            text-align: left;
        }
        button {
            margin-top: 40px;
            padding: 12px 40px;
            font-size: 16px;
            color: #fff;
            background-color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="#">CREATE QUIZ</a>
        <a href="#">SHOP</a>
    </div>
    <div class="container">
        <h1>Setup</h1>
        <form method="POST" action="">
            <label for="title">Quiz Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter quiz title here" required>
            <div class="options">
                <div>
                    <h3>Category</h3>
                    <label><input type="radio" name="category" value="Multiply" required> Multiply</label>
                    <label><input type="radio" name="category" value="Division" required> Division</label>
                    <label><input type="radio" name="category" value="Linear" required> Linear</label>
                    <label><input type="radio" name="category" value="Quadratic" required> Quadratic</label>
                </div>
                <div>
                    <h3>Currency Earn</h3>
                    <label><input type="radio" name="currency" value="1000" required> 90</label>
                    <label><input type="radio" name="currency" value="400" required> 40</label>
                    <label><input type="radio" name="currency" value="100" required> 10</label>
                </div>
                <div>
                    <h3>Question</h3>
                    <label><input type="radio" name="question" value="10" required> 10</label>
                    <label><input type="radio" name="question" value="15" required> 15</label>
                </div>
            </div>
            <button type="submit">Create Quiz</button>
        </form>
    </div>
</body>
</html>