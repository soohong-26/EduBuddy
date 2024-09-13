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
    <title>EduBuddy Register</title>
    <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
    <div class="login-container">
        <!-- Floating box -->
        <div class="login-box">
            <!-- Logo -->
            <img src="images/red_logo_cropped.png" alt="EduBuddy Logo" class="logo">
            <!-- Title -->
            <h2>Register</h2>
            <!-- Input form -->
            <form id="registerForm">
                <!-- Email -->
                <div class="input-group">
                    <input type="email" id="registerEmail" placeholder="Email" required>
                    <span class="icon">
                        <img src="icons/mail_white.png" alt="Email Icon">
                    </span>
                </div>
                <!-- Username -->
                <div class="input-group">
                    <input type="text" id="registerUsername" placeholder="Username" required>
                    <span class="icon">
                        <img src="icons/user_white.png" alt="Password Icon">
                    </span>
                </div>
                <!-- Password -->
                <div class="input-group">
                    <input type="password" id="registerPassword" placeholder="Password" required>
                    <span class="icon toggle-password">
                        <img src="icons/lock.png" alt="Password Icon" id="registerPasswordIcon" class="password-icon">
                    </span>
                </div>
                <!-- Double Confirm Password -->
                <div class="input-group">
                    <input type="password" id="confirmPassword" placeholder="Reenter Password" required>
                    <span class="icon">
                        <img src="icons/lock.png" alt="Password Icon" id="confirmPasswordIcon" class="password-icon">
                    </span>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="login-btn">Register</button>
            </form>
            <p class="register-text">Already have an account?
                <a href="login.php"> Login Now</a>
            </p>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="js/login-register.js"></script>
</body>
</html>
