<?php
// Start the session to manage user login state and other session data.
session_start();


$products_on_home = [
    
    "shirt" => ["name" => "Shirt", "display_price" => 500.00, "image" => "image/shirt_1.png", "alt" => "Shirt", "link_param" => "shirt"],
    "shorts" => ["name" => "Shorts", "display_price" => 400.00, "image" => "image/shorts_1.png", "alt" => "Shorts", "link_param" => "shorts"], 
    "sando" => ["name" => "Sando", "display_price" => 300.00, "image" => "image/sando_1.png", "alt" => "Sando", "link_param" => "sando"], 
    "polo" => ["name" => "Polo", "display_price" => 800.00, "image" => "image/polo_1.jpg", "alt" => "Polo", "link_param" => "polo"],     
 
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Our Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php 
    // Include the common header (navigation, logo, etc.)
    include 'header.php'; 
    ?>

    <main class="container">
        <section class="hero-section">
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
    // This function is also present in your header.php.
    // It's generally better to define such common functions once in a shared script or in header.php.
    // For now, leaving it here ensures it works if header.php's script isn't loaded or a specific version is needed.
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
                link.style.display = 'flex'; // Show the product (make sure 'flex' matches your CSS default for .product-link).
            } else {
                link.style.display = 'none'; // Hide the product.
            }
        });
    });
    </script>
</body>
</html>