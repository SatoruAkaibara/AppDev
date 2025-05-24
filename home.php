<?php
// Start the session to manage user login state and other session data.
session_start();

// Define product data for the home page.
// In a real application with many products, this data would typically come from a database
// or a shared data file (e.g., JSON, another PHP array include).
// For this "no new files" constraint, it's defined directly here.
// Ensure that the keys (e.g., "shirt", "socks") and structure are consistent
// if this data needs to be cross-referenced with other files like 'product_detail.php'.
$products_on_home = [
    // Each product is an associative array with its details.
    // 'name': Display name of the product.
    // 'display_price': Price shown on the home page.
    // 'image': Path to the product image. Ensure these images (e.g., 'images/shirt_1.jpg') exist.
    // 'alt': Alt text for the image, for accessibility and SEO.
    // 'link_param': The parameter used in the URL to link to the product_detail.php page (e.g., product_detail.php?product=shirt).
    "shirt" => ["name" => "Shirt", "display_price" => 500.00, "image" => "images/shirt_1.jpg", "alt" => "Shirt", "link_param" => "shirt"],
    "socks" => ["name" => "Socks", "display_price" => 200.00, "image" => "images/socks_1.jpg", "alt" => "Socks", "link_param" => "socks"],
    "short" => ["name" => "Short", "display_price" => 400.00, "image" => "images/short_1.jpg", "alt" => "Short", "link_param" => "short"],
    "jeans" => ["name" => "Jeans", "display_price" => 800.00, "image" => "images/jeans_1.jpg", "alt" => "Jeans", "link_param" => "jeans"],
    "trousers" => ["name" => "Trousers", "display_price" => 700.00, "image" => "images/trousers_1.jpg", "alt" => "Trousers", "link_param" => "trousers"]
    // Add more products here if they should appear on the home page.
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Our Store</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <?php 
    // Include the common header (navigation, logo, etc.)
    include 'header.php'; 
    ?>

    <main class="container">
        <section class="hero-section">
            <img src="images/hero_banner.jpg" alt="Discover Unique Products">
            <div class="hero-content">
                <h2>Discover Authentic Filipino Craftsmanship</h2>
                <p>From traditional weaves to modern designs, find unique pieces that tell a story.</p>
                <a href="#product-listings" class="button hero-button">Shop Now</a>
            </div>
        </section>

        <input type="text" placeholder="Search product..." id="searchBar" class="search-input"><br><br>

        <section class="product-grid" id="product-listings">
            <?php 
            // Loop through the $products_on_home array to display each product.
            foreach ($products_on_home as $product_key => $product): 
            ?>
                <a class="product-link" href="product_detail.php?product=<?php echo htmlspecialchars($product['link_param']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['alt']); ?>">
                    <span><?php echo htmlspecialchars($product['name']); ?></span>
                    <span class="product-price">₱<?php echo number_format($product['display_price'], 2); ?></span>
                </a>
            <?php endforeach; // End of the product loop. ?>
        </section>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); // Dynamically display the current year. ?> Our Store. All rights reserved. | <a href="about.php">About Us</a> | <a href="#">Privacy Policy</a></p>
    </footer>

    <script>
    // JavaScript function for logout confirmation.
    // This function is likely defined in header.php or in a global JS file if used across many pages.
    // If it's specific to this page and header.php doesn't have it, it should be here.
    // Assuming it's in header.php or a shared script.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }

    // JavaScript for the product search bar functionality.
    document.getElementById('searchBar').addEventListener('keyup', function() {
        const query = this.value.toLowerCase(); // Get the search query in lowercase.
        const productLinks = document.querySelectorAll('.product-grid .product-link'); // Get all product elements.
        
        productLinks.forEach(link => {
            // Get the product name text from within the link (assuming it's in the first span).
            const productName = link.querySelector('span').textContent.toLowerCase();
            // Check if the product name includes the search query.
            if (productName.includes(query)) {
                link.style.display = 'flex'; // Show the product (or 'block', 'inline-block' depending on original display style).
            } else {
                link.style.display = 'none'; // Hide the product.
            }
        });
    });
    </script>
</body>
</html>