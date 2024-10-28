<?php
require 'database.php'; // Include the database connection

// Check if the user is logged in and is a mentor
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Handle role change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_role'], $_POST['user_id'])) {
    $new_role = $_POST['new_role'];
    $user_id = $_POST['user_id'];
    $update_sql = "UPDATE users SET roles = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_role, $user_id);
    if ($update_stmt->execute()) {
        echo "<script>alert('Role has been successfully changed to " . htmlspecialchars($new_role) . ".');</script>";
    }
    $update_stmt->close();
}
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
        .user-list {
            padding: auto;
            margin: auto;
            list-style: none;
        }
        .user-item {
            display: flex;
            align-items: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: white;
        }
        .username, .email {
            font-weight: bold;
            margin-right: 10px;
            white-space: nowrap;
        }
        .username {
            color: #3498db;
        }
        .email {
            color: #e74c3c;
        }
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin-top: 100px;
            padding: 20px;
            text-align: center;
            font-weight: 800;
            font-size: 50px;
        }
        .profile-btn, .role-btn {
            padding: 6px 12px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        form {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        .role-selector {
            font-family: "Poppins", sans-serif;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>
    
    <!-- Display the users -->
    <?php
    $sql = "SELECT user_id, username, email, roles FROM users WHERE roles IN ('student', 'tutor')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<ul class='user-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "
            <li class='user-item'>
                <span class='username'>" . htmlspecialchars($row['username']) . "</span> - <span class='email'>" . htmlspecialchars($row['email']) . "</span>
                 
                <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to change the role?\");'>
                    <input type='hidden' name='user_id' value='" . $row['user_id'] . "'>
                    <select name='new_role' class='role-selector'>
                        <option value='student' " . ($row['roles'] === 'student' ? 'selected' : '') . ">Student</option>
                        <option value='tutor' " . ($row['roles'] === 'tutor' ? 'selected' : '') . ">Tutor</option>
                    </select>
                    <button type='submit' class='role-btn'>Change Role</button>
                </form> 

                <button class='profile-btn' onclick=\"location.href='chat.php?user_id=" . $row['user_id'] . "'\">Chat with " . htmlspecialchars($row['username']) . "</button>
                <button class='profile-btn' onclick=\"location.href='user_profile.php?user_id=" . $row['user_id'] . "'\">View Profile</button>
            </li>";
        }
        echo "</ul>";
    } else {
        echo "No users found.";
    }
    ?>
</body>
</html>
