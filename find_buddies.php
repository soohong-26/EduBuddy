<?php
require 'database.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username']; // Fetch the logged-in user's username
$matches = []; // Initialize matches array
$weaknesses = ''; // Initialize weaknesses variable

// Retrieve the user's weaknesses
$sql = "SELECT weaknesses FROM skills WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($user_data && isset($user_data['weaknesses'])) {
    $weaknesses = $user_data['weaknesses']; // Safely assign weaknesses if available
}

// Search for matches based on the user's weaknesses
if (!empty($weaknesses)) {
    $sql = "
        SELECT u.username, s.strengths, s.weaknesses
        FROM users u
        JOIN skills s ON u.username = s.username
        WHERE s.strengths LIKE CONCAT('%', ?, '%') AND u.username != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $weaknesses, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch and store the matched results
    while ($row = $result->fetch_assoc()) {
        $matches[] = $row; 
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Find Study Buddies</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 20px;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 20px;
        }

        /* Form Submission */
        .skills-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 0 25px 10px 25px;
        }

        label {
            margin: 10px 0 5px 0;
            display: block;
            font-weight: bold;
        }

        input[type="checkbox"], input[type="text"] {
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Output Buddies */
        .sub-title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 10px 0;
        }

        .buddies-list {
            margin: 20px 25px 10px 25px;
        }

        .buddies-list ul {
            list-style-type: none;
            padding: 0;
        }
        
        .buddies-list li {
            background: #fff;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .buddy-username {
            color: black;
            font-weight: bold;
            font-size: 20px;
        }

        .buddy-strength {
            color: black;
        }

        .buddy-weakness {
            color: black;
        }

        .buddy-none {
            color: black;
        }

        /* Extra Skills */
        .extra-skills-placeholder {
            width: 250px;
            padding: 5px;
            margin: 5px 0 5px 0;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: var(--text);
            font-size: 16px;
        }

        .extra-skills-button {
            width: 150px;
            padding: 10px;
            margin: 10px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-profile-button {
            width: 150px;
            padding: 10px;
            margin: 10px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* General Toggle Button Styling */
        .toggle-button {
            display: inline-block;
            width: 200px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .toggle-button:hover {
            background-color: #0056b3;
        }

        /* Active Button Styling */
        .active-button {
            background-color: #004C8C; /* Darker shade for the active page */
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Navigation Buttons -->
    <div>
        <a href="submit_skills.php" class="toggle-button <?php echo basename(__FILE__) == 'submit_skills.php' ? 'active-button' : ''; ?>">Submit Your Skills</a>
        <a href="find_buddies.php" class="toggle-button <?php echo basename(__FILE__) == 'find_buddies.php' ? 'active-button' : ''; ?>">View Study Buddies</a>
    </div>

    <h2 class="title-page">Your Study Buddies</h2>
    <div class="buddies-list">
        <ul>
            <?php if (!empty($matches)) : ?>
                <?php foreach ($matches as $match) : ?>
                    <li>
                        <strong class="buddy-username"><?php echo htmlspecialchars($match['username']); ?></strong><br>
                        <span class="buddy-strength">Strengths: <?php echo htmlspecialchars($match['strengths']); ?></span><br>
                        <span class="buddy-weakness">Weaknesses: <?php echo htmlspecialchars($match['weaknesses']); ?></span><br>
                        <form action="profile.php" method="GET">
                            <input type="hidden" name="username" value="<?php echo urlencode($match['username']); ?>">
                            <button type="submit" class="view-profile-button">View Profile</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li class="buddy-none">No matches found.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
