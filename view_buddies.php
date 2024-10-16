<?php
include 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the buddies from the database
$sql = "SELECT users.username, users.profile_img FROM buddies
        JOIN users ON buddies.buddy_user_id = users.user_id
        WHERE buddies.user_id = ? AND buddies.status = 'accepted'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - View Buddies</title>
    <!-- CSS -->
     <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
        }
     </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>
        <h2>Your Buddies</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <li>
                    <img src="<?php echo $row['profile_img']; ?>" alt="Profile Image" width="50">
                    <?php echo $row['username']; ?>
                </li>
            <?php } ?>
        </ul>
    
</body>
</html>