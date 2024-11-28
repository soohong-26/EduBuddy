<?php
include 'database.php';  // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data handling
    $user_id = $_POST['user_id'];
    $rated_by_user_id = $_POST['rated_by_user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $username = $_POST['username'];

    // Database inserting
    $sql = "INSERT INTO feedback (user_id, rated_by_user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $rated_by_user_id, $rating, $comment);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Success
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='friend_list.php';</script>";
    } else {
        // Failure
        echo "<script>alert('Something went wrong. Please try again later.'); window.location.href='friend_list.php';</script>";
    }
    $stmt->close();
    exit();
}
?>
