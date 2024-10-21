<!-- PHP -->
<?php
include 'database.php'; // Include your database connection
$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session

$query = "SELECT u.user_id, u.username FROM users u
          INNER JOIN friend_requests fr ON u.user_id = fr.requester_id
          WHERE fr.requestee_id = ? AND fr.status = 'pending'";
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
    <title>EduBuddy - Pending Friends</title>
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

        /* Button Container */
        .button-container {
            margin: 0 0 15px 25px;
        }

        /* General Toggle Button Styling */
        .toggle-button {
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 0 8px 0 0;
            background-color: rgba(0, 99, 158, .4);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .toggle-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        .active-button {
            background-color: rgba(0, 136, 169, 1);
        }

        
    </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php';?>

    <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="friend_list.php" class="toggle-button <?php echo basename(__FILE__) == 'friend_list.php' ? 'active-button' : ''; ?>">Friend List</a>
        <a href="pending_requests.php" class="toggle-button <?php echo basename(__FILE__) == 'pending_requests.php' ? 'active-button' : ''; ?>">Pending Requests</a>
    </div>

    <!-- Title -->
     <h2 class="title-page">
        Pending Friend Requests
     </h2>

    <div class="box-container">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "From: " . $row['username'] . " - ";
                    echo "<a href='accept_requests.php?requester_id=" . $row['user_id'] . "'>Accept</a> ";
                    echo "<a href='decline_requests.php?requester_id=" . $row['user_id'] . "'>Decline</a><br>";
                }
            } else {
                echo "No pending friend requests.";
            }
        ?>
    </div>
</body>
</html>