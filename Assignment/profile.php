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

// Fetch achievements and their details
$achievements = [];
$achievementQuery = "
    SELECT a.name, a.description 
    FROM userachievement ua
    JOIN achievement a ON ua.achievement_id = a.achievement_id
    WHERE ua.student_id = '$student_id'";
$result = $conn->query($achievementQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $achievements[] = [
            'name' => $row['name'],
            'description' => $row['description'],
        ];
    }
}

// Check for equipped item and fetch its theme
$equippedTheme = "#808080"; // Default theme (gray)
$themeColors = [
    'item_1' => '#add8e6',  // Frost (light blue)
    'item_2' => '#7cfc00',  // grass
    'item_3' => '#2e1a47', // midnight
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
        $equippedTheme = $themeColors[$equippedItem];
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            background-color: <?php echo $equippedTheme; ?>; /* Theme color */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 600px;
            color: #000;
        }

        .header {
            text-align: center;
            padding: 20px;
        }

        .header img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .header h2 {
            margin: 10px 0;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            text-shadow: 1px 1px 4px rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header h2 .profile-pic {
            margin-left: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: <?php echo $equippedTheme; ?>;
            padding: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header h2 .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .content {
            padding: 20px;
        }

        .content h2 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: normal;
        }

        .level {
            margin: 10px 0;
            font-size: 16px;
        }

        .stats {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .achievements h3 {
            font-size: 18px;
            font-weight: bold;
        }

        .achievements ul {
            list-style-type: disc;
            margin: 10px 0 20px 20px;
            padding: 0;
        }

        .achievements ul li {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-container a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            width: fit-content;
        }

        .btn-container a:hover {
            background-color: #0056b3;
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
</head>
<body>
    <div class="profile-container">
        <div class="header">
            <img src="image/logo.jpg" alt="Header Image">
            <h2>
                <?php echo htmlspecialchars($userData['name']); ?>
                <div class="profile-pic">
                    <img src="image/male.jpg" alt="Profile Icon">
                </div>
            </h2>
        </div>
        <div class="content">
            <h2>Profile Details</h2>
            <div class="level">Level: <?php echo htmlspecialchars($userData['level']); ?></div>
            <div class="stats">
                Balance: $<?php echo htmlspecialchars($userData['balance']); ?><br>
            </div>
            <div class="achievements">
                <h3>Achievements</h3>
                <ul>
                    <?php
                    if (!empty($achievements)) {
                        foreach ($achievements as $achievement) {
                            echo "<li><strong>" . htmlspecialchars($achievement['name']) . ":</strong> " . htmlspecialchars($achievement['description']) . "</li>";
                        }
                    } else {
                        echo "<li>No achievements yet.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="btn-container">
            <a href="QuizTab.php">Back to QuizTab</a>
            <a href="ChangeTheme.php">Change Theme</a>
        </div>
    </div>
</body>
</html>
