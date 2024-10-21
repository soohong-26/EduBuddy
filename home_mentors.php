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
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin-top: 100px;
            padding: 20px;
            text-align: center;
            font-weight: 800;
            font-size: 50px
        }

        .information-text {
            color: white;
            margin: 10px 270px;
            font-weight: 400;
            text-align: center;
            line-height: 2.0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php';?>

    <!-- Welcome Message -->
     <h1 class="title-page">
        Welcome To EduBuddy!
     </h1>

     <!-- Description -->
      <p class="information-text">
        The EduBuddy Website is a brilliant platform created to help students work together and support each other in their studies. At EduBuddy, working with others can really help everyone do better in their courses. It’s a place where you can meet other students, connect with each other, or find someone who is studying the same thing as you are.
      <br><br>
        Every student has different skills and knowledge, so EduBuddy encourages everyone to help each other out. This isn't just good for learning; it also helps us improve our teamwork, communication, and understanding of people. By helping each other understand tricky topics, clearing up any confusion, and giving friendly advice, studying becomes much more fun and effective.
      </p>
</body>
</html>