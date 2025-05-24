<?php
// Start the session to access session variables set by signup.php.
session_start();

// This page is intended to be accessed after a successful "signup" process from signup.php.
// It takes the temporary user data, sets up the main user session (effectively "logging them in"),
// and then clears the temporary data.

// Check if temporary user data from the signup process exists in the session.
if (isset($_SESSION['temp_user_data'])) {
    // "Log in" the user by setting the main session variables.
    // Set the 'username' for general logged-in status checks.
    $_SESSION['username'] = $_SESSION['temp_user_data']['username'];
    
    // Store the complete user data array (including the plain password from the temp data for this example).
    // In a real application with a database:
    // - The user would have been saved to the DB in signup.php (with a hashed password).
    // - This page might not be strictly necessary, or login.php would be used immediately.
    // - If auto-login is desired after signup, you'd typically set session variables based on
    //   the successfully inserted user ID or username, without needing to pass the plain password around.
    $_SESSION['user_data'] = $_SESSION['temp_user_data']; 
    
    // Clear the temporary user data from the session as it's no longer needed.
    unset($_SESSION['temp_user_data']);
} else {
    // If this page is accessed directly without going through the signup process
    // (i.e., 'temp_user_data' is not set), redirect the user back to the signup page.
    header("Location: signup.php");
    exit(); // Stop further script execution.
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Created</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <?php 
    // Include the common header (navigation, logo, etc.)
    // Now that $_SESSION['username'] is set, the header will show "Profile" and "Log Out".
    include 'header.php'; 
    ?>

    <main class="container">
        <h2>Congratulations!</h2>
        <p>You have successfully created an account.</p>
        <div class="button-group">
            <button type="button" class="button" id="btnLoginRedirect" onclick="window.location.href='home.php'">Go to Home Page</button>
        </div>
    </main>

    <footer>
        <p>Start exploring our collection today!</p>
    </footer>

    <script>
    // JavaScript function for logout confirmation.
    // This might be redundant if already handled by header.php.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }
    </script>
</body>
</html>