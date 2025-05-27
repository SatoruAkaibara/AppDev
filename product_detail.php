<?php
// Start the session to manage user login state and cart data.
session_start();

// Redirect to the login page if the user is not logged in.
// Access to product details might be restricted to logged-in users.
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Stop further script execution.
}

$products_data = [
    "shirt" => ["name" => "Shirt", "price" => 500, "image_prefix" => "shirt", "images" => ["images/shirt_1.jpg", "images/shirt_2.jpg", "images/shirt_3.jpg"]],
    "socks" => ["name" => "Socks", "price" => 200, "image_prefix" => "socks", "images" => ["images/socks_1.jpg", "images/socks_2.jpg", "images/socks_3.jpg"]],
    "short" => ["name" => "Short", "price" => 400, "image_prefix" => "short", "images" => ["images/short_1.jpg", "images/short_2.jpg", "images/short_3.jpg"]],
    "jeans" => ["name" => "Jeans", "price" => 800, "image_prefix" => "jeans", "images" => ["images/jeans_1.jpg", "images/jeans_2.jpg", "images/jeans_3.jpg"]],
    "trousers" => ["name" => "Trousers", "price" => 700, "image_prefix" => "trousers", "images" => ["images/trousers_1.jpg", "images/trousers_2.jpg", "images/trousers_3.jpg"]]
];

// Get the product key from the URL query parameter (e.g., product_detail.php?product=shirt).
// Default to 'shirt' if no product is specified, to prevent errors.
$product_key = $_GET['product'] ?? 'shirt';

// Check if the requested product key exists in our data.
if (!isset($products_data[$product_key])) {
    // If product not found, you could redirect to a 404 page, home page, or show an error.
    // For this example, a simple error message is shown.
    echo "Product not found!"; 
    // A more user-friendly approach would be:
    // header("Location: home.php?error=productnotfound");
    exit(); // Stop script execution.
}

// Get the details for the current product.
$current_product_info = $products_data[$product_key];
$product_name_display = $current_product_info['name'];
$base_price = $current_product_info['price'];
$image_prefix = $current_product_info['image_prefix'];

// Generate product images for the slideshow from the $current_product_info['images'] array.
$productImages = [];
if (isset($current_product_info['images']) && is_array($current_product_info['images'])) {
    foreach ($current_product_info['images'] as $imgPath) {
        if (file_exists($imgPath)) { // Check if the image file actually exists.
            $productImages[] = $imgPath;
        }
    }
}
// Fallback: If specific 'images' array is empty or not defined, try constructing paths using image_prefix convention.
if (empty($productImages)) {
    for ($i = 1; $i <= 3; $i++) { // Assuming up to 3 images by convention (e.g., prefix_1.jpg, prefix_2.jpg).
        $imgPath = "images/{$image_prefix}_{$i}.jpg";
        if (file_exists($imgPath)) {
            $productImages[] = $imgPath;
        }
    }
}
// Ultimate Fallback: If still no images, use a placeholder.
// Ensure 'images/placeholder.jpg' exists.
if (empty($productImages)) {
    $productImages[] = "images/placeholder.jpg"; 
}


// Define price adjustments based on product size.
// These values are added to the base price.
$adjustment = [
    "XS" => -100, "S" => -50, "M" => 0, // M is the base, no adjustment.
    "L" => 150, "XL" => 200, "XXL" => 250, "XXXL" => 300
];

// Initialize variables for size, quantity, total price, and success message.
$size = $_GET['size'] ?? 'M'; // Default size from GET request (e.g., if linked from elsewhere with size preselected), otherwise 'M'.
$quantity = 1;                // Default quantity.
$total_price = 0;             // Will be calculated.
$success_message = "";        // For displaying "Added to cart" messages.

// Handle form submission (e.g., Add to Cart, Buy Now).
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get size and quantity from the POST request.
    $size = $_POST['size'] ?? 'M'; // Fallback to 'M' if not set.
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Convert to integer, default to 1.
    if ($quantity < 1) $quantity = 1; // Ensure quantity is at least 1.

    // Calculate total price including size adjustment and quantity.
    $size_adjustment = $adjustment[$size] ?? 0; // Get adjustment for the selected size, default to 0 if size is invalid.
    $total_price = ($base_price + $size_adjustment) * $quantity;

    // Handle "Add to Cart" action.
    if (isset($_POST['add_to_cart'])) {
        // Initialize cart in session if it doesn't exist.
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Add the current product configuration to the cart.
        $_SESSION['cart'][] = [
            "product" => $product_key,               // Store the product's unique key.
            "product_name_display" => $product_name_display, // For easier display in cart.
            "size" => $size,
            "quantity" => $quantity,
            "price" => $total_price,                 // This is the total price for this line item (unit_price * quantity).
            "image_prefix" => $image_prefix          // Store image prefix for cart display.
        ];
        $success_message = "Successfully added to cart!"; // Set success message.
    }

    // Handle "Buy Now" action.
    if (isset($_POST['buy'])) {
        // Store the product details in a temporary session variable for the 'buy.php' page.
        $_SESSION['buy_now'] = [
            "product" => $product_name_display,    // Display name for buy.php.
            "product_key" => $product_key,         // Actual product key.
            "size" => $size,
            "quantity" => $quantity,
            "price" => $total_price,               // Total price for this item.
            "image_prefix" => $image_prefix,       // For image display on buy.php.
            "images" => $productImages             // Pass the array of specific product images.
        ];
        // Redirect to the buy page.
        header("Location: buy.php");
        exit(); // Stop script execution.
    }
} else {
    // If the page is loaded via GET (not a form submission), calculate default total price for initial display.
    $size_adjustment = $adjustment[$size] ?? 0;
    $total_price = ($base_price + $size_adjustment) * $quantity;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars(ucfirst($product_name_display)) ?> - Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; // Include the common site header. ?>

    <main class="container">
        <h2>Product Details: <?= htmlspecialchars(ucfirst($product_name_display)) ?></h2>

        <div class="slideshow-container">
            <?php foreach ($productImages as $index => $imgPath): ?>
                <img src="<?= htmlspecialchars($imgPath); ?>" class="slides" style="<?= $index === 0 ? 'display:block;' : ''; ?>">
            <?php endforeach; ?>
            <?php if (count($productImages) > 1): ?>
                <a class="arrow left-arrow" onclick="plusSlides(-1)">&#10094;</a>
                <a class="arrow right-arrow" onclick="plusSlides(1)">&#10095;</a>
            <?php endif; ?>
        </div>
        <div class="controls">
            </div>

        <div class="description-box">
            <form method="POST" action="product_detail.php?product=<?= htmlspecialchars($product_key) ?>">
                <div class="form-group">
                    <label for="sizeSelect">Size:</label>
                    <select name="size" id="sizeSelect" required>
                        <option value="XS" <?= ($size == 'XS') ? 'selected' : '' ?>>XS (-₱100)</option>
                        <option value="S" <?= ($size == 'S') ? 'selected' : '' ?>>S (-₱50)</option>
                        <option value="M" <?= ($size == 'M') ? 'selected' : '' ?>>M (base price)</option>
                        <option value="L" <?= ($size == 'L') ? 'selected' : '' ?>>L (+₱150)</option>
                        <option value="XL" <?= ($size == 'XL') ? 'selected' : '' ?>>XL (+₱200)</option>
                        <option value="XXL" <?= ($size == 'XXL') ? 'selected' : '' ?>>XXL (+₱250)</option>
                        <option value="XXXL" <?= ($size == 'XXXL') ? 'selected' : '' ?>>XXXL (+₱300)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantityInput">Quantity:</label>
                    <input type="number" name="quantity" id="quantityInput" min="1" value="<?= htmlspecialchars($quantity) ?>" required>
                </div>

                <?php if ($success_message): // Display success message if item was added to cart. ?>
                    <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>

                <p><strong>Total Price:</strong> ₱<span id="totalPrice"><?= number_format($total_price, 2) ?></span></p>

                <div class="button-group">
                    <button class="button" type="submit" name="add_to_cart">Add to Cart</button>
                    <button class="button" type="submit" name="buy">Buy Now</button>
                    <a href="home.php" class="button">Back to Home</a>
                </div>
            </form>
        </div>
    </main>
    
    <footer>
        <p>Explore unique finds and Filipino craftsmanship.</p>
    </footer>

    <script>
    // JavaScript for dynamic price update based on size and quantity.
    const adjustment = { // Price adjustments for sizes (must match PHP $adjustment array).
      "XS": -100, "S": -50, "M": 0,
      "L": 150, "XL": 200, "XXL": 250, "XXXL": 300
    };

    const basePrice = <?= $base_price ?>; // Get base price from PHP.
    const sizeSelect = document.getElementById('sizeSelect');
    const quantityInput = document.getElementById('quantityInput');
    const totalPriceElem = document.getElementById('totalPrice');

    // Function to update the displayed total price.
    function updateTotalPrice() {
      const selectedSize = sizeSelect.value;
      let currentQuantity = parseInt(quantityInput.value);
      
      // Validate quantity.
      if (isNaN(currentQuantity) || currentQuantity < 1) {
        currentQuantity = 1;
        quantityInput.value = 1; // Reset invalid quantity in the input field.
      }
      
      const total = (basePrice + (adjustment[selectedSize] || 0)) * currentQuantity; // Calculate total.
      totalPriceElem.textContent = total.toFixed(2); // Update display, formatted to 2 decimal places.
    }

    // Add event listeners to size and quantity inputs to update price on change.
    sizeSelect.addEventListener('change', updateTotalPrice);
    quantityInput.addEventListener('input', updateTotalPrice);
    
    // Initial call to set the price when the page loads.
    updateTotalPrice(); 

    // JavaScript for image slideshow.
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
        slideIndex = (slideIndex + n + slides.length) % slides.length; // Handle wrapping.
        showSlide(slideIndex);
    }

    // Show initial slide when DOM is ready.
    document.addEventListener('DOMContentLoaded', () => {
        if (slides.length > 0) {
            showSlide(slideIndex);
        }
    });

    // JavaScript function for logout confirmation.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }
    </script>
</body>
</html>
