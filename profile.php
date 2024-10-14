<?php
require 'database.php'; // Include the database connection

// Check if a username is provided in the query string
if (!isset($_GET['username'])) {
    echo "No student selected.";
    exit;
}

$username = $_GET['username'];

// Convert the username to lowercase to match case-insensitively
$lowerUsername = strtolower($username);

// Fetch the user's details from the database
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

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $lowerUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}

// Fetch the user's details
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
            margin: 20px;
            padding: 20px;
        }

        .profile-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            max-width: 600px;
            margin: auto;
        }

        .profile-header {
            font-size: 24px;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .profile-detail {
            margin: 5px 0;
        }

        .profile-label {
            font-weight: bold;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        /* Back button for profile */
        .back-button {
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 0 8px 0 0;
            background-color: rgba(0, 99, 158, .4);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

    </style>
</head>
<body>

<div class="profile-container">
    <h2 class="profile-header"><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>

    <?php if (!empty($user['profile_img'])) : ?>
        <img src="<?php echo htmlspecialchars($user['profile_img']); ?>" alt="Profile Image" class="profile-img">
    <?php else : ?>
        <img src="default-profile.png" alt="Default Profile Image" class="profile-img">
    <?php endif; ?>

    <p class="profile-detail">
        <span class="profile-label">Email:</span> <?php echo htmlspecialchars($user['email']); ?>
    </p>
    <p class="profile-detail">
        <span class="profile-label">Role:</span> <?php echo htmlspecialchars($user['roles']); ?>
    </p>
    <p class="profile-detail">
        <span class="profile-label">Strengths:</span> <?php echo htmlspecialchars($user['strengths']); ?>
    </p>
    <p class="profile-detail">
        <span class="profile-label">Weaknesses:</span> <?php echo htmlspecialchars($user['weaknesses']); ?>
    </p>
    <p class="profile-detail">
        <span class="profile-label">Extra Skills:</span> <?php echo htmlspecialchars($user['extra_skills']); ?>
    </p>

    <!-- Back button -->
     <button class="back-button" onclick="window.location.href = 'find_buddies.php'">Back To Study Buddy</button>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
