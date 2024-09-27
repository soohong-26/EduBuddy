<?php
include 'db.php';
session_start();
$userId = $_SESSION['user_id'];

// Sending a friend request
if (isset($_POST['friend_id'])) {
    $friendId = $_POST['friend_id'];

    $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param('ii', $userId, $friendId);
    $stmt->execute();
}

// Accepting friend request
if (isset($_POST['accept_request'])) {
    $friendId = $_POST['friend_id'];
    $stmt = $conn->prepare("UPDATE friends SET status = 'accepted' WHERE user_id = ? AND friend_id = ?");
    $stmt->bind_param('ii', $friendId, $userId);
    $stmt->execute();
}

// List of friends
$friends = $conn->prepare("SELECT users.id, users.username FROM users JOIN friends ON users.id = friends.friend_id WHERE friends.user_id = ? AND friends.status = 'accepted'");
$friends->bind_param('i', $userId);
$friends->execute();
$friends->bind_result($friendId, $friendName);
?>

<!-- List of Friends -->
<ul>
<?php while ($friends->fetch()) { ?>
    <li><?php echo $friendName; ?> <a href="chat.php?friend_id=<?php echo $friendId; ?>">Chat</a></li>
<?php } ?>
</ul>
