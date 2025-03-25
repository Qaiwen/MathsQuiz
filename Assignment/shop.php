<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "quiz4math";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session and check if a user is logged in (student or lecturer)
session_start();

if (isset($_SESSION['student_id'])) {
    $user_id = $_SESSION['student_id'];
    $user_role = 'student';
    $balanceQuery = "SELECT balance FROM student WHERE student_id = '$user_id'";
} elseif (isset($_SESSION['instructor_id'])) {
    $user_id = $_SESSION['instructor_id'];
    $user_role = 'instructor';
    $balanceQuery = "SELECT balance FROM instructor WHERE instructor_id = '$user_id'";
} else {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch balance
$balanceResult = $conn->query($balanceQuery);
$balance = $balanceResult->fetch_assoc()['balance'];

// Fetch data from the shop table
$sql = "SELECT * FROM shop WHERE display = 1";
$result = $conn->query($sql);

// Handle theme purchase
if (isset($_POST['submit_purchase'])) {
    $theme_id = $_POST['theme_id'];
    $price = $_POST['price'];

    // Check if the user has sufficient balance
    if ($balance >= $price) {
        $new_balance = $balance - $price;

        // Update balance depending on the role
        if ($user_role === 'student') {
            $updateBalanceQuery = "UPDATE student SET balance = '$new_balance' WHERE student_id = '$user_id'";
        } elseif ($user_role === 'instructor') {
            $updateBalanceQuery = "UPDATE instructor SET balance = '$new_balance' WHERE instructor_id = '$user_id'";
        }
        $conn->query($updateBalanceQuery);

        // Generate new useritem_id
        $userItemQuery = "SELECT useritem_id FROM useritem ORDER BY useritem_id DESC LIMIT 1";
        $userItemResult = $conn->query($userItemQuery);
        $lastUserItem = $userItemResult->fetch_assoc();

        if ($lastUserItem) {
            preg_match('/(\d+)$/', $lastUserItem['useritem_id'], $matches);
            $nextUserItemId = "UI_" . ($matches[1] + 1);
        } else {
            $nextUserItemId = "UI_1";
        }

        // Insert into useritem table
        $insertQuery = "INSERT INTO useritem (useritem_id, {$user_role}_id, item_id, is_equipped) 
                        VALUES ('$nextUserItemId', '$user_id', '$theme_id', 1)";
        $conn->query($insertQuery);

        echo "<script>alert('Purchase successful! Your new balance is $$new_balance');</script>";
    } else {
        echo "<script>alert('Insufficient balance. Please add more funds.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Themes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            background-color: #d9d9d9;
            padding: 20px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .themes {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .theme-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: calc(25% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
        }
        .theme-item h3 {
            font-size: 18px;
            margin: 10px 0 5px;
        }
        .theme-item p {
            font-size: 14px;
            color: #666;
            margin: 5px 0 10px;
        }
        .theme-item .price {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .theme-item button {
            background: #000;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .theme-item button:hover {
            background: #444;
        }
        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background: #ff5733;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .back-button:hover {
            background: #ff2d00;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Themes</h1>
        <a href="QuizTab.php" class="back-button">Back</a>
    </div>
    
    <div class="themes">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $theme_id = $row["item_id"];
                $theme_price = $row["price"];
                echo '<div class="theme-item">';
                echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '<div class="price">$' . htmlspecialchars($row["price"]) . '</div>';
                ?>
                <form method="POST" style="text-align: center;">
                    <input type="hidden" name="theme_id" value="<?php echo $theme_id; ?>">
                    <input type="hidden" name="price" value="<?php echo $theme_price; ?>">
                    <button type="submit" name="submit_purchase">Buy Now</button>
                </form>
                <?php
                echo '</div>';
            }
        } else {
            echo "<p>No themes available.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>