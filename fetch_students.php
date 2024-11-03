<?php
require 'database.php';

$search = $_POST['search'] ?? '';

$sql = "SELECT u.user_id, u.username, u.email, u.roles, 
               COALESCE(SUM(CASE WHEN m.is_read = FALSE AND m.receiver_id = ? AND m.sender_id = u.user_id THEN 1 ELSE 0 END), 0) AS unread_count
        FROM users u
        LEFT JOIN messages m ON m.sender_id = u.user_id
        WHERE u.roles IN ('student', 'tutor') AND (u.username LIKE ? OR u.email LIKE ?)
        GROUP BY u.user_id, u.username, u.email, u.roles";

$stmt = $conn->prepare($sql);
$likeTerm = '%' . $search . '%';
$stmt->bind_param("iss", $_SESSION['user_id'], $likeTerm, $likeTerm);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<li class='user-item'>";
        $output .= "<span class='username'>" . htmlspecialchars($row['username']) . "</span> - ";
        $output .= "<span class='email'>" . htmlspecialchars($row['email']) . "</span>";

        $output .= "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to change the role?\");'>";
        $output .= "<input type='hidden' name='user_id' value='" . $row['user_id'] . "'>";
        $output .= "<select name='new_role' class='role-selector'>";
        $output .= "<option value='student'" . ($row['roles'] === 'student' ? ' selected' : '') . ">Student</option>";
        $output .= "<option value='tutor'" . ($row['roles'] === 'tutor' ? ' selected' : '') . ">Tutor</option>";
        $output .= "</select>";
        $output .= "<button type='submit' class='role-btn'>Change Role</button>";
        $output .= "</form>";

        $output .= "<button class='profile-btn' onclick=\"location.href='chat.php?user_id=" . $row['user_id'] . "'\">Chat with " . htmlspecialchars($row['username']) . " (" . $row['unread_count'] . ")</button>";
        $output .= "<button class='profile-btn' onclick=\"location.href='profile_view_only.php?username=" . $row['username'] . "'\">View Profile</button>";
        $output .= "</li>";
    }
} else {
    $output = "<div class='no-results'>No users found.</div>";
}

echo $output;
?>
