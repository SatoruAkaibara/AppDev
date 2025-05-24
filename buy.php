<?php
// Start the session to access session variables.
session_start();

// Ensure the user is logged in before allowing them to access this page.
// If not logged in, redirect to the login page.
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Stop further script execution.
}

// Retrieve product details that were stored in the session by 'product_detail.php' when "Buy Now" was clicked.
// The '??' null coalescing operator provides a default value (null) if 'buy_now' is not set.
$product_details_from_session = $_SESSION['buy_now'] ?? null;

// If there are no product details in the session (e.g., user accessed buy.php directly),
// redirect to the home page.
if (!$product_details_from_session) {
    header("Location: home.php");
    exit(); // Stop further script execution.
}

// Extract product information for display and calculation.
// Provide default values if certain keys are missing to prevent errors.
$productName = $product_details_from_session['product'] ?? 'Unknown Product'; // This is the display name.
$item_total_price = $product_details_from_session['price']; // This 'price' is already the calculated total for the item (unit_price * quantity).
$quantity = $product_details_from_session['quantity'];
$shipping_fee = 100; // Fixed shipping fee. Could be made dynamic later.
$overall_total_price = $item_total_price + $shipping_fee; // Calculate the final total.

// Retrieve product images for the slideshow, passed from product_detail.php via $_SESSION['buy_now'].
$productImages = $product_details_from_session['images'] ?? [];

// Fallback logic for images if the 'images' array wasn't passed or is empty.
// This attempts to construct image paths based on a naming convention (e.g., productkey_1.jpg).
if (empty($productImages)) {
    // Use 'image_prefix' or a lowercase version of 'product_key' if available.
    $product_image_prefix = $product_details_from_session['image_prefix'] ?? strtolower($product_details_from_session['product_key'] ?? 'unknown');
    for ($i = 1; $i <= 3; $i++) { // Assuming up to 3 images per product by convention.
        $imgPath = "images/" . $product_image_prefix . "_" . $i . ".jpg";
        // Check if the image file exists before adding to the array.
        // Ensure 'images/' directory exists and contains these product images.
        if (file_exists($imgPath)) {
            $productImages[] = $imgPath;
        }
    }
}
// If still no images are found, use placeholder images.
// Ensure 'images/placeholder.jpg' exists.
if (empty($productImages)) {
    $productImages = [
        'images/placeholder.jpg',
        'images/placeholder.jpg',
        'images/placeholder.jpg'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Product - <?= htmlspecialchars($productName) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Include the common header for the site.
    include 'header.php';
    ?>

    <main class="container">
        <div class="slideshow-container">
            <?php foreach ($productImages as $index => $imgPath): ?>
                <img src="<?php echo htmlspecialchars($imgPath); ?>" class="slides" style="<?php echo $index === 0 ? 'display:block;' : ''; ?>">
            <?php endforeach; ?>
            <?php if (count($productImages) > 1): ?>
            <a class="arrow left-arrow" onclick="plusSlides(-1)">&#10094;</a>
            <a class="arrow right-arrow" onclick="plusSlides(1)">&#10095;</a>
            <?php endif; ?>
        </div>

        <p><strong>Product:</strong> <?php echo htmlspecialchars($productName); ?></p>

        <form action="buy_result.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required value="<?= htmlspecialchars($_SESSION['user_data']['username'] ?? '') ?>"><br>

            <label for="address">Address:</label>
            <textarea name="address" id="address" required><?= htmlspecialchars($_SESSION['user_data']['address'] ?? '') ?></textarea><br>

            <label for="contact">Contact Number:</label>
            <input type="tel" name="contact" id="contact" pattern="[0-9]{11}" required value="<?= htmlspecialchars($_SESSION['user_data']['contact'] ?? '') ?>"><br>

            <label for="payment">Payment Method:</label>
            <select name="payment" id="payment" required>
                <option value="credit_card">Credit Card</option>
                <option value="cod">Cash on Delivery</option>
            </select><br>

            <h2>Order Summary</h2>
            <p>Product: <?php echo htmlspecialchars($productName); ?></p>
            <p>Quantity: <?php echo htmlspecialchars($quantity); ?></p>
            <?php if ($quantity > 0): // Avoid division by zero if quantity is somehow 0. ?>
            <p>Price per item: ₱<?php echo number_format($item_total_price / $quantity, 2); ?></p>
            <?php endif; ?>
            <p>Subtotal for product(s): ₱<?php echo number_format($item_total_price, 2); ?></p>
            <p>Shipping Fee: ₱<?php echo number_format($shipping_fee, 2); ?></p>
            <p><strong>Total: ₱<?php echo number_format($overall_total_price, 2); ?></strong></p>

            <input type="hidden" name="product_name" value="<?= htmlspecialchars($productName) ?>">
            <input type="hidden" name="size" value="<?= htmlspecialchars($product_details_from_session['size'] ?? ''); ?>">
            <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity ?? 1); ?>">
            <input type="hidden" name="total_price" value="<?= htmlspecialchars($overall_total_price); ?>">


            <div class="button-group">
                <button type="submit" id="buyButton" class="button">Confirm Purchase</button>
                <button type="button" id="backButton" class="button" onclick="window.history.back();">Back</button>
            </div>
        </form>
    </main>

    <footer>
        <p>Need help? Contact us at: store-email@gmail.com</p>
    </footer>

    <script>
        // JavaScript for the image slideshow.
        let slideIndex = 0; // Current slide index.
        const slides = document.getElementsByClassName("slides"); // Get all slide elements.

        // Function to show a specific slide.
        function showSlide(index) {
            if (slides.length === 0) return; // Do nothing if no slides.
            // Hide all slides.
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            // Display the selected slide.
            slides[index].style.display = "block";
        }

        // Function to change slide by a given number (n can be 1 or -1).
        function plusSlides(n) {
            if (slides.length === 0) return; // Do nothing if no slides.
            // Calculate new slide index, wrapping around if necessary.
            slideIndex = (slideIndex + n + slides.length) % slides.length;
            showSlide(slideIndex);
        }

        // When the DOM is fully loaded, show the initial slide.
        document.addEventListener('DOMContentLoaded', () => {
             if (slides.length > 0) {
                showSlide(slideIndex);
            }
        });

        // JavaScript function for logout confirmation (shared across pages).
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>