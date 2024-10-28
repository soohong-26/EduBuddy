<?php
require 'database.php'; // Include the database connection

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

// Fetch all mentors along with their unread message count
$sql = "SELECT u.user_id, u.username, u.email,
        COALESCE(SUM(CASE WHEN m.is_read = FALSE AND m.receiver_id = ? THEN 1 ELSE 0 END), 0) AS unread_count
        FROM users u
        LEFT JOIN messages m ON m.sender_id = u.user_id AND m.receiver_id = ?
        WHERE u.roles = 'mentor'
        GROUP BY u.user_id, u.username, u.email";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mentors</title>
    <!-- CSS -->
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
        }

        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 35px;
        }

        .mentor-list {
            list-style: none;
        }

        .mentor-item {
            display: flex;
            align-items: center;
            padding: 15px 10px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: white;
            width: 96.5%;
        }

        .username {
            font-weight: bold;
            margin-right: 10px;
            color: #3498db;
        }

        .email {
            color: #e74c3c;
        }

        .profile-btn {
            padding: 6px 12px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php'; ?>

    <!-- View mentors -->
    <h2 class="title-page">View Mentors</h2>
    
    <ul class='mentor-list'>
        <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <li class='mentor-item'>
                    <span class='username'><?php echo htmlspecialchars($row['username']); ?></span>
                    <span class='email'><?php echo htmlspecialchars($row['email']); ?></span>
                    <button class='profile-btn' onclick="location.href='chat.php?user_id=<?php echo $row['user_id']; ?>'">
                        Chat (<?php echo $row['unread_count']; ?>)
                    </button>
                </li>
            <?php } 
        } else {
            echo "No mentors found.";
        } ?>
    </ul>
</body>
</html>
