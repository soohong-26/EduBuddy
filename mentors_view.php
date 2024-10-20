<?php
require 'database.php'; // Include the database connection

// Check if the user is logged in and is a mentor
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Fetch all students from the database
$sql = "SELECT user_id, username, email FROM users WHERE roles = 'student'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors View</title>
    <!-- CSS -->
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
            margin: 20px;
            padding: 20px;
        }

        /* Lists */
        .user-list {
            padding: auto;
            margin: auto;
            list-style: none;
        }

        .user-item {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Texts */
        .username, .email {
            font-weight: bold; /* Example to make text bold */
        }

        .username {
            color: #3498db; /* Blue color for username */
        }

        .email {
            color: #e74c3c; /* Red color for email */
        }

        /* Title */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin-top: 100px;
            padding: 20px;
            text-align: center;
            font-weight: 800;
            font-size: 50px;
        }

        .profile-btn {
            padding: 9px 25px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 50px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>
    
    <!-- Display the users -->
    <?php
    if ($result->num_rows > 0) {
        echo "<ul class='user-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li class='user-item'><span class='username'>" . htmlspecialchars($row['username']) . "</span> - <span class='email'>" . htmlspecialchars($row['email']) . 
                 "</span> <button class='profile-btn' onclick=\"location.href='students_profile.php?username=" . $row['username'] . "'\">View Profile</button></li>";
        }
        echo "</ul>";
    } else {
        echo "No students found.";
    }
    ?>
</body>
</html>
