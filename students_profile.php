<?php
require 'database.php'; // Include the database connection

// Access control checks
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Check if a user_id is provided in the query string
if (!isset($_GET['username'])) {
    echo "No student selected.";
    exit;
}

// Convert the username to lowercase to match case-insensitively
$lowerUsername = strtolower($username);

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
    WHERE LOWER(u.username) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $lowerUsername);
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
</head>
<body>
    <h1><?php echo htmlspecialchars($student['username']); ?>'s Profile</h1>
    <p>Email: <?php echo htmlspecialchars($student['email']); ?></p>
    <p>Profile Image: <img src="<?php echo htmlspecialchars($student['profile_img']); ?>" alt="Profile Image" style="width:150px; height:auto;"></p>
    <p>Role: <?php echo htmlspecialchars($student['roles']); ?></p>
    <p>Strengths: <?php echo htmlspecialchars($student['strengths']); ?></p>
    <p>Weaknesses: <?php echo htmlspecialchars($student['weaknesses']); ?></p>
    <p>Extra Skills: <?php echo htmlspecialchars($student['extra_skills']); ?></p>
</body>
</html>