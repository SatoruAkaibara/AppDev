<?php
// Start the session to access session variables.
// Sessions are used to maintain user information across different pages.
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Our Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Include the header content.
    // 'header.php'  contains the navigation bar and site logo.
    include 'header.php';
    ?>

    <main class="container about-page">
        <h2>Our Story</h2>

        <div class="about-content">
            <div class="about-text">
                <p>We are TELLa, a proudly Filipino clothing brand that creates stylish and breathable tropical wear made for</p>
                <p>the vibrant, sun-soaked lifestyle of the Philippines. Designed with comfort and culture in mind, our pieces are</p>
                <p>perfect for embracing the warmthm, whether you're by the beach, in the city, or anywhere in between.</p>
                
                <h3>Our Mission</h3>
                    <p>To provide high-quality tropical clothing that reflects the vibrant spirit of the Philippines, while promoting sustainable practices, celebrating local craftsmanship, and empowering communities through ethical fashion.</p>

            </div>
            <div class="about-image">
                <img src="images/about_us_photo.jpg" alt="Our Team / Craftsmanship">
            </div>
        </div>

        <h3>Our Values</h3>
        <ul class="values-list">
            <li><strong>Authenticity:</strong> We source products directly from skilled artisans.</li>
            <li><strong>Sustainability:</strong> We promote eco-friendly materials and practices.</li>
            <li><strong>Empowerment:</strong> We ensure fair wages and safe working conditions.</li>
            <li><strong>Quality:</strong> We offer durable, high-quality, and beautifully crafted items.</li>
            <li><strong>Community:</strong> We build strong relationships with our artisan partners.</li>
        </ul>

        <div class="button-group">
            <a href="home.php" class="button">Back to Home</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Our Store. All rights reserved. | <a href="#">Terms of Service</a> | <a href="#">Contact Us</a></p>
    </footer>

    <script>
        // JavaScript function to confirm user logout.
        function confirmLogout() {
            // Display a confirmation dialog.
            if (confirm("Are you sure you want to log out?")) {
                // If confirmed, redirect to the logout script.
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
