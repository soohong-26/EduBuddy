<?php
require 'database.php'; // Include the database connection

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Fetch logged-in user's username
$username = $_SESSION['username']; 

// Initialize weaknesses variable to avoid undefined warnings
$weaknesses = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and process form input
    $strengths = isset($_POST['strengths']) ? implode(',', array_map('htmlspecialchars', $_POST['strengths'])) : '';
    $weaknesses = isset($_POST['weaknesses']) ? implode(',', array_map('htmlspecialchars', $_POST['weaknesses'])) : '';
    $extra_skills = htmlspecialchars($_POST['extra_skills']);

    // Insert data into the skills table
    $sql = "INSERT INTO skills (username, strengths, weaknesses, extra_skills) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE strengths = ?, weaknesses = ?, extra_skills = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $strengths, $weaknesses, $extra_skills, $strengths, $weaknesses, $extra_skills);
    $stmt->execute();
    $stmt->close();

    $select = mysqli_query($conn, "SELECT * FROM skills WHERE username = '$username'") or die('query failed');

    if (mysqli_num_rows($select) > 0){
        header('location:study_buddies_dummy.php');
    } else {
        echo '<script>alert("You have submitted before!")</script>';
    }
}

// Fetch matching study buddies (weaknesses of the user matched to strengths of others)
$matches = [];
$weaknessArray = explode(',', $weaknesses);

// Build dynamic SQL with placeholders for each weakness
$placeholders = implode(' OR ', array_fill(0, count($weaknessArray), 'most_recent_skills.strengths LIKE ?'));

// Construct the query to select the most recent skill record for each user.
$sql = "
    SELECT u.username, most_recent_skills.strengths, most_recent_skills.weaknesses 
    FROM users u
    JOIN (
        SELECT s1.username, s1.strengths, s1.weaknesses
        FROM skills s1
        JOIN (
            SELECT username, MAX(id) AS max_id
            FROM skills
            GROUP BY username
        ) s2 ON s1.username = s2.username AND s1.id = s2.max_id
    ) most_recent_skills ON u.username = most_recent_skills.username
    WHERE ($placeholders) AND u.username != ?";

// Prepare the SQL statement for execution to prevent SQL injection
$stmt = $conn->prepare($sql);

// Bind each weakness to a wildcard pattern
$weaknessParams = array_map(fn($w) => "%$w%", $weaknessArray);
$weaknessParams[] = $username; // Add the current username as the last parameter for exclusion

// Dynamically bind parameters using call_user_func_array
$stmt->bind_param(str_repeat('s', count($weaknessParams)), ...$weaknessParams);

// Execute the prepared statement
$stmt->execute();

// Get the result set from the executed statement
$result = $stmt->get_result();

// Fetch each row from the result set and add to the matches array
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

// Close statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Find Study Buddies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            margin-top: 10px;
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
    </style>
</head>
<body>

<!-- Navigation Bar -->
<?php require 'header.php';?>

<h2 class="title-page">Fill Out Your Skills to Find Study Buddies</h2>
<form action="" class="skills-form" method="POST">
    <div>
        <label>Strengths:</label>
        <div>
            <input type="checkbox" name="strengths[]" value="Math"> Math
            <input type="checkbox" name="strengths[]" value="Science"> Science
            <input type="checkbox" name="strengths[]" value="Writing"> Writing
        </div>
    </div>
    <div>
        <label>Weaknesses:</label>
        <div>
            <input type="checkbox" name="weaknesses[]" value="Math"> Math
            <input type="checkbox" name="weaknesses[]" value="Science"> Science
            <input type="checkbox" name="weaknesses[]" value="Writing"> Writing
        </div>
    </div>
    <div>
        <label>Extra Skills:</label>
        <input type="text" name="extra_skills" placeholder="Type your extra skills">
    </div>
    <button type="submit">Submit</button>
</form>

<div class="buddies-list">
    <h3 class="sub-title-page">Your Study Buddies</h3>
    <ul>
        <?php if (!empty($matches)) : ?>
            <?php foreach ($matches as $match) : ?>
                <li>
                    <strong><?php echo htmlspecialchars($match['username']); ?></strong><br>
                    Strengths: <?php echo htmlspecialchars($match['strengths']); ?><br>
                    Weaknesses: <?php echo htmlspecialchars($match['weaknesses']); ?>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li>No matches found yet.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
