<!-- PHP -->
<?php
include 'database.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Pending Buddies</title>
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

        /* Button */
        .accept-btn {
            padding: 9px 25px;
            background-color: rgba(0, 179, 107, 1);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 10px 0 10px;
        }

        .accept-btn:hover {
            background-color: rgba(0, 179, 107, 0.8);
        }

        .decline-btn {
            padding: 9px 25px;
            background-color: rgba(217, 83, 79, 1);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .decline-btn:hover {
            background-color: rgba(217, 83, 79, 0.8);
        }

        /* View Profile Button */
        .view-profile-button {
            background-color: rgba(0, 136, 169, 1);
            color: white;
            padding: 8px 16px;
            margin: 10px 0 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .view-profile-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        /* Friend Request Item */
        .request-item {
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

    <!-- Title -->
    <h2 class="title-page">
        Pending Buddies Requests
    </h2>

    <div class="box-container">
        <?php
        $query = "SELECT u.user_id, u.username FROM users u
                  INNER JOIN friend_requests fr ON u.user_id = fr.requester_id
                  WHERE fr.requestee_id = ? AND fr.status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="request-item">';
                echo htmlspecialchars($row['username']) . " - ";
                ?>
                <!-- View Profile Button -->
                <form action="profile_view_only.php" method="GET" style="display:inline;">
                    <input type="hidden" name="username" value="<?php echo urlencode($row['username']); ?>">
                    <button type="submit" class="view-profile-button">View Profile</button>
                </form>

                <!-- Accept and Decline Buttons -->
                <a class='accept-btn' href='accept_requests.php?requester_id=<?php echo $row['user_id']; ?>'>Accept</a>
                <a class='decline-btn' href='decline_requests.php?requester_id=<?php echo $row['user_id']; ?>'>Decline</a>
                <?php
                echo '</div>';
            }
        } else {
            echo "No pending buddy requests.";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
