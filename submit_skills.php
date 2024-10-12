<?php
require 'database.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username']; // Fetch the logged-in user's username

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $strengths = isset($_POST['strengths']) ? htmlspecialchars($_POST['strengths']) : '';
    $weaknesses = isset($_POST['weaknesses']) ? htmlspecialchars($_POST['weaknesses']) : '';
    $extra_skills = htmlspecialchars($_POST['extra_skills']);

    // Insert data into the skills table or update existing entries
    $sql = "INSERT INTO skills (username, strengths, weaknesses, extra_skills) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE strengths = ?, weaknesses = ?, extra_skills = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $strengths, $weaknesses, $extra_skills, $strengths, $weaknesses, $extra_skills);
    $stmt->execute();
    $stmt->close();

    // Redirect to the search results page
    header("Location: find_buddies.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Submit Your Skills</title>
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

    <h2 class="title-page">Fill Out Your Skills to Find Study Buddies</h2>
    <form action="" method="POST" class="skills-form">
        <!-- Input fields for strengths, weaknesses, and extra skills -->
        <div>
            <label>Strengths:</label>
            <div>
            <input type="radio" name="strengths" value="American University Program">American University Program <br>
            <input type="radio" name="strengths" value="Art and Design">Art and Design <br>
            <input type="radio" name="strengths" value="Biotechnology and Life Science">Biotechnology and Life Science <br>
            <input type="radio" name="strengths" value="Business">Business <br>
            <input type="radio" name="strengths" value="Computing and IT">Computing and IT <br>
            <input type="radio" name="strengths" value="Computer Science">Computer Science <br>
            <input type="radio" name="strengths" value="Marketing">Marketing <br>
            <input type="radio" name="strengths" value="Engineering">Engineering <br>
            <input type="radio" name="strengths" value="Fashion Design">Fashion Design <br>
            <input type="radio" name="strengths" value="Management">Management <br>
            </div>
        </div>
        <div>
            <label>Weaknesses:</label>
            <div>
            <input type="radio" name="weaknesses" value="American University Program">American University Program <br>
            <input type="radio" name="weaknesses" value="Art and Design">Art and Design <br>
            <input type="radio" name="weaknesses" value="Biotechnology and Life Science">Biotechnology and Life Science <br>
            <input type="radio" name="weaknesses" value="Business">Business <br>
            <input type="radio" name="weaknesses" value="Computing and IT">Computing and IT <br>
            <input type="radio" name="weaknesses" value="Computer Science">Computer Science <br>
            <input type="radio" name="weaknesses" value="Marketing">Marketing <br>
            <input type="radio" name="weaknesses" value="Engineering">Engineering <br>
            <input type="radio" name="weaknesses" value="Fashion Design">Fashion Design <br>
            <input type="radio" name="weaknesses" value="Management">Management <br>
            </div>
        </div>
        <div>
            <label>Extra Skills:</label>
            <input type="text" name="extra_skills" placeholder="Type your extra skills">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
