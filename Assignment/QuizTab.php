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
    die("Connection failed: " . $conn->connect_error);
}

// Get student_id from the session
$student_id = $_SESSION['student_id'];

// Fetch user data
$userData = [];
$userQuery = "SELECT name, level, balance FROM student WHERE student_id = '$student_id'";
$result = $conn->query($userQuery);
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}

// Check for equipped theme
$theme = "#ffffff"; // Default theme (white background)
$themeColors = [
    'item_1' => '#add8e6',  // Frost (light blue)
    'item_2' => '#7cfc00',  // Grass green
    'item_3' => '#2e1a47',  // Midnight (dark purple)
];
$buttonColors = [
    'item_1' => '#007bff',  // Frost theme button color (blue)
    'item_2' => '#32cd32',  // Grass theme button color (green)
    'item_3' => '#8a2be2',  // Midnight theme button color (purple)
];

$themeQuery = "
    SELECT u.item_id 
    FROM useritem u 
    WHERE u.student_id = '$student_id' AND u.is_equipped = 1
    LIMIT 1";
$result = $conn->query($themeQuery);
if ($result->num_rows > 0) {
    $equippedItem = $result->fetch_assoc()['item_id'];
    if (array_key_exists($equippedItem, $themeColors)) {
        $theme = $themeColors[$equippedItem];
    }
}

// Fetch unique quiz categories
$categories = [];
$categoryQuery = "SELECT DISTINCT category FROM quiz";
$categoryResult = $conn->query($categoryQuery);
if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Fetch quizzes
$allQuizzes = [];
$allQuizzesQuery = "SELECT quiz_id, title, category FROM quiz";
$result = $conn->query($allQuizzesQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allQuizzes[] = $row;
    }
}

// Fetch last attempt score and time taken
$lastAttemptQuery = "SELECT start_time, end_time, final_score FROM quizattempt WHERE student_id = '$student_id' ORDER BY attempt_id DESC LIMIT 1";
$lastAttemptResult = $conn->query($lastAttemptQuery);
$lastAttemptData = $lastAttemptResult->num_rows > 0 ? $lastAttemptResult->fetch_assoc() : null;

$lastAttemptScore = $lastAttemptData ? $lastAttemptData['final_score'] : "No attempts yet";

// Calculate the time difference (if data exists)
$timeTaken = "";
if ($lastAttemptData) {
    $startTime = new DateTime($lastAttemptData['start_time']);
    $endTime = new DateTime($lastAttemptData['end_time']);
    $interval = $startTime->diff($endTime);
    $timeTaken = $interval->format('%H:%I:%S');  // Format as HH:MM:SS
}

// Handle logout request
if (isset($_POST['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Tab</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?php echo $theme; ?>; /* Dynamically set background color */
            margin: 0;
            padding: 0;
            color: black;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: black;
            color: white;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .header-left img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .dropdown {
            position: relative;
        }
        .dropdown button {
            background-color: #444;
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .dropdown button:hover {
            background-color: #666;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: black;
            color: white;
            border-radius: 5px;
            padding: 10px;
            z-index: 1000;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: white;
        }
        .dropdown-menu a:hover {
            background-color: #666;
        }
        .header-right {
            display: flex;
            align-items: center;
        }
        .header-right img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 50%;
        }
        .header-right span {
            margin-right: 15px;
        }
        .shop-btn, .logout-btn {
            margin-left: 20px;
            background-color: <?php echo $buttonColors[$equippedItem] ?? '#ff9900'; ?>;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }
        .shop-btn:hover, .logout-btn:hover {
            background-color: <?php echo $buttonColors[$equippedItem] ?? '#ffcc66'; ?>;
            color: #333;
        }
        .quiz-section {
            padding: 20px;
        }

        /* Separate box for each quiz category */
        .quiz-category-box {
            background-color: <?php echo $buttonColors[$equippedItem] ?? '#ff5733'; ?>;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quiz-category-box h3 {
            margin: 0
            font-size: 24px;
        }

        /* Style the quiz cards within each box */
        .quiz-card {
            background-color: white;
            color: black;
            padding: 10px;
            border-radius: 5px;
            width: 150px;
            text-align: center;
            cursor: pointer;
            margin-top: 10px;
        }

        .quiz-card:hover {
            background-color: <?php echo $buttonColors[$equippedItem] ?? '#ff3e00'; ?>;
            color: white;
        }

        .last-attempt {
            padding: 20px;
            font-weight: bold;
            font-size: 30px;
            background-color: #f4f4f4;
            border: 2px solid #ccc;
            border-radius: 10px;
            margin-top: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 480px) {
            header {
                width: 550px;
            }

            .quiz-category-box {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="header-left">
        <img src="image/logo.jpg" alt="Logo">
        <div class="dropdown">
            <button onclick="toggleDropdown()">Quizzes</button>
            <div class="dropdown-menu" id="quizDropdownMenu">
                <a href="#" onclick="filterQuizzes('All')">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="#" onclick="filterQuizzes('<?php echo $category; ?>')"><?php echo $category; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="header-right">
        <a href="profile.php">
            <img src="image/profileLogo.jpg" alt="Profile Logo" style="width: 30px; height: 30px; margin-right: 10px; border-radius: 50%;">
        </a>
        <span>Welcome, <?php echo $userData['name']; ?>!</span>
        <span>Level: <?php echo $userData['level']; ?></span>
        <span>Balance: $<?php echo $userData['balance']; ?></span>
        <a href="shop.php" class="shop-btn">Shop</a>
        <form action="" method="POST" style="display:inline;">
            <button type="submit" name="logout" class="logout-btn">Log Out</button>
        </form>
    </div>
</header>
<main>
    <section class="quiz-section">
        <h2>Quizzes</h2>
        
        <!-- Loop through categories and display each one in a separate box -->
        <div id="quiz-container" class="quiz-container">
            <!-- Quizzes will be dynamically inserted here based on the selected category -->
        </div>

        <!-- Last Attempt Scores and Time Taken -->
        <div class="last-attempt">
            <h3>Last Attempt Scores: Score: <?php echo $lastAttemptScore; ?> </h3>
            <p>Time Taken: <?php echo $timeTaken; ?></p>
        </div>
    </section>
</main>

<script>
    const allQuizzes = <?php echo json_encode($allQuizzes); ?>;

    function toggleDropdown() {
        const menu = document.getElementById('quizDropdownMenu');
        if (menu.style.display === "block") {
            menu.style.display = "none";
        } else {
            menu.style.display = "block";
        }
    }

    function filterQuizzes(category) {
        const container = document.getElementById("quiz-container");
        container.innerHTML = ""; // Clear current quizzes

        const filteredQuizzes = category === "All"
            ? allQuizzes
            : allQuizzes.filter(quiz => quiz.category === category);

        if (filteredQuizzes.length > 0) {
            filteredQuizzes.forEach(quiz => {
                const quizBox = document.createElement("div");
                quizBox.className = "quiz-category-box";
                quizBox.innerHTML = `<h3>${quiz.title}</h3>`;

                // Create the quiz card
                const quizCard = document.createElement("a");
                quizCard.className = "quiz-card";
                quizCard.innerHTML = "Start Quiz";
                quizCard.href = `QuizQuestions.php?quiz_id=${quiz.quiz_id}`;

                quizBox.appendChild(quizCard);
                container.appendChild(quizBox);
            });
        } else {
            container.innerHTML = "<span>No quizzes found.</span>";
        }

        // Close the dropdown menu after filtering
        const menu = document.querySelector('.dropdown-menu');
        menu.style.display = 'none';
    }

    // Show all quizzes by default
    filterQuizzes("All");
</script>
</body>
</html>
