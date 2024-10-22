<?php
include 'database.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Begin transaction to ensure all or nothing operation
    $conn->begin_transaction();

    try {
        // SQL to delete friend connection from 'friends' table
        $query = "DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (friend_id = ? AND user_id = ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("iiii", $user_id, $friend_id, $user_id, $friend_id);
            $stmt->execute();
            $friends_deleted = $stmt->affected_rows;
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement for deleting friends.');
        }

        // SQL to delete any related friend requests
        $query_requests = "DELETE FROM friend_requests WHERE (requester_id = ? AND requestee_id = ?) OR (requestee_id = ? AND requester_id = ?)";
        $stmt_requests = $conn->prepare($query_requests);
        if ($stmt_requests) {
            $stmt_requests->bind_param("iiii", $user_id, $friend_id, $user_id, $friend_id);
            $stmt_requests->execute();
            $requests_deleted = $stmt_requests->affected_rows;
            $stmt_requests->close();
        } else {
            throw new Exception('Failed to prepare statement for deleting friend requests.');
        }

        // Commit the transaction if all operations are successful
        if ($friends_deleted > 0 || $requests_deleted > 0) {
            $conn->commit();
            echo "<script>alert('Friend and related requests deleted successfully!'); window.location.href='friend_list.php';</script>";
        } else {
            throw new Exception('No records updated - possible integrity issue.');
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        $conn->rollback();
        echo "<script>alert('" . $e->getMessage() . "'); window.location.href='friend_list.php';</script>";
    }
} else {
    // Redirect if the form wasn't submitted correctly
    header('Location: friend_list.php');
    exit;
}
?>
