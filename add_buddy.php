<?php
include 'database.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$buddy_user_id = $_POST['buddy_user_id'];

// Function to check is a user exists
function userExists($user_id, $conn) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Validate ser IDs before proceeding
if (!userExists($user_id, $conn) || !userExists($buddy_user_id, $conn)) {
    echo "One of the user IDs does not exist.";
    exit();
}

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
