<?php
// --- SESSION START ---
// Always needed at the top if you're using $_SESSION variables on the page.
// This ensures user login status and other session data is available.
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Starts a new session or resumes an existing one.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Store</title> <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/favicon.png" type="image/png">
</head>
<body>
<header>
    <div class="header-content">
        <div class="logo">
            <a href="home.php">
                <img src="images/logo_main.png" alt="Our Store Logo">
            </a>
        </div>

        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="about.php">About Us</a></li>
                
                <?php // Checks if a 'username' is set in the session. ?>
                <?php if (!isset($_SESSION['username'])): ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php else: ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="#" onclick="confirmLogout()">Log Out</a></li> <?php // Triggers JavaScript logout confirm. ?>
                <?php endif; ?>
            </ul>
        </nav>

        <?php // Also shown only if user is logged in. ?>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="welcome-message">
                Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
    // LOGOUT CONFIRMATION FUNCTION:
    // This function is called by the "Log Out" link above.
    // It's defined here to ensure it's available whenever this header is included.
    if (typeof confirmLogout !== 'function') { // Define only if not already defined elsewhere.
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) { // Shows a confirmation pop-up.
                window.location.href = "logout.php"; // Redirects to 'logout.php' if user confirms.
            }
        }
    }
</script>