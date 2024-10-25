<?php
include 'database.php';  // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Assumes user ID is stored in session

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
    <!-- CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
            margin: 20px;
            padding: 20px;
            color: white;
        }

        .feedback-container {
            padding: 20px; 
            background-color: #3B4E61; 
            color: white; 
            width: 600px; 
            margin: auto; 
            border-radius: 10px;
        }

        .feedback-entry {
            padding: 0 0 0 0;
        }

        .back-button {
            font-family: "Poppins", sans-serif;
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 10px 0 19px 0;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .no-feedback{
            text-align: center;
            margin-top: 30px;
            font-weight: 400;
        }
    </style>
</head>
<body>

        <!-- Back button -->
        <button class="back-button" onclick="window.history.back();">
            Back
        </button>

        <div class="feedback-container">
    <h1>Feedback for <?php echo htmlspecialchars($user['username']); ?></h1>

    <?php if (count($feedbacks) > 0): ?>
        <!-- Average rating -->
        <h3>Average Rating: <?php echo round($averageRating, 1); ?>/5</h3>
        <hr>
        
        <?php foreach ($feedbacks as $feedback): ?>
            <div class="feedback-entry">
                <p><h2>Achievement:</h2> <?php echo htmlspecialchars($feedback['comment']); ?></p>
                <p><h2>From:</h2> <?php echo htmlspecialchars($feedback['rated_by_username']); ?></p>
                <p><h2>Date:</h2> <?php echo date("F j, Y, g:i a", strtotime($feedback['created_at'])); ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <hr>
        <!-- No feedback available -->
        <div class="no-feedback">
            No Achievements Yet
        </div>
    <?php endif; ?>
</div>

</body>
</html>
