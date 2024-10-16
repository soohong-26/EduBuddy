<!-- PHP -->
<?php

?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Header</title>
    <!-- CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            background-color: #212121;
        }
        
        /* Texts & Fonts */
        li, a, button {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 16px;
            color: #edf0f1;
            text-decoration: none;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            margin: 0 20px 0 20px ;
        }

        /* Logo */
        .header-title {
            cursor: pointer;
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
        }

        .header-title a {
            text-decoration: none;
            color: inherit;
            font-size: 20px;
            font-weight: 600;
        }

        .nav_links {
            list-style: none;
        }

        .nav_links li {
            display: inline-block;
            padding: 0px 20px;
        }

        .nav_links li .nav_anc {
            transition: all 0.3s ease;
        }

        .nav_links li a:hover {
            color: #7AA3CC;
        }
        
        /* Button */
        .logout-btn {
            padding: 9px 25px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 0 0 20px;
        }

        .logout-btn:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        .username-text {
            color: #eaf2e8;
            font-family: "Poppins", sans-serif;
            font-style: normal;
            margin: 0 0 0 10px;
        }
        
    </style>
</head>
<body>
    <header>
        <!-- Logo -->
        <h2 class="header-title">
            <a href="home_buddies.php">
                EduBuddy: Connecting Peers, Tutors, and Mentors
            </a>
        </h2>

        <!-- Navigation Panel -->
        <nav>
            <ul class="nav_links">
                <li><a class='nav_anc' href="submit_skills.php">Buddies</a></li>
                <li><a class='nav_anc' href="view_buddies.php">View Buddies</a></li>
                <!-- <li><a class='nav_anc' href="#">Achievements</a></li> -->
                <li><a class='nav_anc' href="#">Community</a></li>
            </ul>
        </nav>

        <!-- Extra Button -->
        <a href="logout.php" class="cta">
            <button class="logout-btn">Logout</button>
        </a>

        <!-- Display username or guest -->
        <div class="welcome-message">
            <?php
            if (isset($_SESSION['username'])) {
                echo "<p class='username-text'>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</p>";
            } else {
                echo "<p class='username-text'>Welcome, Guest!</p>";
            }
            ?>
        </div>
        
    </header>
</body>
</html>
