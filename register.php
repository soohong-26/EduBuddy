<?php
// Include database connection file
require 'database.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side check to ensure username has no spaces
    if (preg_match('/\s/', $username)) {
        echo "<script>alert('Username must not contain spaces.'); window.history.back();</script>";
        exit;
    }

    // Check if the username already exists
    $check_username_sql = "SELECT user_id FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($check_username_sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Username already exists, prompt user
            echo "<script>alert('Username already taken. Please choose another username.'); window.history.back();</script>";
        } else {
            // Check if the email already exists
            $check_email_sql = "SELECT user_id FROM users WHERE email = ?";
            if ($stmt = $conn->prepare($check_email_sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // Email already exists, prompt user
                    echo "<script>alert('Email already taken. Please use another email.'); window.history.back();</script>";
                } else {
                    // If username and email do not exist, proceed with registration
                    // Hash the password before saving it to the database
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // Define the default role for new users
                    $default_role = 'student';

                    // Define the default profile image for the new users
                    $profile_img = 'images/profile.png';

                    // Prepare the SQL insert statement including the roles column
                    $sql = "INSERT INTO users (username, email, password, profile_img, roles) VALUES (?, ?, ?, ?, ?)";

                    if ($stmt = $conn->prepare($sql)) {
                        // Bind parameters (s for string, the order matters)
                        $stmt->bind_param("sssss", $username, $email, $hashed_password, $profile_img, $default_role);

                        // Execute the prepared statement
                        if ($stmt->execute()) {
                            // Registration successful
                            echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
                        } else {
                            // If the SQL execution fails
                            echo "<script>alert('Something went wrong. Please try again later.');</script>";
                        }
                    } else {
                        // If the statement couldn't be prepared
                        echo "<script>alert('Something went wrong. Please try again later.');</script>";
                    }
                }
            } else {
                // If the statement couldn't be prepared
                echo "<script>alert('Something went wrong. Please try again later.');</script>";
            }
        }
    } else {
        // If the statement couldn't be prepared
        echo "<script>alert('Something went wrong. Please try again later.');</script>";
    }
    // Closing the statement
    $stmt->close();
}
// Closing the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Register</title>
    <!-- CSS -->
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
            <form id="registerForm" action="register.php" method="post">
                <!-- Email -->
                <div class="input-group">
                    <input type="email" id="registerEmail" placeholder="Email" name="email" required>
                    <span class="icon">
                        <img src="icons/mail.png" alt="Email Icon">
                    </span>
                </div>

                <!-- Username -->
                <div class="input-group">
                    <input type="text" id="registerUsername" placeholder="Username" name="username" required>
                    <span class="icon">
                        <img src="icons/user.png" alt="Password Icon">
                    </span>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <input type="password" id="registerPassword" placeholder="Password" name="password" required>
                    <span class="icon toggle-password">
                        <img src="icons/lock2.png" alt="Password Icon" id="registerPasswordIcon" class="password-icon">
                    </span>
                </div>

                <!-- Double Confirm Password -->
                <div class="input-group">
                    <input type="password" id="confirmPassword" placeholder="Reenter Password" name="check_password" required>
                    <span class="icon">
                        <img src="icons/lock2.png" alt="Password Icon" id="confirmPasswordIcon" class="password-icon">
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" name="submit">Register</button>
            </form>

            <!-- Register Link Page -->
            <p class="register-text">Already have an account?
                <a href="login.php"> Login Now</a>
            </p>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Registration Validation
        // Add an event listener for form submission
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            // Get form inputs
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Email validation pattern
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Username no white spaces validation
            const usernamePattern = /^\S*$/; // No spaces allowed

            // Validation checks
            if (!usernamePattern.test(username)) {
                alert("Username must not contain spaces.");
                event.preventDefault();
                return;
            }

            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                event.preventDefault(); // Stop form submission
                return;
            }

            if (password.length < 8) {
                alert("Password must be at least 8 characters long.");
                event.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault();
                return;
            }

            // Confirmation dialog for accurate information
            const confirmMessage = "Please ensure all information is accurate. Once the account is created, changes cannot be made. Do you wish to proceed?";
            if (!confirm(confirmMessage)) {
                event.preventDefault(); // Stop form submission if the user cancels
            }
        });

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
                icon.src = "icons/lock2.png"; // Change to lock icon
            }
        }

        // Add event listeners for each password field's icon
        const registerPasswordIcon = document.getElementById('registerPasswordIcon');
        const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

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
    </script>
</body>
</html>
