<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* Your existing styles remain unchanged */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
        }
        .left-side {
            flex: 1;
            background-color: #d0d0d0;
            background: url('image/logo.jpg') no-repeat center center; 
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .left-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .right-side {
            width: 50%;
            background-color: rgb(88, 88, 88);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signup-container {
            width: 300px;
            text-align: center;
        }
        .signup-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: white;
        }
        .form-group, .form-group2 {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label, .form-group2 label {
            display: block;
            margin-bottom: 5px;
            color: white;
        }
        .form-group input, .form-group2 select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .signup-button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .signup-button:hover {
            background-color: #218838;
        }
        .login {
            margin-top: 10px;
            text-align: center;
            color: white;
        }
        .login a {
            color: #007bff;
            text-decoration: none;
        }
        .login a:hover {
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
    <script>
        function toggleIDLabel() {
            const accountType = document.getElementById("type").value;
            const idLabel = document.getElementById("id_label");
            const idInput = document.getElementById("student_id");

            if (accountType === "Instructor-account") {
                idLabel.innerText = "Instructor ID";
                idInput.placeholder = "Enter your Instructor ID (e.g., I_1)";
            } else {
                idLabel.innerText = "Student ID";
                idInput.placeholder = "Enter your Student ID (e.g., S_1)";
            }
        }

        function validateIDFormat() {
            const idInput = document.getElementById("student_id").value;
            const accountType = document.getElementById("type").value;
            const idPattern = accountType === "Instructor-account" ? /^I_\d+$/ : /^S_\d+$/;

            if (!idPattern.test(idInput)) {
                alert("Invalid ID format. Use 'S_1' for Student or 'I_1' for Instructor.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="left-side">
        <!-- Placeholder for logo -->
    </div>
    <div class="right-side">
        <div class="signup-container">
            <h2>Sign Up</h2>
            <form action="" method="POST" onsubmit="return validateIDFormat()">
                <div class="form-group">
                    <label for="username">Name</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label id="id_label" for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" placeholder="Enter your Student ID (e.g., S_1)" required>
                </div>
                <div class="form-group2">
                    <label for="type">Types of account</label>
                    <select id="type" name="type" onchange="toggleIDLabel()" required>
                        <option value="Student-account">Student Account</option>
                        <option value="Instructor-account">Instructor Account</option>
                    </select>
                </div>
                <button type="submit" name="signupbtn" class="signup-button">Sign Up</button>
                <div class="login">
                    <p>Already have an account? <a href="login.php">Log In</a></p>
                </div>
            </form>
        </div>
    </div>

    <?php
if (isset($_POST['signupbtn'])) {
    include("conn.php");

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $student_id = mysqli_real_escape_string($con, $_POST['student_id']);
    $type = mysqli_real_escape_string($con, $_POST['type']);

    // Validate ID format server-side
    if ($type === "Instructor-account" && !preg_match("/^I_\d+$/", $student_id)) {
        echo '<script>alert("Invalid Instructor ID format. Use I_1, I_2, etc.");</script>';
    } elseif ($type === "Student-account" && !preg_match("/^S_\d+$/", $student_id)) {
        echo '<script>alert("Invalid Student ID format. Use S_1, S_2, etc.");</script>';
    } else {
        // Check for existing username, ID, or email
        $check_query = $type === "Instructor-account" 
            ? "SELECT * FROM instructor WHERE name = '$username' OR instructor_id = '$instructor_id' OR email = '$email'" 
            : "SELECT * FROM student WHERE name = '$username' OR student_id = '$student_id' OR email = '$email'";

        $result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['name'] === $username) {
                echo '<script>alert("Username already exists! Please choose a different username.");</script>';
            } elseif ($row['student_id'] === $student_id || $row['instructor_id'] === $student_id) {
                echo '<script>alert("Student/Instructor ID already exists! Please provide a unique ID.");</script>';
            } elseif ($row['email'] === $email) {
                echo '<script>alert("Email already exists! Please use a different email address.");</script>';
            }
        } else {
            if ($type === "Instructor-account") {
                // Insert into instructor table
                $sql = "INSERT INTO instructor (instructor_id, name, email, password)
                        VALUES ('$instructor_id', '$username', '$email', '$password')";
            } else {
                // Insert into student table
                $sql = "INSERT INTO student (student_id, name, email, password, level, balance)
                        VALUES ('$student_id', '$username', '$email', '$password', '1', '0')";
            }

            if (!mysqli_query($con, $sql)) {
                die('Error: ' . mysqli_error($con));
            } else {
                echo '<script>alert("Registered Successfully!");
                window.location.href = "login.php";
                </script>';
            }
        }
    }

    mysqli_close($con);
}
?>
</body>
</html>