<!-- PHP -->
<?php
include 'database.php'; // Include your database connection

session_start();
$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session

$query = "SELECT u.user_id, u.username FROM users u
          INNER JOIN friend_requests fr ON u.user_id = fr.requester_id
          WHERE fr.requestee_id = ? AND fr.status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Pending Friend Requests:</h2>";
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

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Pending Friends</title>
</head>
<body>
    
</body>
</html>