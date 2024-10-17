<!-- PHP -->
<?php
// Include the database connection
require 'database.php'; 

if (!isset($_SESSION['username'])) {
    // Redirect if not logged in
    header('Location: login.php'); 
    exit;
}

// Fetch the logged-in user's username
$username = $_SESSION['username']; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_desc']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $status = '1';
    date_default_timezone_set("Asia/Kuala_Lumpur");
    $current = date('Y-m-d H:i:s'); 
    $userid =  $_SESSION['user_id'];

    $sql = "INSERT INTO posts (post_title, user_id, short_desc, description, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $title, $userid, $short_desc, $desc, $current);
    if ($stmt->execute()) {
        // Redirect to find buddies page on successful insertion
        header("Location: community.php"); 
        exit;
    } else {
        // Display SQL errors if any
        echo "SQL Error: " . $stmt->error; 
    }
    $stmt->close();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Add Post</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 20px;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 25px;
        }

        /* Form Submission */
        .skills-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 0 25px 10px 25px;
        }

        label {
            margin: 10px 0 5px 0;
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        input[type="checkbox"], input[type="text"] {
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgba(0, 136, 169, .8);
        }

        .sub-title {
            font-size: 18px;
        }

        /* Output Buddies */
        .sub-title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 10px 0;
        }

        .buddies-list {
            margin: 20px 25px 10px 25px;
        }

        .buddies-list ul {
            list-style-type: none;
            padding: 0;
        }
        
        .buddies-list li {
            background: #fff;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .buddy-username {
            color: black;
            font-weight: bold;
            font-size: 20px;
        }

        .buddy-strength {
            color: black;
        }

        .buddy-weakness {
            color: black;
        }

        .buddy-none {
            color: black;
        }

        /* Extra Skills */
        .extra-skills-placeholder {
            width: 350px;
            padding: 8px 8px 8px 8px;
            margin: 5px 0 5px 0;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: var(--text);
            font-size: 14px;
        }

        .extra-skills-button {
            width: 150px;
            padding: 7px;
            margin: 4px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-profile-button {
            width: 150px;
            padding: 10px;
            margin: 10px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* General Toggle Button Styling */
        .toggle-button {
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 0 8px 0 0;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .toggle-button:hover {
            background-color: rgba(0, 136, 169, .8);
        }

        .active-button {
            background-color: rgba(0, 136, 169, .5);
        }

        /* Button Container */
        .button-container {
            margin: 0 0 15px 25px;
        }

        /* Adding flexbox to the strength container */
        .strengths-container {
            display: flex;
            justify-content: space-between; /* Ensures spacing between columns */
        }
         
        .strength-column {
            flex: 1; /* Each column takes equal space */
            padding: 10px; /* Adds padding around the content of each column */
        }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="community.php" class="toggle-button">Back</a>
    </div>

    <h2 class="title-page">Create New Post. Fill in the following.</h2>
    <form action="" method="POST" class="skills-form">
        <div>
            <label>Title:</label>
            <input type="text" name="title" placeholder="Type your topic" class="extra-skills-placeholder">
        </div>
        <div>
            <label>Short Description:</label>
            <textarea rows="8" type="text" name="short_desc" placeholder="Type your short description" class="extra-skills-placeholder"></textarea>
        </div>
        <div>
            <label>Description:</label>
            <textarea rows="8" type="text" name="desc" placeholder="Type your description" class="extra-skills-placeholder"></textarea>
        </div>
        <button type="submit" class="extra-skills-button">Submit</button>
    </form>
</body>
</html>
