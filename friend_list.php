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

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Friend: " . $row['username'] . "<br>";
    }
} else {
    echo "You have no friends yet.";
}
?>
