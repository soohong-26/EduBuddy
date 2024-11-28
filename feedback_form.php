<?php
include 'database.php';  // Include your database connection file

// Get user_id from URL
if (!isset($_GET['user_id'])) {
    echo "User not specified.";
    exit;
}

$user_id = $_GET['user_id'];

// Fetch the username for display purposes
$sql = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback for <?php echo htmlspecialchars($user['username']); ?></title>
    <!-- CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .feedback-form-container {
            padding: 20px;
            background-color: #3B4E61;
            width: 350px; /* Increased width */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center; /* Center align text and form elements */
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-size: 16px; 
        }

        select, button {
            margin-bottom: 12px; 
            width: 100%; 
            padding: 12px; 
            font-size: 16px; 
            border-radius: 10px; 
            border: 1px solid #ccc; 
        }

        .comment-section {
            margin-bottom: 12px; 
            width: 92%; 
            padding: 12px; 
            font-size: 16px;
            border-radius: 10px; 
            border: 1px solid #ccc; 
        }

        select {
            appearance: none; 
            background-color: white; 
            color: #333;
        }

        textarea {
            resize: none; /* Prevent resizing */
        }

        /* Button */
        .submit-button {
            padding: 15px 25px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            font-family: "Poppins", sans-serif;
        }

        .submit-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }
    </style>
</head>
<body>
<div class="feedback-form-container">

    <!-- Title -->
    <h1>Achievement for <?php echo htmlspecialchars($user['username']); ?></h1>

    <!-- The Achievemnt Form -->
    <form action="submit_feedback.php" method="POST">

        <!-- User ID (Receiver) auto input -->
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

        <!-- User ID (Giver) auto input -->
        <input type="hidden" name="rated_by_user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

        <!-- Username auto input -->
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">

        <!-- Rating for the Achievement -->
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>

        <!-- Achievement title for that student -->
        <label for="comment">Achievement:</label>
        <textarea class="comment-section" name="comment" id="comment" rows="4" placeholder="Achievement title here" required></textarea>

        <!-- Submit Button -->
        <button type="submit" class="submit-button">Submit Achievement</button>
    </form>
</div>
</body>
</html>
