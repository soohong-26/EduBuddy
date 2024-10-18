<?php
require 'database.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image']) && isset($_POST['username'])) {
    $username = $_POST['username'];
    $file = $_FILES['profile_image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading file.";
        exit;
    }

    // Validate file size and type here if needed
    $maxFileSize = 5 * 1024 * 1024; // 5 MB, adjust as needed
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($file['size'] > $maxFileSize) {
        echo "File size exceeds maximum limit of 5 MB.";
        exit;
    }

    if (!in_array($file['type'], $allowedMimeTypes)) {
        echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        exit;
    }

    // Generate a unique name for the image to avoid overwriting existing files
    $targetDirectory = "images/";
    $newFileName = $targetDirectory . uniqid('img_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

    // Move the uploaded file to your target directory
    if (move_uploaded_file($file['tmp_name'], $newFileName)) {
        // Update the user's profile image in the database
        $sql = "UPDATE users SET profile_img = ? WHERE LOWER(username) = LOWER(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $newFileName, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Profile image updated successfully!";
        } else {
            echo "No changes were made to your profile.";
        }
        $stmt->close();
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "Invalid request.";
}

// Redirect back to the profile page or handle differently
header('Location: edit_profile.php?username=' . urlencode($username));
exit;

// Close the database connection
$conn->close();
?>
