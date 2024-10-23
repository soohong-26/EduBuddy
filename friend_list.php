<?php
include 'database.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

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

        /* Button Container */
        .button-container {
            margin: 0 0 15px 25px;
        }

        /* View Profile Button */
        .view-profile-button {
            background-color: rgba(0, 136, 169, 1);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .view-profile-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        /* Friend List Item */
        .friend-item {
            margin-bottom: 15px;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php';?>

    <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="friend_list.php" class="toggle-button <?php echo basename(__FILE__) == 'friend_list.php' ? 'active-button' : ''; ?>">Buddies List</a>
        <a href="pending_requests.php" class="toggle-button <?php echo basename(__FILE__) == 'pending_requests.php' ? 'active-button' : ''; ?>">Pending Requests</a>
    </div>
    
    <!-- Friend list -->
    <h2 class="title-page">Buddies List</h2>

    <div class="box-container">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="friend-item">';
                    echo htmlspecialchars($row['username']);
                    ?>

                    <!-- View Profile Button -->
                    <form action="profile_view_only.php" method="GET" style="display:inline; margin-left: 20px;">
                        <input type="hidden" name="username" value="<?php echo urlencode($row['username']); ?>">
                        <button type="submit" class="view-profile-button">View Buddies</button>
                    </form>

                    <!-- Delete button -->
                    <form action="delete_friend.php" method="POST" style="display:inline; margin-left: 10px;">
                        <input type="hidden" name="friend_id" value="<?php echo $row['user_id']; ?>">
                        <button type="submit" style="background-color: rgba(217, 83, 79, 1);" class="view-profile-button" onclick="return confirm('Are you sure you want to delete this friend?');">Delete Friend</button>
                    </form>

                    <?php
                    echo '</div>';
                }
            } else {
                echo "You have no friends yet.";
            }
        ?>
    </div>
</body>
</html>
