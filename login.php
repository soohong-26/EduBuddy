<!-- PHP -->
<?php 
// Calling required files
require 'config.php';


?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Login</title>
    <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
    <div class="login-container"></div>
        <!-- Floating box -->
        <div class="login-box">
            <!-- Logo -->
            <img src="images/red_logo_cropped.png" alt="EduBuddy Logo" class="logo">
            <!-- Title -->
            <h2>Login</h2>
            <!-- Input form -->
            <form id="loginForm">
                <div class="input-group">
                    <input type="email" id="loginEmail"  placeholder="Email" required>
                    <span class="icon">
                        <img src="icons/mail_white.png" alt="Email Icon">
                    </span>
                </div>
                <div class="input-group">
                    <input type="password" id="loginPassword"  placeholder="Password" required>
                    <span class="icon">
                        <img src="icons/lock_white.png" alt="Password Icon" id="loginPasswordIcon" class="password-icon">
                    </span>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p class="register-text">Don't have an account?
                <a href="register.php"> Register Now</a>
            </p>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="js/login-register.js"></script>
</body>
</html>