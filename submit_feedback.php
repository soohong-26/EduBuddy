<?php
include 'database.php';  // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $rated_by_user_id = $_POST['rated_by_user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $username = $_POST['username'];

    $sql = "INSERT INTO feedback (user_id, rated_by_user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $rated_by_user_id, $rating, $comment);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error submitting feedback: " . $conn->error;
    }
    $stmt->close();
    header("Location: friend_list.php"); 
    exit();
}
?>
