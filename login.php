<?php
session_start();

if (isset($_SESSION['active_login_username']) && isset($_SESSION['active_login_hashed_password'])) {
    $valid_username_to_check = $_SESSION['active_login_username'];
    $valid_hashed_password_to_check = $_SESSION['active_login_hashed_password'];
} else {
    // Fallback to hardcoded testuser if no one has signed up in this session yet
    $valid_username_to_check = "testuser"; // Original hardcoded user
    $valid_hashed_password_to_check = '$2y$10$K9bsY2ZkM4A.r.b.CLaS9OAYkI09B7Yk9L3o3Q8Bw.vF.IshTqkS.'; // Hash for "password123"
}

// Check for signup success redirect message
$show_signup_success_message = false;
$registered_username_for_login_form = '';
if (isset($_SESSION['signup_success_redirect']) && $_SESSION['signup_success_redirect'] === true) {
    $show_signup_success_message = true;
    if (isset($_SESSION['registered_username'])) {
        $registered_username_for_login_form = $_SESSION['registered_username'];
    }
    unset($_SESSION['signup_success_redirect']);
    unset($_SESSION['registered_username']);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username_attempt = trim($_POST['username'] ?? '');
    $password_attempt = $_POST['password'] ?? '';

  

    if ($username_attempt === $valid_username_to_check && password_verify($password_attempt, $valid_hashed_password_to_check)) {
        $_SESSION['username'] = $username_attempt;
       

        
        $_SESSION['user_data'] = [
            'username' => $username_attempt,
            'email' => ($username_attempt === "testuser") ? 'test@example.com' : 'newuser@example.com', // Example
            'address' => ($username_attempt === "testuser") ? '123 Test St.' : 'N/A',
            'contact' => ($username_attempt === "testuser") ? '09123456789' : 'N/A',
            'birthday' => ($username_attempt === "testuser") ? '2000-01-01' : 'N/A',
            'profile_image' => 'images/default_profile.png'
        ];
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container <?php if(basename($_SERVER['PHP_SELF']) == 'login.php') echo 'login-main-transparent'; ?>">
        <?php if ($show_signup_success_message): ?>
            <div class="success-message">
                Account for "<?= htmlspecialchars($registered_username_for_login_form) ?>" created successfully! Please log in.
            </div>
        <?php endif; ?>

        <h2>Login to Your Account</h2>
        <form method="post" action="login.php" class="auth-form">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($registered_username_for_login_form) ?>" placeholder="Username" required>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <button type="submit" id="btnLogin">Login</button> 
            
            <div class="login-separator">
                <span class="login-separator-line"></span>
                <span class="login-separator-text">or</span>
                <span class="login-separator-line"></span>
            </div>

            <div class="form-footer">
                Don't have an account? <a href="signup.php">Create Account</a>
            </div>
        </form>
    </main>

    <footer>
        <p>Discover unique finds and Filipino craftsmanship.</p>
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
