<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? ''; // Raw password from form
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $contact = htmlspecialchars(trim($_POST['contact'] ?? '')); 
    $birthday = htmlspecialchars(trim($_POST['birthday'] ?? ''));

    // Hash the password entered during signup
    $hashed_password_for_storage = password_hash($password, PASSWORD_DEFAULT);

    // Store the new valid credentials in the session
    // This will effectively set the "active" user for the login page
    $_SESSION['active_login_username'] = $username;
    $_SESSION['active_login_hashed_password'] = $hashed_password_for_storage;

   
    $_SESSION['temp_user_data_details'] = [ // Renamed to avoid confusion
        'username' => $username,
        'email' => $email,
   
        'address' => $address,
        'contact' => $contact,
        'birthday' => $birthday,
        'profile_image' => 'images/default_profile.png'
    ];

    // Redirect to signup_result.php
    header("Location: signup_result.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container <?php if(basename($_SERVER['PHP_SELF']) == 'signup.php') echo 'login-main-transparent'; // Optional for transparent bg ?>">
        <h2>Create an Account</h2>
        <form method="post" action="signup.php" class="auth-form">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" placeholder="Enter your address" required>

            <label for="contact">Contact Number:</label>
            <input type="tel" name="contact" id="contact" pattern="[0-9]{11}" placeholder="e.g., 09123456789" required>

            <label for="birthday">Birthday:</label>
            <input type="date" name="birthday" id="birthday" required>

            <button type="submit" id="btnCreateAccount">Create Account</button>
            
            <div class="form-footer">
                 <a href="login.php">Back to Login</a>
            </div>
        </form>
    </main>

    <footer>
        <p>Join our community and discover unique Filipino products.</p>
    </footer>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
