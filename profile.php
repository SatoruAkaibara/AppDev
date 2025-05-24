<?php
// Start the session to access user data.
session_start();

// Ensure the user is logged in and user data exists in the session.
// If not, redirect to the login page.
if (!isset($_SESSION['username']) || !isset($_SESSION['user_data'])) {
    header("Location: login.php");
    exit(); // Stop further script execution.
}

// Retrieve username and user data from the session.
// Username is assumed to be htmlspecialchars'd upon setting (e.g., during login/signup).
$username = $_SESSION['username']; 
// User data array. Assumed to be clean if set correctly during login/signup.
$user = $_SESSION['user_data']; 

// Handle profile image upload.
// This is a temporary upload; without a database, the change will only persist for the current session.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $targetDir = "uploads/"; // Directory to store uploaded images.
                             // IMPORTANT: Ensure this 'uploads/' directory exists in the same location as your PHP files
                             // and is writable by the web server (e.g., chmod 755 or 777, depending on server setup).
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist.
    }

    $fileName = basename($_FILES["profile_image"]["name"]); // Get the original filename.
    // Sanitize filename to prevent directory traversal and other security issues.
    // Allows alphanumeric characters, underscore, period, and hyphen.
    $safeFileName = preg_replace("/[^A-Za-z0-9_.\-]/", '', $fileName);
    if (empty($safeFileName)) $safeFileName = "uploaded_image.jpg"; // Default if filename becomes empty after sanitization.

    // Create a unique filename to prevent overwriting existing files.
    $targetFile = $targetDir . uniqid() . "_" . $safeFileName; 
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Get file extension.

    // Check if the uploaded file is an actual image.
    $check = false;
    if (isset($_FILES["profile_image"]["tmp_name"]) && is_uploaded_file($_FILES["profile_image"]["tmp_name"])) {
        // getimagesize() returns false if it's not a valid image.
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    }

    if ($check !== false) { // If it's a valid image.
        if ($_FILES["profile_image"]["size"] <= 2000000) { // Check file size (e.g., max 2MB).
            // Allow certain file formats.
            if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                // Try to move the uploaded file to the target directory.
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
                    // If upload is successful, update the profile image path in the SESSION only.
                    $_SESSION['user_data']['profile_image'] = $targetFile;
                    $_SESSION['upload_success'] = "Profile image updated! (This change is temporary for this session)";
                } else {
                    $_SESSION['upload_error'] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $_SESSION['upload_error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            $_SESSION['upload_error'] = "File is too large. Maximum 2MB.";
        }
    } else {
        $_SESSION['upload_error'] = "File is not an image or was not uploaded correctly.";
    }

    // Redirect back to the profile page to display messages and clear POST data.
    header("Location: profile.php");
    exit();
}

// Get the current profile image path from session.
// Fallback to a default image if not set or if the path is problematic.
// Ensure 'images/default_profile.png' exists.
$profileImage = $_SESSION['user_data']['profile_image'] ?? 'images/default_profile.png';
// Basic safety check for image path - though it's mostly controlled internally.
// If $profileImage could ever be user-influenced directly (not just through this script), more validation would be critical.
// For now, htmlspecialchars will handle output safety.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; // Include the common site header. ?>
    
    <main class="container profile-container">
        <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image" class="profile-image">

        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact']) ?></p>
        <p><strong>Birthday:</strong> <?= htmlspecialchars($user['birthday']) ?></p>

        <form method="POST" enctype="multipart/form-data" action="profile.php">
            <label for="profile_image">Change Profile Image:</label><br>
            <input type="file" name="profile_image" id="profile_image" accept="image/*" required>
            <br><button type="submit" class="button">Upload Image</button>
        </form>

        <?php // Display upload success or error messages. ?>
        <?php if (isset($_SESSION['upload_success'])): ?>
            <div class="upload-status success-message"><?= htmlspecialchars($_SESSION['upload_success']) ?></div>
            <?php unset($_SESSION['upload_success']); // Clear the message after displaying it. ?>
        <?php elseif (isset($_SESSION['upload_error'])): ?>
            <div class="upload-status error-message"><?= htmlspecialchars($_SESSION['upload_error']) ?></div>
            <?php unset($_SESSION['upload_error']); // Clear the message. ?>
        <?php endif; ?>

        <div class="button-group">
            <a href="cart.php" class="button">Your Cart</a>
             <a href="home.php" class="button">Back to Home</a>
        </div>
    </main>

    <footer>
        <p>Manage your account settings.</p>
    </footer>

    <script>
        // JavaScript function for logout confirmation.
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>