<!-- PHP -->
<?php
session_start();

// Check if the user is logged in, if not redirect to the login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch the user's personalized data (for example, their username)
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Home Page</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        
    </style>
</head>
<body>
<?php
        // Include header where welcome message is displayed
        require 'header.php';
    ?>

    <p>This is your personalized home page content.</p>
    <a href="logout.php">Logout</a>
</body>
</html>