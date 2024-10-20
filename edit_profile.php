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

        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
            margin: 20px;
            padding: 20px;
            color: white;
        }

        .profile-container {
            background: #3B4E61;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            max-width: 600px;
            margin: auto;
        }

        .profile-header {
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .profile-detail {
            margin: 18px 0;
        }

        .profile-label {
            font-weight: 600;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: block;
            margin: 15px auto 10px auto;
            object-fit: cover;
        }

        /* Container for the back button to center it */
        .button-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            width: 100%; /* Take full width of the profile container */
        }

        .back-button {
            font-family: "Poppins", sans-serif;
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 10px 0 19px 10px;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>

<div class="profile-container">

    <!-- Username -->
    <h2 class="profile-header"><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>

    <!-- Profile picture -->
    <?php 
    // Check if the profile image is not empty and exists
    $profileImagePath = !empty($user['profile_img']) ? $user['profile_img'] : "images/profile.png";
    ?>
    <img src="<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image" class="profile-img">

    <!-- Email -->
    <p class="profile-detail">
        <span class="profile-label">Email:</span> 
        <?php echo htmlspecialchars($user['email']); ?>
    </p>

    <!-- Roles (student/tutor) -->
    <p class="profile-detail">
        <span class="profile-label">Role:</span>
            <?php
            // Role indicator
            echo $user['roles'] === 'student' ? 'Student' : ($user['roles'] === 'mentor' ? 'Mentor' : 'Unknown Role');
            ?>
    </p>

    <!-- Strengths -->
    <p class="profile-detail">
        <span class="profile-label">Strengths:</span> 
        <?php echo htmlspecialchars($user['strengths']); ?>
    </p>

    <!-- Weakness -->
    <p class="profile-detail">
        <span class="profile-label">Weaknesses:</span> 
        <?php echo htmlspecialchars($user['weaknesses']); ?>
    </p>

    <!-- Extra Skills -->
    <p class="profile-detail">
        <span class="profile-label">Extra Skills:</span>
            <?php
            // Check if extra_skills is empty or null
            echo !empty($user['extra_skills']) ? htmlspecialchars($user['extra_skills']) : "None";
            ?>
    </p>

    <div class="button-container">
        <!-- Back button -->
        <button class="back-button" onclick="window.location.href = 'find_buddies.php'">
            Back To Study Buddy
        </button>

        <!-- Editing Profile Picture -->
        <form method="POST" action="edit_profile_picture.php" enctype="multipart/form-data">
            <label for="profile_image" class="back-button" style="cursor: pointer;">Change Profile Picture</label>
            <input type="file" id="profile_image" name="profile_image" style="display: none;" onchange="this.form.submit()">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
        </form>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>