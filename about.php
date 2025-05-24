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
    // 'header.php' likely contains the navigation bar and site logo.
    include 'header.php';
    ?>

    <main class="container about-page">
        <h2>Our Story</h2>

        <div class="about-content">
            <div class="about-text">
                <p>Welcome to **Our Store**, your destination for unique, handcrafted treasures inspired by the rich cultural heritage of the Philippines. We believe in showcasing the artistry and dedication of local artisans, bringing their beautiful creations directly to you.</p>
                <p>Our journey began with a passion for preserving traditional techniques while also embracing modern designs. Each product in our collection is carefully curated, reflecting stories of craftsmanship, community, and the vibrant spirit of Filipino creativity.</p>
                <p>We are committed to fair trade practices, ensuring that our artisans receive equitable compensation for their incredible work. By choosing **Our Store**, you're not just acquiring a product; you're supporting sustainable livelihoods and celebrating a legacy of artistic excellence.</p>
                
                <h3>Our Mission</h3>
                <p>To connect global customers with authentic Filipino craftsmanship, promoting cultural appreciation and empowering local artisan communities through fair and sustainable trade.</p>
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