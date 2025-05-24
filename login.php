<?php
// Start the session to manage user login state.
session_start();

// Simulated user data for login.
// In a real application, this would involve connecting to a database and querying user records.
// For this no-DB version, a hardcoded user is used for testing.
$valid_username = "testuser";
// This is a hashed version of a password (e.g., "password123").
// You should generate this hash once using password_hash().
// Example: echo password_hash("password123", PASSWORD_DEFAULT);
// $2y$10$K9bsY2ZkM4A.r.b.CLaS9OAYkI09B7Yk9L3o3Q8Bw.vF.IshTqkS. is a hash for "password123"
$valid_hashed_password = '$2y$10$K9bsY2ZkM4A.r.b.CLaS9OAYkI09B7Yk9L3o3Q8Bw.vF.IshTqkS.'; 

// Check if the form was submitted using the POST method.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve username and password from the POST request.
    // Use null coalescing operator (??) to provide default empty strings if not set, preventing errors.
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate the submitted credentials against the hardcoded values.
    // password_verify() securely compares the submitted password with the stored hash.
    if ($username === $valid_username && password_verify($password, $valid_hashed_password)) {
        // If login is successful:
        // Store the username in the session to indicate the user is logged in.
        $_SESSION['username'] = $username;
        
        // In a real application, you would fetch other user details from the database
        // and store them in the session as needed.
        // For this example, we're storing some mock user data.
        // Ensure 'images/default_profile.png' exists for the profile image.
        $_SESSION['user_data'] = [
            'username' => $username,
            'email' => 'test@example.com', // Mock email
            'address' => '123 Test St.',    // Mock address
            'contact' => '09123456789',   // Mock contact
            'birthday' => '2000-01-01',   // Mock birthday
            'profile_image' => 'images/default_profile.png' // Default profile image path
        ];
        
        // Redirect the user to the home page after successful login.
        header("Location: home.php");
        exit(); // Stop further script execution.
    } else {
        // If login fails, set an error message to display to the user.
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <?php 
    // Include the common header (navigation, logo, etc.)
    include 'header.php'; 
    ?>

    <main class="container">
        <h2>Login to Your Account</h2>
        <form method="post" action="login.php">
            <?php 
            // If an error message is set (e.g., due to invalid credentials), display it.
            if (isset($error_message)): 
            ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <div class="button-group">
                <button type="submit" class="button" id="btnLogin">Login</button>
                <button type="button" class="button" id="btnSignIn" onclick="window.location.href='signup.php'">Sign Up</button>
            </div>
        </form>
    </main>

    <footer>
        <p>Discover unique finds and Filipino craftsmanship.</p>
    </footer>

    <script>
    // JavaScript function for logout confirmation.
    // This might be redundant if already handled by header.php, but included for completeness if header.php is minimal.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }
    </script>
</body>
</html>