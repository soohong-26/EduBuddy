<?php
include 'database.php'; // Include your database connection

$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session
$requester_id = $_GET['requester_id']; // ID of the requester

// Update the friend request status to accepted
$update = "UPDATE friend_requests SET status = 'accepted' WHERE requester_id = ? AND requestee_id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ii", $requester_id, $user_id);
$stmt->execute();

// Insert into friends table
$insert = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("iiii", $user_id, $requester_id, $requester_id, $user_id);
$stmt->execute();

// Show an alert and then redirect to the pending requests page
echo "<script>alert('Friend request accepted!'); window.location.href='friend_list.php';</script>";
exit;
?>