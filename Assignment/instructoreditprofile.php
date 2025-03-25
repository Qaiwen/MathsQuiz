<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
    }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .header {
      background: #4caf50;
      color: white;
      padding: 15px;
      text-align: center;
      font-size: 18px;
      position: relative;
    }
    .header .close-btn {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 18px;
      color: white;
      cursor: pointer;
    }
    .edit-options {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: 20px;
    }
    .edit-options button {
      background: #f0f0f0;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 10px;
      font-size: 14px;
      cursor: pointer;
      text-align: left;
      transition: 0.3s ease;
    }
    .edit-options button:hover {
      background: #e0e0e0;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        Edit Profile
        <a href="instructorprofile.php" class="back-to-profile-btn" style="color: white; position: absolute; left: 20px;">← Back to Profile</a>
        <span class="close-btn" onclick="alert('Close clicked!')">X</span>
      </div>
    </div>
    <div class="edit-options">
      <button>Title</button>
      <button>Theme</button>
    </div>
  </div>
</body>
</html>
