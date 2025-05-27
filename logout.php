<?php
// Start the session. This is necessary to access and manipulate session data.
session_start();

// Unset all session variables.
// $_SESSION is an associative array containing all session variables.
// session_unset() clears all variables from this array.
session_unset();

// Destroy the session.
// This removes the session data from the server and deletes the session cookie from the user's browser.
session_destroy();

// Redirect the user to the login page after successfully logging out.
header("Location: login.php");
// Ensure no further code is executed after the redirect.
exit();
?>
