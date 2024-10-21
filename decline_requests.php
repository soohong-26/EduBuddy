<?php
include 'database.php'; // Include your database connection

$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session
$requester_id = $_GET['requester_id']; // ID of the requester

// Update the friend request status to declined
$update = "UPDATE friend_requests SET status = 'declined' WHERE requester_id = ? AND requestee_id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ii", $requester_id, $user_id);
$stmt->execute();

echo "<script> alert('Friend request declined!'); </script>";
?>
