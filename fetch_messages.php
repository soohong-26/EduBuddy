<?php
include 'database.php';

if (isset($_SESSION['user_id']) && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Updated SQL query to include username and timestamp
    $sql = "SELECT m.message_id, m.message_text, m.timestamp, u.username AS sender
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.timestamp ASC";

    // Preparing and executing the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetching and formatting the messages
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $row['timestamp'] = date('Y-m-d H:i:s', strtotime($row['timestamp'])); // Format the timestamp if necessary
        $messages[] = $row;
    }
    // Output the messages as JSON
    echo json_encode($messages); 
    $stmt->close();
}
$conn->close();
?>