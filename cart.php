<?php
// Start the session to access session variables.
session_start();

// Redirect to login page if the user is not logged in.
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Stop further script execution.
}

// Get cart items from the session. If the cart is not set, default to an empty array.
$cart = $_SESSION['cart'] ?? [];


$product_definitions_for_cart = [
    "shirt" => ["name" => "Shirt", "image_prefix" => "shirt"],
    "socks" => ["name" => "Socks", "image_prefix" => "socks"],
    "short" => ["name" => "Short", "image_prefix" => "short"],
    "jeans" => ["name" => "Jeans", "image_prefix" => "jeans"],
    "trousers" => ["name" => "Trousers", "image_prefix" => "trousers"]
    // Add other products here if they can be added to the cart and need this definition.
];


// Handle remove item request if the 'remove_item' POST variable is set.
if (isset($_POST['remove_item'])) {
    // Get the index of the item to remove from the cart. Cast to integer for safety.
    $indexToRemove = (int)$_POST['remove_item'];
    
    // Check if the item at the given index exists in the session cart.
    if (isset($_SESSION['cart'][$indexToRemove])) {
        // Remove the item from the cart array.
        unset($_SESSION['cart'][$indexToRemove]);
        
        // Re-index the array to maintain consecutive numerical keys.
        // This is important for consistent access and display.
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    // Redirect to the same page (cart.php) to prevent form resubmission on page refresh.
    // This is a common pattern (POST/Redirect/GET).
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit; // Stop further script execution.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Include the common header for the site.
    include 'header.php';
    ?>

    <main class="container">
        <h2>Your Cart</h2>

        <input type="text" class="search-input" placeholder="Search in your cart..." onkeyup="filterCart(this.value)">
        
        <?php if (empty($cart)): ?>
          <p class="no-items">You do not have items in your cart for now.</p>
        <?php endif; ?>

        <div id="cart-list">
            <?php
            $overall_total = 0; // Initialize overall total price for the cart.
            // Loop through each item in the cart.
            foreach ($cart as $index => $item):
                // Get the product key (e.g., "shirt", "socks") stored in the cart item.
                $product_key_in_cart = $item['product'] ?? ''; 
                // Get the display name. Use 'product_name_display' if available, otherwise generate from product key.
                $item_display_name = $item['product_name_display'] ?? ucfirst($product_key_in_cart); 

              
                $image_prefix_to_use = $item['image_prefix'] ?? ($product_definitions_for_cart[$product_key_in_cart]['image_prefix'] ?? $product_key_in_cart);
                
                // Construct the image path. Assumes a naming convention like "prefix_1.jpg".
                // Ensure 'images/' directory and these product images (e.g., shirt_1.jpg) exist.
                $itemImage = "images/" . $image_prefix_to_use . "_1.jpg"; 
                if (!file_exists($itemImage)) {
                    // If the primary image is not found, use a placeholder.
                    // Ensure 'images/placeholder.jpg' exists.
                    $itemImage = "images/placeholder.jpg"; 
                }
                // Add the item's total price to the overall cart total.
                // The 'price' in the cart item is already the total for that line item (price_per_unit * quantity).
                $overall_total += $item['price']; 
            ?>
                <div class="cart-item" data-name="<?= htmlspecialchars(strtolower($item_display_name)) ?>">
                    <img src="<?= htmlspecialchars($itemImage) ?>" alt="<?= htmlspecialchars($item_display_name) ?>">
                    <div class="cart-item-info">
                        <a href="product_detail.php?product=<?= htmlspecialchars($product_key_in_cart) ?>"><strong><?= htmlspecialchars(ucfirst($item_display_name)) ?></strong></a><br>
                        Size: <?= htmlspecialchars($item['size']) ?> | Quantity: <?= htmlspecialchars($item['quantity']) ?><br>
                        Price: ₱<?= number_format($item['price'], 2) ?>
                    </div>
                    <form method="POST" class="remove-form" onsubmit="return confirm('Are you sure you want to remove this item from your cart?')">
                        <input type="hidden" name="remove_item" value="<?= $index ?>">
                        <button type="submit" class="button remove-button">Remove</button>
                    </form>
                </div>
            <?php endforeach; // End of loop through cart items. ?>
        </div>

        <?php if (!empty($cart)): ?>
            <div class="cart-total">
                Overall Total: ₱<?= number_format($overall_total, 2) ?>
            </div>
        <?php endif; ?>

        <div class="button-group">
            <a href="home.php" class="button">Back to Home Page</a>
            <?php if (!empty($cart)): ?>
                <?php // <a href="checkout.php" class="button">Proceed to Checkout</a> ?>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <p>Need help? Contact us at: store-email@gmail.com</p>
    </footer>

    <script>
    // JavaScript function to filter cart items based on search query.
    function filterCart(query) {
      const items = document.querySelectorAll('.cart-item'); // Get all cart item elements.
      const lowerQuery = query.toLowerCase(); // Convert query to lowercase for case-insensitive search.
      
      items.forEach(item => {
        // Check if the item's 'data-name' attribute (product name) includes the search query.
        // Show or hide the item accordingly.
        item.style.display = item.getAttribute('data-name').includes(lowerQuery) ? 'flex' : 'none';
      });
    }

    // JavaScript function for logout confirmation.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }
    </script>
</body>
</html>
