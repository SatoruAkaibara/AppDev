<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$is_from_cart = isset($_GET['from_cart']) && $_GET['from_cart'] == 1;
$items_to_buy = [];
$overall_base_price = 0; // This will be the sum of all item prices from cart
$shipping_fee = 100; // Default shipping fee
$order_summary_html = '';
$product_name_display_for_title = "Your Order";

if ($is_from_cart && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $items_to_buy = $_SESSION['cart'];
    $product_name_display_for_title = "Cart Checkout";
    $order_summary_html .= "<ul>";
    foreach ($items_to_buy as $item) {
        $item_total = $item['price']; // 'price' in cart is already item_unit_price * quantity
        $overall_base_price += $item_total;
        $order_summary_html .= "<li>" . htmlspecialchars($item['product_name_display']) . " (Size: " . htmlspecialchars($item['size']) . ") x " . htmlspecialchars($item['quantity']) . " - ₱" . number_format($item_total, 2) . "</li>";
    }
    $order_summary_html .= "</ul>";
    $overall_total_price = $overall_base_price + $shipping_fee;

    // For buy_result.php, we might want to pass the whole cart or a summary
    $_SESSION['buy_now_cart_summary'] = [ // New session variable for cart checkout
        'items' => $items_to_buy,
        'subtotal' => $overall_base_price,
        'shipping' => $shipping_fee,
        'total' => $overall_total_price
    ];
    unset($_SESSION['buy_now']); // Clear any single item buy_now

} elseif (isset($_SESSION['buy_now'])) {
    $single_item_details = $_SESSION['buy_now'];
    $product_name_display_for_title = $single_item_details['product'] ?? 'Unknown Product';
    $overall_base_price = $single_item_details['price']; // This is already item_unit_price * quantity
    $quantity = $single_item_details['quantity'];
    $overall_total_price = $overall_base_price + $shipping_fee;
    
    $order_summary_html = "<p>Product: " . htmlspecialchars($product_name_display_for_title) . "</p>";
    $order_summary_html .= "<p>Quantity: " . htmlspecialchars($quantity) . "</p>";
    if ($quantity > 0) {
        $order_summary_html .= "<p>Price per item (excl. size adj.): This info might need recalculation or to be stored differently if needed here.</p>";
    }
    $order_summary_html .= "<p>Subtotal for product(s): ₱" . number_format($overall_base_price, 2) . "</p>";
    unset($_SESSION['buy_now_cart_summary']); // Clear cart summary if doing single buy

} else {
    header("Location: home.php"); // Redirect if no product to buy
    exit();
}

// Slideshow images - for single item buy now, it uses $_SESSION['buy_now']['images']
// For cart, you might show a generic image or the first item's image
$productImages = ['image/placeholder.jpg']; // Default
if (!$is_from_cart && isset($single_item_details['images']) && !empty($single_item_details['images'])) {
    $productImages = $single_item_details['images'];
} elseif ($is_from_cart && !empty($items_to_buy) && isset($items_to_buy[0]['display_image'])) {
    $productImages = [$items_to_buy[0]['display_image']]; // Show first cart item image as example
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Purchase - <?= htmlspecialchars($product_name_display_for_title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <div class="slideshow-container" style="<?php if($is_from_cart && count($items_to_buy) > 1) echo 'display:none;'; /* Hide if many items */ ?>">
            <?php foreach ($productImages as $index => $imgPath): ?>
                <img src="<?php echo htmlspecialchars($imgPath); ?>" class="slides" style="<?php echo $index === 0 ? 'display:block;' : ''; ?>">
            <?php endforeach; ?>
            <?php if (count($productImages) > 1 && !$is_from_cart): ?>
            <a class="arrow left-arrow" onclick="plusSlides(-1)">&#10094;</a>
            <a class="arrow right-arrow" onclick="plusSlides(1)">&#10095;</a>
            <?php endif; ?>
        </div>

        <h2>Confirm Your Order</h2>

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

            <h3>Order Summary</h3>
            <div><?= $order_summary_html ?></div>
            <p>Shipping Fee: ₱<?= number_format($shipping_fee, 2); ?></p>
            <p><strong>Total Amount Due: ₱<?= number_format($overall_total_price, 2); ?></strong></p>

            <input type="hidden" name="is_cart_checkout" value="<?= $is_from_cart ? '1' : '0' ?>">
            <?php if (!$is_from_cart && isset($single_item_details)): ?>
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($single_item_details['product'] ?? '') ?>">
                <input type="hidden" name="size" value="<?= htmlspecialchars($single_item_details['size'] ?? ''); ?>">
                <input type="hidden" name="quantity" value="<?= htmlspecialchars($single_item_details['quantity'] ?? 1); ?>">
            <?php endif; ?>
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
        let slideIndex = 0;
        const slides = document.getElementsByClassName("slides");

        function showSlide(index) {
            if (slides.length === 0) return;
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[index].style.display = "block";
        }

        function plusSlides(n) {
            if (slides.length === 0) return;
            slideIndex = (slideIndex + n + slides.length) % slides.length;
            showSlide(slideIndex);
        }

        document.addEventListener('DOMContentLoaded', () => {
             if (slides.length > 0 && document.querySelector('.slideshow-container').style.display !== 'none') { // Only if slideshow is visible
                showSlide(slideIndex);
            }
        });

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>