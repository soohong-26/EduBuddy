<?php
include 'database.php'; // Include your database connection

$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session

$query = "SELECT u.user_id, u.username FROM users u
          INNER JOIN friends f ON u.user_id = f.friend_id
          WHERE f.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- HTML --> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Friend List</title>
    <!-- CSS -->
     <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 25px;
        }

        .box-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 0 25px 10px 25px;
        }
     </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php';?>
    
    <!-- Friend list -->
     <h2 class="title-page">
        Friend List
     </h2>

     <div class="box-container">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Friend: " . $row['username'] . "<br>";
                }
            } else {
                echo "You have no friends yet.";
            }
        ?>
     </div>
</body>
</html>