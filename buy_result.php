<?php
// Start the session to access session variables.
session_start();

// Ensure the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Stop further script execution.
}

// This page should typically be accessed after a successful purchase submission (POST request).
// In a real application, this is where you would:
// 1. Retrieve order details from $_POST or $_SESSION.
// 2. Validate the data.
// 3. Process payment (if not Cash on Delivery).
// 4. Save the order to a database.
// 5. Send confirmation emails.

// For this no-database example, we just confirm the "purchase" and clear relevant session data.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate order processing.
    // Estimate delivery date (e.g., 5 days from now).
    $estimated_delivery = date('F j, Y', strtotime('+5 days'));

    // Clear the specific 'buy_now' session variable as the purchase flow for this item is "complete".
    // This prevents the user from accidentally repurchasing the same item if they revisit 'buy.php'.
    unset($_SESSION['buy_now']);

    // If the purchase was for all items in a cart (not implemented in this specific 'buy_now' flow, but common),
    // you might also clear the entire cart here.
    // Example: unset($_SESSION['cart']);
} else {
    // If this page is accessed directly via URL (GET request) without a POST submission,
    // it means the purchase process wasn't followed. Redirect to the home page.
    header("Location: home.php");
    exit(); // Stop further script execution.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Include the common header for the site.
    include 'header.php';
    ?>

    <main class="container">
        <h2>Thank You for Your Purchase!</h2>
        <p>Your order has been placed successfully.</p>
        <p>Estimated arrival of product is: <strong><?php echo htmlspecialchars($estimated_delivery); ?></strong></p>
        
        <div class="button-group">
            <a href="home.php" class="button">Back to Home Page</a>
        </div>
    </main>

    <footer>
        <p>Need help? Contact us at: store-email@gmail.com</p>
    </footer>

    <script>
        // JavaScript function for logout confirmation (shared across pages).
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
