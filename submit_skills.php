<!-- PHP -->
<?php

// Include the database connection
require 'database.php'; 

if (!isset($_SESSION['username'])) {
    // Redirect if not logged in
    header('Location: login.php'); 
    exit;
}

// Fetch the logged-in user's username
$username = $_SESSION['username']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Convert array to string
    $strengths = isset($_POST['strengths']) ? implode(',', $_POST['strengths']) : ''; 
    // Convert array to string
    $weaknesses = isset($_POST['weaknesses']) ? implode(',', $_POST['weaknesses']) : ''; 
    $extra_skills = htmlspecialchars($_POST['extra_skills']);

    $sql = "INSERT INTO skills (username, strengths, weaknesses, extra_skills) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE strengths = VALUES(strengths), weaknesses = VALUES(weaknesses), extra_skills = VALUES(extra_skills)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $strengths, $weaknesses, $extra_skills);
    if ($stmt->execute()) {
        // Redirect to find buddies page on successful insertion
        header("Location: find_buddies.php"); 
        exit;
    } else {
        // Display SQL errors if any
        echo "SQL Error: " . $stmt->error; 
    }
    $stmt->close();
}

    // Fetch existing strengths and weaknesses for the user
    $strengths_display = 'None';
    $weaknesses_display = 'None';

    $query = "SELECT strengths, weaknesses FROM skills WHERE username = ? ORDER BY id DESC LIMIT 1";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $strengths_display = !empty($data['strengths']) ? $data['strengths'] : 'None';
            $weaknesses_display = !empty($data['weaknesses']) ? $data['weaknesses'] : 'None';
        }
        $stmt->close();
    }

?>

<!-- HTML -->
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
            margin: 0 0 20px 25px;
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
            font-size: 18px;
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

        .sub-title {
            font-size: 18px;
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
            padding: 8px 8px 8px 8px;
            margin: 5px 0 5px 0;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: var(--text);
            font-size: 14px;
        }

        .extra-skills-button {
            width: 150px;
            padding: 7px;
            margin: 4px 0 10px 0;
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
            margin: 0 8px 0 0;
            background-color: rgba(0, 99, 158, .4);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .toggle-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        .active-button {
            background-color: rgba(0, 136, 169, 1);
        }

        /* Button Container */
        .button-container {
            margin: 0 0 15px 25px;
        }

        /* Flex Container for strengths and weaknesses */
        .strengths-container, .weaknesses-container {
            display: flex;
            justify-content: space-between; /* Ensures spacing between columns */
            margin-bottom: 20px;
        }

        /* Column styling */
        .strength-column, .weakness-column {
            flex: 1; /* Each column takes equal space */
            padding: 10px; /* Adds padding around the content of each column */
        }

        /* Statistics */
        .stats-container {
            background-color: #333;
            color: #ffffff; 
            padding: 10px; 
            margin: 10px 0 0 5px; 
            border-radius: 5px; 
            display: inline-block; 
            width: auto; 
        }

        .stats-container strong {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="submit_skills.php" class="toggle-button <?php echo basename(__FILE__) == 'submit_skills.php' ? 'active-button' : ''; ?>">Submit Your Skills</a>
        <a href="find_buddies.php" class="toggle-button <?php echo basename(__FILE__) == 'find_buddies.php' ? 'active-button' : ''; ?>">View Study Buddies</a>

        <div class="stats-container">
            <strong>Current Strengths:</strong> <?php echo $strengths_display; ?>
        </div>

        <div class="stats-container">
            <strong>Current Weaknesses:</strong> <?php echo $weaknesses_display; ?>
        </div>
    </div>

    <h2 class="title-page">Fill Out Your Skills to Find Study Buddies</h2>
    <form action="" method="POST" class="skills-form">
        <!-- Input fields for strengths, weaknesses, and extra skills -->
        <div>
            <!-- Strengths -->
            <label>Strengths:</label>

            <div class="strengths-container">
                <div class="strength-column">
                    <!-- Art and Design -->
                    <div>
                        <h3 class="sub-title">Art and Design</h3>
                            <input type="checkbox" name="strengths[]" value="Art and Design History">Art and Design History<br>
                            <input type="checkbox" name="strengths[]" value="3D Design">3D Design<br>
                            <input type="checkbox" name="strengths[]" value="Design Elements">Design Elements<br>
                            <input type="checkbox" name="strengths[]" value="Design Principles">Design Principles<br>
                            <input type="checkbox" name="strengths[]" value="Drawing">Drawing<br>
                            <input type="checkbox" name="strengths[]" value="Digital Graphics">Digital Graphics<br>
                            <input type="checkbox" name="strengths[]" value="Photography">Photography<br>
                            <input type="checkbox" name="strengths[]" value="Painting and Printing Techniques">Painting and Printing Techniques<br>
                    </div>
                </div>

                <div class="strength-column">
                    <!-- Biotechnology and Life Science -->
                    <div>
                        <h3 class="sub-title">Biotechnology and Life Science</h3>
                            <input type="checkbox" name="strengths[]" value="Biology">Biology<br>
                            <input type="checkbox" name="strengths[]" value="Chemistry">Chemistry<br>
                            <input type="checkbox" name="strengths[]" value="Biotechnology">Biotechnology<br>
                            <input type="checkbox" name="strengths[]" value="Mathematics and Statistics">Mathematics and Statistics<br>
                            <input type="checkbox" name="strengths[]" value="Biochemistry">Biochemistry<br>
                            <input type="checkbox" name="strengths[]" value="Argrobiotechnology">Argrobiotechnology<br>
                    </div>
                </div>

                <div class="strength-column">
                    <!-- Business -->
                    <div>
                        <h3 class="sub-title">Business</h3>
                            <input type="checkbox" name="strengths[]" value="Accounting">Accounting<br>
                            <input type="checkbox" name="strengths[]" value="Ethics">Ethics<br>
                            <input type="checkbox" name="strengths[]" value="Entrepreneurship">Entrepreneurship<br>
                            <input type="checkbox" name="strengths[]" value="Financial Management">Financial Management<br>
                            <input type="checkbox" name="strengths[]" value="Human Resources">Human Resources<br>
                            <input type="checkbox" name="strengths[]" value="Information Management">Information Management<br>
                            <input type="checkbox" name="strengths[]" value="Marketing Principles">Marketing Principles<br>
                            <input type="checkbox" name="strengths[]" value="Operation Management">Operation Management<br>
                            <input type="checkbox" name="strengths[]" value="Leadership in Organisation">Leadership in Organisation<br>
                            <input type="checkbox" name="strengths[]" value="Business Mathematics">Business Mathematics<br>
                            <input type="checkbox" name="strengths[]" value="Marketing">Marketing<br>
                    </div>
                </div>

                <div class="strength-column">
                    <!-- Computing and IT -->
                    <div>
                        <h3 class="sub-title">Computing and IT</h3>
                            <input type="checkbox" name="strengths[]" value="Programming">Programming<br>
                            <input type="checkbox" name="strengths[]" value="Discrete Mathematics">Discrete Mathematics<br>
                            <input type="checkbox" name="strengths[]" value="Internet Technology and Application">Internet Technology and Application<br>
                            <input type="checkbox" name="strengths[]" value="Database Management">Database Management<br>
                            <input type="checkbox" name="strengths[]" value="Networking">Networking<br>
                            <input type="checkbox" name="strengths[]" value="System Analysis and Design">System Analysis and Design<br>
                            <input type="checkbox" name="strengths[]" value="Rapid Application Development">Rapid Application Development<br>
                            <input type="checkbox" name="strengths[]" value="Computer Architecture">Computer Architecture<br>
                            <input type="checkbox" name="strengths[]" value="Data Structures">Data Structures<br>
                            <input type="checkbox" name="strengths[]" value="Software Engineering">Software Engineering<br>
                            <input type="checkbox" name="strengths[]" value="IT Project Management">IT Project Management<br>
                    </div>
                </div>
                
                <div class="strength-column">
                    <!-- Engineering -->
                    <div>
                        <h3 class="sub-title">Engineering</h3>
                            <input type="checkbox" name="strengths[]" value="Electrical Circuits">Electrical Circuits<br>
                            <input type="checkbox" name="strengths[]" value="Engineering Mathematics">Engineering Mathematics<br>
                            <input type="checkbox" name="strengths[]" value="Fluid Mechanics">Fluid Mechanics<br>
                            <input type="checkbox" name="strengths[]" value="Thermodynamics">Thermodynamics<br>
                            <input type="checkbox" name="strengths[]" value="Design of Machine Elements">Design of Machine Elements<br>
                            <input type="checkbox" name="strengths[]" value="Engineering Geology">Engineering Geology<br>
                            <input type="checkbox" name="strengths[]" value="Structural Analysis">Structural Analysis<br>
                            <input type="checkbox" name="strengths[]" value="Civil Engineering Materials">Civil Engineering Materials<br>
                            <input type="checkbox" name="strengths[]" value="Digital Electronics">Digital Electronics<br>
                            <input type="checkbox" name="strengths[]" value="Control Systems">Control Systems<br>
                    </div>
                </div>
            </div>
        </div> 

        <!-- Weaknesses -->
        <div>
            <label>Weaknesses:</label>
            <div class="weaknesses-container">
                <div class="weakness-column">
                    <!-- Art and Design -->
                    <div>
                        <h3 class="sub-title">Art and Design</h3>
                            <input type="checkbox" name="weaknesses[]" value="Art and Design History">Art and Design History<br>
                            <input type="checkbox" name="weaknesses[]" value="3D Design">3D Design<br>
                            <input type="checkbox" name="weaknesses[]" value="Design Elements">Design Elements<br>
                            <input type="checkbox" name="weaknesses[]" value="Design Principles">Design Principles<br>
                            <input type="checkbox" name="weaknesses[]" value="Drawing">Drawing<br>
                            <input type="checkbox" name="weaknesses[]" value="Digital Graphics">Digital Graphics<br>
                            <input type="checkbox" name="weaknesses[]" value="Photography">Photography<br>
                            <input type="checkbox" name="weaknesses[]" value="Painting and Printing Techniques">Painting and Printing Techniques<br>
                    </div>
                </div>

            <div class="weakness-column">
                <!-- Biotechnology and Life Science -->
                <div>
                    <h3 class="sub-title">Biotechnology and Life Science</h3>
                        <input type="checkbox" name="weaknesses[]" value="Biology">Biology<br>
                        <input type="checkbox" name="weaknesses[]" value="Chemistry">Chemistry<br>
                        <input type="checkbox" name="weaknesses[]" value="Biotechnology">Biotechnology<br>
                        <input type="checkbox" name="weaknesses[]" value="Mathematics and Statistics">Mathematics and Statistics<br>
                        <input type="checkbox" name="weaknesses[]" value="Biochemistry">Biochemistry<br>
                        <input type="checkbox" name="weaknesses[]" value="Argrobiotechnology">Argrobiotechnology<br>
                </div>
            </div>

            <div class="weakness-column">
                <!-- Business -->
                <div>
                    <h3 class="sub-title">Business</h3>
                        <input type="checkbox" name="weaknesses[]" value="Accounting">Accounting<br>
                        <input type="checkbox" name="weaknesses[]" value="Ethics">Ethics<br>
                        <input type="checkbox" name="weaknesses[]" value="Entrepreneurship">Entrepreneurship<br>
                        <input type="checkbox" name="weaknesses[]" value="Financial Management">Financial Management<br>
                        <input type="checkbox" name="weaknesses[]" value="Human Resources">Human Resources<br>
                        <input type="checkbox" name="weaknesses[]" value="Information Management">Information Management<br>
                        <input type="checkbox" name="weaknesses[]" value="Marketing Principles">Marketing Principles<br>
                        <input type="checkbox" name="weaknesses[]" value="Operation Management">Operation Management<br>
                        <input type="checkbox" name="weaknesses[]" value="Leadership in Organisation">Leadership in Organisation<br>
                        <input type="checkbox" name="weaknesses[]" value="Business Mathematics">Business Mathematics<br>
                        <input type="checkbox" name="weaknesses[]" value="Marketing">Marketing<br>
                </div>
            </div>

            <div class="weakness-column">
                <!-- Computing and IT -->
                <div>
                    <h3 class="sub-title">Computing and IT</h3>
                        <input type="checkbox" name="weaknesses[]" value="Programming">Programming<br>
                        <input type="checkbox" name="weaknesses[]" value="Discrete Mathematics">Discrete Mathematics<br>
                        <input type="checkbox" name="weaknesses[]" value="Internet Technology and Application">Internet Technology and Application<br>
                        <input type="checkbox" name="weaknesses[]" value="Database Management">Database Management<br>
                        <input type="checkbox" name="weaknesses[]" value="Networking">Networking<br>
                        <input type="checkbox" name="weaknesses[]" value="System Analysis and Design">System Analysis and Design<br>
                        <input type="checkbox" name="weaknesses[]" value="Rapid Application Development">Rapid Application Development<br>
                        <input type="checkbox" name="weaknesses[]" value="Computer Architecture">Computer Architecture<br>
                        <input type="checkbox" name="weaknesses[]" value="Data Structures">Data Structures<br>
                        <input type="checkbox" name="weaknesses[]" value="Software Engineering">Software Engineering<br>
                        <input type="checkbox" name="weaknesses[]" value="IT Project Management">IT Project Management<br>
                </div>
            </div>
            
            <div class="weakness-column">
                <!-- Engineering -->
                <div>
                    <h3 class="sub-title">Engineering</h3>
                        <input type="checkbox" name="weaknesses[]" value="Electrical Circuits">Electrical Circuits<br>
                        <input type="checkbox" name="weaknesses[]" value="Engineering Mathematics">Engineering Mathematics<br>
                        <input type="checkbox" name="weaknesses[]" value="Fluid Mechanics">Fluid Mechanics<br>
                        <input type="checkbox" name="weaknesses[]" value="Thermodynamics">Thermodynamics<br>
                        <input type="checkbox" name="weaknesses[]" value="Design of Machine Elements">Design of Machine Elements<br>
                        <input type="checkbox" name="weaknesses[]" value="Engineering Geology">Engineering Geology<br>
                        <input type="checkbox" name="weaknesses[]" value="Structural Analysis">Structural Analysis<br>
                        <input type="checkbox" name="weaknesses[]" value="Civil Engineering Materials">Civil Engineering Materials<br>
                        <input type="checkbox" name="weaknesses[]" value="Digital Electronics">Digital Electronics<br>
                        <input type="checkbox" name="weaknesses[]" value="Control Systems">Control Systems<br>
                </div>
            </div>
        </div>
    </div>

    <!-- Extra skills section -->
    <div>
        <label>Extra Skills:</label>
        <input type="text" name="extra_skills" placeholder="Type your extra skills" class="extra-skills-placeholder">
    </div>

    <!-- Button -->
    <button type="submit" class="extra-skills-button">Submit</button>

    </form>
</body>
</html>
