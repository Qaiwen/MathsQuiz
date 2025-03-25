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

// Fetch themes owned by the user
$ownedThemes = [];
$themeQuery = "
    SELECT s.item_id, s.title, s.description
    FROM useritem u
    INNER JOIN shop s ON u.item_id = s.item_id
    WHERE u.student_id = '$student_id' AND s.category = 'Theme'";
$result = $conn->query($themeQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ownedThemes[] = $row;
    }
}

// Create an array to map theme titles to colors
$themeColors = [
    'Frost Theme' => '#add8e6',  // light blue
    'Grass' => '#7cfc00',        // grass green
    'Midnight' => '#2e1a47', // midnight
];

// Handle theme change request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['theme_id'])) {
    $theme_id = $_POST['theme_id'];

    // Set all themes for the user as not equipped
    $conn->query("UPDATE useritem SET is_equipped = 0 WHERE student_id = '$student_id'");

    // Equip the selected theme
    $conn->query("UPDATE useritem SET is_equipped = 1 WHERE student_id = '$student_id' AND item_id = '$theme_id'");

    echo '<script>alert("Theme updated successfully!"); window.location.href = "profile.php";</script>';
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Theme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .theme-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .theme-item  {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9; /* Default color */
        }

        .theme-item h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .theme-item p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .theme-item button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .theme-item button:hover {
            background-color: #0056b3;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
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
    <div class="container">
        <h1>Change Your Theme</h1>
        <form action="" method="POST" class="theme-list">
            <?php if (count($ownedThemes) > 0): ?>
                <?php foreach ($ownedThemes as $theme): ?>
                    <?php
                    // Normalize the theme title before using it for color lookup
                    $normalizedTitle = trim(ucwords(strtolower($theme['title']))); // Normalizing title
                    ?>
                    <div class="theme-item" style="background-color: <?php echo htmlspecialchars($themeColors[$normalizedTitle] ?? '#f9f9f9'); ?>;">
                        <h3><?php echo htmlspecialchars($theme['title']); ?></h3>
                        <p><?php echo htmlspecialchars($theme['description']); ?></p>
                        <button type="submit" name="theme_id" value="<?php echo htmlspecialchars($theme['item_id']); ?>">
                            Equip Theme
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You don't own any themes yet.</p>
            <?php endif; ?>
        </form>
        <a href="profile.php" class="back-btn">Back to Profile</a>
    </div>
</body>
</html>
