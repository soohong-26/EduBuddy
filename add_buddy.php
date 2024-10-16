<?php
include 'database.php'; // Include your database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['username'];
$buddy_user_id = $_POST['buddy_user_id'];

// Check if the request is valid
if ($user_id == $buddy_user_id) {
    echo "You cannot add yourself as a buddy.";
    exit();
}

// Check if the buddy relationship already exists
$sql = "SELECT * FROM buddies WHERE user_id = ? AND buddy_user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $buddy_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "You are already buddies or the request is pending.";
    exit();
}

// Insert buddy request
$sql = "INSERT INTO buddies (user_id, buddy_user_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $buddy_user_id);

if ($stmt->execute()) {
    echo "Buddy request sent successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
