<!-- PHP -->
<?php
// Include database connection file
require 'database.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before saving it to the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Define the default role for new users
    $default_role = 'student';

    // Prepare the SQL insert statement including the roles column
    $sql = "INSERT INTO users (username, email, password, roles) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (s for string, the order matters)
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $default_role);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Registration successful, show success message in a pop-up
            echo "<script>
                    alert('Registration successful! You will now be redirected to the login page.');
                    window.location.href = 'login.php'; // Redirect to login page
                  </script>";
        } else {
            // Registration failed, show error message in a pop-up
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                  </script>";
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
}
?>

<!-- HTML -->
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
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Email validation pattern
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validation checks
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
