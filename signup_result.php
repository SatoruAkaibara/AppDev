<?php
session_start();

$signup_successful = false;
$username_registered = "User"; // Default

// Check if user details were processed by signup.php
if (isset($_SESSION['temp_user_data_details']['username'])) {
    $username_registered = $_SESSION['temp_user_data_details']['username'];
    // We keep active_login_username and active_login_hashed_password in session for login.php
    // Clear the temp_user_data_details as it's no longer needed for auto-login here.
    unset($_SESSION['temp_user_data_details']);
    $signup_successful = true;

    $_SESSION['signup_success_redirect'] = true; // For a message on login page
    $_SESSION['registered_username'] = $username_registered; // For pre-filling login form
} else {
    // If accessed directly or temp data missing
    header("Location: signup.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Created Successfully</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <?php if ($signup_successful): ?>
            <h2>Congratulations, <?= htmlspecialchars($username_registered) ?>!</h2>
            <p>Your account has been successfully created.</p>
            <p>You can now log in with the credentials you provided.</p>
            <div class="button-group">
                <button type="button" class="button" onclick="window.location.href='login.php'">Go to Login Page</button>
            </div>
        <?php else: ?>
            <h2>Signup Process Incomplete</h2>
            <p>There was an issue with your signup. Please try again.</p>
            <div class="button-group">
                <button type="button" class="button" onclick="window.location.href='signup.php'">Back to Sign Up</button>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>Thank you for joining us!</p>
    </footer>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
