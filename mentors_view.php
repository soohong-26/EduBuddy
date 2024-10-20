<?php
require 'database.php'; // Include the database connection

// Check if the user is logged in and is a mentor
if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'mentor') {
    echo "Access Denied. You must be logged in as a mentor to view this page.";
    exit;
}

// Fetch all students from the database
$sql = "SELECT user_id, username, email FROM users WHERE roles = 'student'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Student List</h1>";
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='students_profile.php?user_id=" . $row['user_id'] . "'>" . htmlspecialchars($row['username']) . "</a> - " . htmlspecialchars($row['email']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "No students found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors View</title>
</head>
<body>
    <!-- Body content here -->
</body>
</html>
<?php $conn->close(); ?>
