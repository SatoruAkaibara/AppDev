<?php
// Start the session to manage user login state and cart data.
session_start();

// Redirect to the login page if the user is not logged in.
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); 
}

$products_data = [
    "shirt" => ["name" => "Shirt", "price" => 500, "image_prefix" => "shirt", "images" => ["image/shirt_1.png", "image/shirt_2.jpg", "image/shirt_3.png"]], 
    "shorts" => ["name" => "Shorts", "price" => 200, "image_prefix" => "shorts", "images" => ["image/shorts_1.png", "image/shorts_2.png", "image/shorts_3.png"]], 
    "sando" => ["name" => "Sando", "price" => 400, "image_prefix" => "sando", "images" => ["image/sando_1.png", "image/sando_2.png", "image/sando_3.png"]],
    "polo" => ["name" => "Polo", "price" => 800, "image_prefix" => "polo", "images" => ["image/polo_1.jpg", "image/polo_2.jpg", "image/polo_3.jpg"]],
];

$product_key = $_GET['product'] ?? 'shirt';

if (!isset($products_data[$product_key])) {
    echo "Product not found!"; 
    exit();
}

$current_product_info = $products_data[$product_key];
$product_name_display = $current_product_info['name'];
$base_price = $current_product_info['price'];
$image_prefix = $current_product_info['image_prefix'];

$productImages = [];
if (isset($current_product_info['images']) && is_array($current_product_info['images'])) {
    foreach ($current_product_info['images'] as $imgPath) {
        if (file_exists($imgPath)) { 
            $productImages[] = $imgPath;
        }
    }
}

if (empty($productImages)) {
    $possible_extensions = ['jpg', 'png', 'jpeg', 'gif'];
    for ($i = 1; $i <= 3; $i++) { 
        foreach ($possible_extensions as $ext) {
            $imgPath = "image/{$image_prefix}_{$i}.{$ext}"; 
            if (file_exists($imgPath)) {
                $productImages[] = $imgPath;
                break; 
            }
        }
    }
}

if (empty($productImages)) {
    $productImages[] = "image/placeholder.jpg"; 
}

$adjustment = [
    "XS" => -100, "S" => -50, "M" => 0, 
    "L" => 150, "XL" => 200, "XXL" => 250, "XXXL" => 300
];

$size = $_GET['size'] ?? 'M'; 
$quantity = 1;                
$total_price = 0;             
$success_message = "";        

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $size = $_POST['size'] ?? 'M'; 
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; 
    if ($quantity < 1) $quantity = 1; 

    $size_adjustment = $adjustment[$size] ?? 0; 
    $total_price = ($base_price + $size_adjustment) * $quantity;

    if (isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = [
            "product" => $product_key,                      
            "product_name_display" => $product_name_display, 
            "size" => $size,
            "quantity" => $quantity,
            "price" => $total_price,                        
            "display_image" => !empty($productImages) ? $productImages[0] : "image/placeholder.jpg",
            "all_item_images" => $productImages // Store all images for this product variant
        ];
        $success_message = "Successfully added to cart!"; 
    }

    if (isset($_POST['buy'])) {
        $_SESSION['buy_now'] = [
            "product" => $product_name_display,     
            "product_key" => $product_key,          
            "size" => $size,
            "quantity" => $quantity,
            "price" => $total_price,                
            "images" => $productImages // Pass all resolved images for buy.php slideshow
        ];
        header("Location: buy.php");
        exit(); 
    }
} else {
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
    <?php include 'header.php'; ?>
    <main class="container">
        <h2>Product Details: <?= htmlspecialchars(ucfirst($product_name_display)) ?></h2>
        <div class="slideshow-container">
            <?php foreach ($productImages as $index => $imgPath): ?>
                <img src="<?= htmlspecialchars($imgPath); ?>" class="slides" style="<?= $index === 0 ? 'display:block;' : ''; ?>">
            <?php endforeach; ?>
            <?php if (count($productImages) > 1 && $productImages[0] !== "image/placeholder.jpg"): ?>
                <a class="arrow left-arrow" onclick="plusSlides(-1)">&#10094;</a>
                <a class="arrow right-arrow" onclick="plusSlides(1)">&#10095;</a>
            <?php endif; ?>
        </div>
        <div class="controls"></div>
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
                <?php if ($success_message): ?>
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
    const adjustment = { 
      "XS": -100, "S": -50, "M": 0,
      "L": 150, "XL": 200, "XXL": 250, "XXXL": 300
    };
    const basePrice = <?= $base_price ?>; 
    const sizeSelect = document.getElementById('sizeSelect');
    const quantityInput = document.getElementById('quantityInput');
    const totalPriceElem = document.getElementById('totalPrice');
    function updateTotalPrice() {
      const selectedSize = sizeSelect.value;
      let currentQuantity = parseInt(quantityInput.value);
      if (isNaN(currentQuantity) || currentQuantity < 1) {
        currentQuantity = 1;
        quantityInput.value = 1; 
      }
      const total = (basePrice + (adjustment[selectedSize] || 0)) * currentQuantity; 
      totalPriceElem.textContent = total.toFixed(2); 
    }
    sizeSelect.addEventListener('change', updateTotalPrice);
    quantityInput.addEventListener('input', updateTotalPrice);
    updateTotalPrice(); 
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
        if (slides.length > 0) {
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