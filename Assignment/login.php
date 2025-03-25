<?php
session_start(); // Start session management

// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$database = "quiz4math";

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Check for student login
    if (isset($_POST['loginStudent'])) {
        $stmt = $conn->prepare("SELECT student_id, password FROM student WHERE name = ?");
        $stmt->bind_param("s", $input_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($input_password === $user['password']) { // Replace with password_verify() if passwords are hashed
                $_SESSION['student_id'] = $user['student_id'];
                header("Location: QuizTab.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('No student found with this username.');</script>";
        }
        $stmt->close();
    }

    // Check for instructor login
    if (isset($_POST['loginInstructor'])) {
        $stmt = $conn->prepare("SELECT instructor_id, password FROM instructor WHERE name = ?");
        $stmt->bind_param("s", $input_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($input_password === $user['password']) { // Replace with password_verify() if passwords are hashed
                $_SESSION['instructor_id'] = $user['instructor_id'];
                header("Location: instructorprofile.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('No instructor found with this username.');</script>";
        }
        $stmt->close();
    }

    // Check for admin login
    if (isset($_POST['loginAdmin'])) {
        $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE name = ?");
        $stmt->bind_param("s", $input_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($input_password === $user['password']) { // Replace with password_verify() if passwords are hashed
                $_SESSION['admin_id'] = $user['admin_id'];
                header("Location: adminpanel.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('No admin found with this username.');</script>";
        }
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
        }
        .left-side {
            width: 50%; 
            background: url('image/logo.jpg') no-repeat center center; 
            background-size: cover; 
        }
        .right-side {
            width: 50%; 
            background-color: rgb(88, 88, 88); 
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: white;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-button:hover {
            background-color: #0056b3;
        }
        .signup {
            margin-top: 20px;
            text-align: center;
        }
        .signup a {
            color: #007bff;
            text-decoration: none;
        }
        .signup a:hover {
            text-decoration: underline;
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
    <div class="left-side"></div>
    <div class="right-side">
        <div class="login-container">
            <h2>Log In</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="loginStudent" class="login-button">Log In as Student</button>
                <button type="submit" name="loginInstructor" class="login-button">Log In as Instructor</button>
                <button type="submit" name="loginAdmin" class="login-button">Log In as Admin</button>
                <div class="signup">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
