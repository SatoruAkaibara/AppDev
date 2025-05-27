<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$estimated_delivery = date('F j, Y', strtotime('+5 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['is_cart_checkout']) && $_POST['is_cart_checkout'] == '1') {
        unset($_SESSION['cart']); // Clear the entire cart
        unset($_SESSION['buy_now_cart_summary']); // Clear the cart summary
    } else {
        unset($_SESSION['buy_now']); // Clear single item buy_now
    }
} else {
    header("Location: home.php");
    exit();
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
    <?php include 'header.php'; ?>

    <main class="container">
        <h2>Thank You for Your Purchase!</h2>
        <p>Your order has been placed successfully.</p>
        <p>Estimated arrival of product is: <strong><?= htmlspecialchars($estimated_delivery); ?></strong></p>
        <div class="button-group">
            <a href="home.php" class="button">Back to Home Page</a>
        </div>
    </main>

    <footer>
        <p>Need help? Contact us at: store-email@gmail.com</p>
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