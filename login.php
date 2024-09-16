<!-- PHP -->
<?php
// Include database connection file
require 'database.php';


?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Login</title>
    <!-- CSS -->
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

                <!-- Email -->
                <div class="input-group">
                    <input type="email" id="loginEmail"  placeholder="Email" required>
                    <span class="icon">
                        <img src="icons/mail.png" alt="Email Icon">
                    </span>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <input type="password" id="loginPassword"  placeholder="Password" required>
                    <span class="icon">
                        <img src="icons/lock.png" alt="Password Icon" id="loginPasswordIcon" class="password-icon">
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
    <script>
        // Toggle Password Visibility Function 
        function togglePasswordVisibility(passwordFieldId, iconId) {
            const passwordField = document.getElementById(passwordFieldId);
            const icon = document.getElementById(iconId);
            
            // Check current type of the password field
            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show the password
                icon.src = "icons/unlock.png"; // Change to unlock icon
            } else {
                passwordField.type = "password"; // Hide the password
                icon.src = "icons/lock.png"; // Change to lock icon
            }
        }

        // Add event listeners for each password field's icon
        const registerPasswordIcon = document.getElementById('registerPasswordIcon');
        const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
        const loginPasswordIcon = document.getElementById('loginPasswordIcon');

        if (registerPasswordIcon) {
            registerPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('registerPassword', 'registerPasswordIcon');
            });
        }

        if (confirmPasswordIcon) {
            confirmPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon');
            });
        }

        if (loginPasswordIcon) {
            loginPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('loginPassword', 'loginPasswordIcon');
            });
        }
    </script>
</body>
</html>