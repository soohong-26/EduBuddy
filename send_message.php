<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    // Retrieving and sanitising the inputs
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message_text = $_POST['message_text'];

    // SQL query to insert the message
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)"; // uses placeholders to securely insert data into the messages table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message_text);

    // Executing the query and handle response
    if ($stmt->execute()) {
        echo "Message sent!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>