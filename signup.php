<?php
// Start the session to store temporary user data during signup.
session_start();

// Check if the form was submitted using the POST method.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize form data.
    // htmlspecialchars() is used to prevent XSS attacks by converting special characters to HTML entities.
    // trim() removes whitespace from the beginning and end of the string.
    // The null coalescing operator (??) provides a default empty string if the POST variable is not set.
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? ''; // Password will be handled by signup_result.php.
                                          // In a real app, hash it here before storing.
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $contact = htmlspecialchars(trim($_POST['contact'] ?? '')); // Assumes HTML5 pattern validation handles format.
    $birthday = htmlspecialchars(trim($_POST['birthday'] ?? ''));

    // --- IMPORTANT NOTES FOR REAL APPLICATION ---
    // 1. Password Hashing:
    //    NEVER store plain text passwords. Always hash them using password_hash().
    //    Example: $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    //    Store $hashed_password in the database.
    //
    // 2. Input Validation:
    //    Beyond sanitization, perform robust server-side validation:
    //    - Check for empty required fields (though 'required' attribute in HTML helps, server-side is crucial).
    //    - Validate email format (e.g., using filter_var($email, FILTER_VALIDATE_EMAIL)).
    //    - Check for username/email uniqueness by querying the database.
    //    - Enforce password complexity rules.
    //    - Validate contact number format more strictly if needed.
    //    - Validate date format for birthday.
    //
    // 3. Database Interaction:
    //    Save the validated and hashed user data into a database (e.g., MySQL, PostgreSQL).
    // --- END OF REAL APPLICATION NOTES ---

    // For this no-database example, we'll store user data directly in a temporary session variable.
    // This data will be LOST when the session ends (e.g., browser closed, server restarted).
    // It's used to pass data to 'signup_result.php' which will then "log in" the user.
    $_SESSION['temp_user_data'] = [
        'username' => $username,
        'email' => $email,
        // WARNING: Storing plain password in session, even temporarily, is NOT recommended for production.
        // For this specific example flow (immediate use and clear by signup_result.php without re-verification from session),
        // it's shown, but in a real app, you'd hash it immediately. If it were to be verified later
        // from session (which is also not ideal), it should definitely be the hashed version.
        'password' => $password, 
        'address' => $address,
        'contact' => $contact,
        'birthday' => $birthday,
        'profile_image' => 'images/default_profile.png' // Fixed default profile image path.
                                                       // Ensure 'images/default_profile.png' exists.
    ];

    // After "signup" processing, redirect to a result page.
    header("Location: signup_result.php");
    exit(); // Stop further script execution.
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <?php 
    // Include the common header (navigation, logo, etc.)
    include 'header.php'; 
    ?>

    <main class="container">
        <h2>Create an Account</h2>
        <form method="post" action="signup.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required><br>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" placeholder="Enter your address" required><br>

            <label for="contact">Contact Number:</label>
            <input type="tel" name="contact" id="contact" pattern="[0-9]{11}" placeholder="e.g., 09123456789" required><br>

            <label for="birthday">Birthday:</label>
            <input type="date" name="birthday" id="birthday" required><br>

            <div class="button-group">
                <button type="submit" class="button" id="btnCreateAccount">Create Account</button>
                <button type="button" class="button" id="btnBack" onclick="window.location.href='login.php'">Back to Login</button>
            </div>
        </form>
    </main>

    <footer>
        <p>Join our community and discover unique Filipino products.</p>
    </footer>

    <script>
    // JavaScript function for logout confirmation.
    // This might be redundant if already handled by header.php.
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    }
    </script>
</body>
</html>