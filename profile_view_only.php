<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require 'database.php';

if (!isset($_GET['username'])) {
    echo "No student selected.";
    exit;
}

$username = $_GET['username'];
$lowerUsername = strtolower($username);

// Query for user details and latest skills
$sql = "
    SELECT u.username, u.email, u.profile_img, u.roles, s.strengths, s.weaknesses, s.extra_skills
    FROM users u
    LEFT JOIN (
        SELECT s1.username, s1.strengths, s1.weaknesses, s1.extra_skills
        FROM skills s1
        JOIN (
            SELECT username, MAX(id) AS max_id
            FROM skills
            GROUP BY username
        ) s2 ON s1.username = s2.username AND s1.id = s2.max_id
    ) s ON LOWER(u.username) = LOWER(s.username)
    WHERE LOWER(u.username) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $lowerUsername);
$stmt->execute();
$result = $stmt->get_result();

$userDetails = $result->fetch_assoc();
$stmt->close();

// Query for average rating
$sql_avg_rating = "SELECT AVG(f.rating) AS average_rating
                   FROM feedback f
                   JOIN users u ON f.user_id = u.user_id
                   WHERE LOWER(u.username) = ?";

$stmt_avg_rating = $conn->prepare($sql_avg_rating);
$stmt_avg_rating->bind_param('s', $lowerUsername);
$stmt_avg_rating->execute();
$result_avg_rating = $stmt_avg_rating->get_result();
$avg_rating_result = $result_avg_rating->fetch_assoc();
$averageRating = $avg_rating_result['average_rating'];
$stmt_avg_rating->close();

// Query for feedback
$sql_feedback = "SELECT f.rating, f.comment, f.created_at, u.username AS rated_by
                 FROM feedback f
                 JOIN users u ON f.rated_by_user_id = u.user_id
                 WHERE f.user_id = (SELECT user_id FROM users WHERE LOWER(username) = ?)";

$stmt_feedback = $conn->prepare($sql_feedback);
$stmt_feedback->bind_param('s', $lowerUsername);
$stmt_feedback->execute();
$result_feedback = $stmt_feedback->get_result();

$feedbacks = [];
while ($row = $result_feedback->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt_feedback->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userDetails['username']); ?>'s Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #212121;
            color: white;
            margin: 20px;
            padding: 20px;
        }
        .profile-container, .feedback-container {
            background: #3B4E61;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: auto;
            max-width: 600px;
            margin-top: 20px;
        }
        .profile-header {
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: block;
            margin: auto;
            object-fit: cover;
        }
        .profile-detail {
            margin: 18px 0;
        }
        .profile-label {
            font-weight: 600;
        }
        .feedback-entry {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
        }
        .back-button {
            font-family: "Poppins", sans-serif;
            display: inline-block;
            padding: 10px;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 200px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
<div class="profile-container">
    <h2 class="profile-header"><?php echo htmlspecialchars($userDetails['username']); ?>'s Profile</h2>
    
    <img src="<?php echo htmlspecialchars($userDetails['profile_img'] ?? "images/profile.png"); ?>" alt="Profile Image" class="profile-img">
    
    <p class="profile-detail">
        <span class="profile-label">
            Email:
        </span> 
        <?php echo htmlspecialchars($userDetails['email']); ?>
    </p>
    
    <p class="profile-detail">
        <span class="profile-label">
            Role:
        </span> 
        <?php echo htmlspecialchars($userDetails['roles'] ?? 'Unknown Role'); ?>
    </p>
    
    <p class="profile-detail">
        <span class="profile-label">
            Strengths:
        </span>
        <?php echo htmlspecialchars($userDetails['strengths']); ?>
    </p>
    
    <p class="profile-detail">
        <span class="profile-label">
            Weaknesses:
        </span> 
        <?php echo htmlspecialchars($userDetails['weaknesses']); ?>
    </p>
    
    <p class="profile-detail">
        <span class="profile-label">
            Extra Skills:
        </span> 
        <?php echo empty(trim($userDetails['extra_skills'])) ? "None" : htmlspecialchars($userDetails['extra_skills']); ?>
    </p>

    <!-- Show average rating -->
    <p class="profile-detail">
        <span class="profile-label">
            Average Rating:
        </span>
        <?php echo isset($averageRating) ? round($averageRating, 1) : 'No ratings yet'; ?>/5
    </p>


    <div class="button-container">
        <button class="back-button" onclick="window.history.back();">
                Back
        </button>
    </div>
</div>

<!-- Feedback Container -->
<div class="feedback-container">
    <h2 class="profile-header">Achievements</h2>
    <?php if (!empty($feedbacks)): ?>
        <?php foreach ($feedbacks as $feedback): ?>
            <div class="feedback-entry">
                <p><strong>Achievement:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>
                <p><strong>Rating:</strong> <?php echo $feedback['rating']; ?>/5</p>
                <p><strong>From:</strong> <?php echo htmlspecialchars($feedback['rated_by']); ?></p>
                <p><strong>Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($feedback['created_at'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No feedback available.</p>
    <?php endif; ?>
</div>

</body>
</html>
<?php $conn->close(); ?>
