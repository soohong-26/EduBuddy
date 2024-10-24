<?php
ini_set('display_errors', 'On'); // Turn on error displaying for debugging
error_reporting(E_ALL); // Report all errors for logging
require 'database.php'; // Include the database connection

if (!isset($_GET['username'])) {
    echo "No student selected.";
    exit;
}

$username = $_GET['username'];
$lowerUsername = strtolower($username); // Convert the username to lowercase to match case-insensitively

$sql = "
    SELECT u.username, u.email, u.profile_img, u.roles, s.strengths, s.weaknesses, s.extra_skills, f.rating
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
    LEFT JOIN feedback f ON u.user_id = f.user_id
    WHERE LOWER(u.username) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $lowerUsername);
$stmt->execute();
$result = $stmt->get_result();

$totalRating = 0;
$countRatings = 0;
$userDetails = []; // Changed variable name for clarity

while ($row = $result->fetch_assoc()) {
    if (empty($userDetails)) { // Fetch user details once
        $userDetails = $row; // Store all user details in an array
    }
    if (!is_null($row['rating'])) { // Ensure the rating is not null
        $totalRating += $row['rating'];
        $countRatings++;
    }
}

if ($countRatings > 0) {
    $averageRating = $totalRating / $countRatings;
} else {
    $averageRating = "No ratings";
}

$stmt->close();
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
        .profile-container {
            background: #3B4E61;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: auto;
            max-width: 600px;
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

        .button-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            width: 100%; /* Take full width of the profile container */
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

    
    <p class="profile-detail">
        <span class="profile-label">
            Average Rating:
        </span> 
        <?php echo is_numeric($averageRating) ? round($averageRating, 1) . '/5' : $averageRating; ?>
    </p>

    <div class="button-container">
        <button class="back-button" onclick="window.location.href='friend_list.php'">
            Back
        </button>
    </div>
    
</div>
</body>
</html>
<?php
$conn->close();
?>
