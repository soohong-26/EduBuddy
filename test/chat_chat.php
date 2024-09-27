<?php
include 'db.php';
session_start();
$userId = $_SESSION['user_id'];
$friendId = $_GET['friend_id'];

// Sending a message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iis', $userId, $friendId, $message);
    $stmt->execute();
}

// Fetching messages
$messages = $conn->prepare("SELECT sender_id, message, created_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
$messages->bind_param('iiii', $userId, $friendId, $friendId, $userId);
$messages->execute();
$messages->bind_result($senderId, $message, $timestamp);
?>

<!-- Chat Window -->
<div>
    <ul>
        <?php while ($messages->fetch()) { ?>
            <li><?php echo ($senderId == $userId) ? 'You' : 'Friend'; ?>: <?php echo $message; ?> <small><?php echo $timestamp; ?></small></li>
        <?php } ?>
    </ul>
</div>

<!-- Message Input -->
<form method="POST">
    <input type="text" name="message" placeholder="Type your message" required>
    <button type="submit">Send</button>
</form>
