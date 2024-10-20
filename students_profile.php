<?php
require 'database.php'; // Include the database connection

// Access control checks
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Check if a username is provided in the query string
if (!isset($_GET['username'])) {
    echo "No student selected.";
    exit;
}

$username = $_GET['username'];

// Fetch the student's details from the database along with their skills
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
    WHERE LOWER(u.username) = LOWER(?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username); // Bind the username as a string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Student not found.";
    exit;
}

// Fetch the student's details
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #212121;
            color: white;
            margin: 0;
            padding: 20px;
        }
        .profile-container {
            background: #3B4E61;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: block;
            margin: 10px auto;
            object-fit: cover;
        }
        .profile-detail {
            margin: 10px 0;
            font-size: 16px;
        }
        .profile-label {
            font-weight: 600;
            color: #CCCCCC;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($student['profile_img']); ?>" alt="Profile Image" class="profile-img">
        <h1><?php echo htmlspecialchars($student['username']); ?>'s Profile</h1>
        <p class="profile-detail"><span class="profile-label">Email:</span> <?php echo htmlspecialchars($student['email']); ?></p>
        <p class="profile-detail"><span class="profile-label">Role:</span> <?php echo htmlspecialchars($student['roles']); ?></p>
        <p class="profile-detail"><span class="profile-label">Strengths:</span> <?php echo htmlspecialchars($student['strengths']); ?></p>
        <p class="profile-detail"><span class="profile-label">Weaknesses:</span> <?php echo htmlspecialchars($student['weaknesses']); ?></p>
        <p class="profile-detail"><span class="profile-label">Extra Skills:</span> <?php echo htmlspecialchars($student['extra_skills']); ?></p>
    </div>
</body>
</html>
<?php $conn->close(); ?>
