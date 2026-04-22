<?php
/* ===========================
   php/logout.php
   Destroys the user's session and logs them out.
   =========================== */

// Start the session so we can access and destroy it
session_start();

// Step 1: Unset all session variables
// This removes all data stored in $_SESSION
$_SESSION = array();

// Step 2: Destroy the session cookie (if one exists)
// This removes the session cookie from the user's browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,              // Expire in the past
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Step 3: Completely destroy the session on the server
session_destroy();

// Step 4: Redirect to login page with a success message
header('Location: ../login.html?success=loggedout');
exit;
?>
