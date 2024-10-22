<?php
include 'database.php';  // Include your database connection file

// Get user_id from URL
if (!isset($_GET['user_id'])) {
    echo "User not specified.";
    exit;
}

$user_id = $_GET['user_id'];

// Fetch the username for display purposes
$sql = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback for <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="feedback-form-container" style="padding: 20px; background-color: #3B4E61; color: white; width: 300px; margin: auto; border-radius: 10px;">
    <h1>Feedback for <?php echo htmlspecialchars($user['username']); ?></h1>
    <form action="submit_feedback.php" method="POST">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <input type="hidden" name="rated_by_user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
            <option value="1">1 Star</option>
            <option value="2">2 Stars</option>
            <option value="3">3 Stars</option>
            <option value="4">4 Stars</option>
            <option value="5">5 Stars</option>
        </select>
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" rows="4" placeholder="Leave a comment..."></textarea>
        <button type="submit" class="submit-button">Submit Feedback</button>
    </form>
</div>

</body>
</html>
