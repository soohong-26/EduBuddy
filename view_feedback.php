<?php
include 'database.php';  // Include your database connection file

// Get user_id from URL
if (!isset($_GET['user_id'])) {
    echo "No user specified.";
    exit;
}

$user_id = $_GET['user_id'];

// Fetch the feedback details from the database
$sql = "SELECT f.rating, f.comment, f.created_at, u.username as rated_by_username FROM feedback f
        JOIN users u ON f.rated_by_user_id = u.user_id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}

$totalRating = array_sum(array_column($feedbacks, 'rating'));
$averageRating = count($feedbacks) > 0 ? $totalRating / count($feedbacks) : 0;

// Fetch the username of the profile being viewed
$sql_username = "SELECT username FROM users WHERE user_id = ?";
$stmt_username = $conn->prepare($sql_username);
$stmt_username->bind_param("i", $user_id);
$stmt_username->execute();
$result_username = $stmt_username->get_result();
$user = $result_username->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback for <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="feedback-container" style="padding: 20px; background-color: #3B4E61; color: white; width: 600px; margin: auto; border-radius: 10px;">
    <h1>Feedback for <?php echo htmlspecialchars($user['username']); ?></h1>
    <h3>Average Rating: <?php echo round($averageRating, 1); ?>/5 Stars</h3>
    <?php foreach ($feedbacks as $feedback): ?>
        <div class="feedback-entry">
            <p><strong>Rating:</strong> <?php echo $feedback['rating']; ?>/5 Stars</p>
            <p><strong>Comment:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>
            <p><strong>From:</strong> <?php echo htmlspecialchars($feedback['rated_by_username']); ?></p>
            <p><strong>Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($feedback['created_at'])); ?></p>
            <hr>
        </div>
    <?php endforeach; ?>
    <button onclick="window.history.back();" style="padding: 10px; background-color: #007BFF; color: white; border: none; border-radius: 5px;">Go Back</button>
</div>

</body>
</html>
