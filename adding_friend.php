<?php
ini_set('display_errors', 1); // Turn on error displaying
error_reporting(E_ALL); // Report all errors for debugging
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buddy_username'])) {
    $friend_username = $_POST['buddy_username'];

    // Check if the buddy_username exists in the users table
    $userCheckQuery = "SELECT user_id FROM users WHERE username = ?";
    $userStmt = $conn->prepare($userCheckQuery);
    $userStmt->bind_param("s", $friend_username);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows === 0) {
        // If the user does not exist, show an error and stop the execution
        die("<script> alert('The user you are trying to send a friend request to does not exists'); </script>");
    }

    // Get the friend_id from the result
    $friendRow = $userResult->fetch_assoc();
    $friend_id = $friendRow['user_id'];

    // Check if there is already a request or friendship
    $check = "SELECT * FROM friend_requests WHERE (requester_id = ? AND requestee_id = ?)
              OR (requester_id = ? AND requestee_id = ?)";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insert the friend request
        $insert = "INSERT INTO friend_requests (requester_id, requestee_id, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("ii", $user_id, $friend_id);
        if ($stmt->execute()) {
            echo "<script> alert('Friend request sent!'); </script>";
        } else {
            echo "<script> alert('Failed to send friend request'); </script>";
        }
    } else {
        echo "<script> alert('Friend request alreadt exists or you are already friends'); </script>";
    }

    header("Location: profile.php?username=" . urlencode($_GET['username']));
    exit;
} else {
    echo "<script> alert('Invalid request!'); </script>";
}
?>
