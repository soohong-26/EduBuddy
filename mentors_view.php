<?php
require 'database.php'; // Include the database connection

if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Handle role change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];

    // Validate the new role
    if (in_array($new_role, ['student', 'tutor'])) {
        $update_sql = "UPDATE users SET roles = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('si', $new_role, $user_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Role updated successfully.');</script>";
        } else {
            echo "<script>alert('Failed to update role. Please try again.');</script>";
        }

        $update_stmt->close();
    } else {
        echo "<script>alert('Invalid role selected.');</script>";
    }
}

// Fetch users along with their unread message count
$sql = "SELECT u.user_id, u.username, u.email, u.roles, 
               COALESCE(SUM(CASE WHEN m.is_read = FALSE AND m.receiver_id = ? AND m.sender_id = u.user_id THEN 1 ELSE 0 END), 0) AS unread_count
        FROM users u
        LEFT JOIN messages m ON m.sender_id = u.user_id
        WHERE u.roles IN ('student', 'tutor')
        GROUP BY u.user_id, u.username, u.email, u.roles";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Pass the current user's ID
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
        input[type="text"]#searchInput {
            padding: 15px;
            margin-left: 20px;
            border-radius: 10px;
            font-family: "Poppins", sans-serif;
            width: 300px;
        }
        .no-results {
            background-color: white;
            color: #212121; /* Adjust the color as needed */
            text-align: center;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#searchInput').on('keyup', function(){
                var value = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_students.php',
                    data: {search: value},
                    success: function(data){
                        $('#user-list').html(data);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div style="margin: 20px;">
        <input type="text" id="searchInput" placeholder="Search students or tutors..." autocomplete="off">
    </div>
    <ul id='user-list' class='user-list'>
    <?php if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li class='user-item'>";
            echo "<span class='username'>" . htmlspecialchars($row['username']) . "</span> - ";
            echo "<span class='email'>" . htmlspecialchars($row['email']) . "</span>";
            echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to change the role?\");'>";
            echo "<input type='hidden' name='user_id' value='" . $row['user_id'] . "'>";
            echo "<select name='new_role' class='role-selector'>";
            echo "<option value='student' " . ($row['roles'] === 'student' ? 'selected' : '') . ">Student</option>";
            echo "<option value='tutor' " . ($row['roles'] === 'tutor' ? 'selected' : '') . ">Tutor</option>";
            echo "</select>";
            echo "<button type='submit' class='role-btn'>Change Role</button>";
            echo "</form>";
            echo "<button class='profile-btn' onclick=\"location.href='chat.php?user_id=" . $row['user_id'] . "'\">Chat with " . htmlspecialchars($row['username']) . " (" . $row['unread_count'] . ")</button>";
            echo "<button class='profile-btn' onclick=\"location.href='profile_view_only.php?username=" . $row['username'] . "'\">View Profile</button>";
            echo "</li>";
        }
    } else {
        echo "No users found.";
    } ?>
    </ul>
</body>
</html>
