// Registration form logic
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = document.getElementById('registerEmail').value;
        const username = document.getElementById('registerUsername').value;
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Basic validation
        if (email === "" || username === "" || password === "") {
            alert("All fields must be filled!");
            return;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        // Save user info to local storage
        const user = {
            email: email,
            username: username,
            password: password
        };

        localStorage.setItem('user', JSON.stringify(user));
        alert("Registration successful! Redirecting to login page.");
        window.location.href = "login.php"; // Redirect to login paage
    });
}

// Login form logic
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // Retrieve the stored user information
        const storedUser = JSON.parse(localStorage.getItem('user'));

        // Basic validation
        if (email === "" || password === "") {
            alert("Please fill in all fields.");
            return;
        }

        // Check if the entered credentials match the stored user data
        if (storedUser && storedUser.email === email && storedUser.password === password) {
            alert("Login successful! Redirecting to dashboard.");
            // Redirect to a new page or dashboard
            window.location.href = "home.php"; // Home Page, after logging in successfully
        } else {
            alert("Invalid credentials. Please try again.");
        }
    });
}
