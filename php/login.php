<?php
/* ===========================
   php/login.php
   Handles user login.
   Checks credentials against the database,
   creates a session if valid.
   =========================== */

// Start the session so we can store session variables
session_start();

// Include database connection
require_once 'config.php';

// Only process if submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: Get and clean form inputs
    $email = trim($_POST['email']    ?? '');
    $pass  = $_POST['password']      ?? '';

    // Step 2: Basic validation — make sure fields are not empty
    if (empty($email) || empty($pass)) {
        header('Location: ../login.html?error=empty');
        exit;
    }

    // Step 3: Look up the user by email address
    // Using a prepared statement prevents SQL injection attacks
    $stmt = $conn->prepare("SELECT id, name, email, password, is_subscribed FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result row
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();  // Returns an associative array or NULL
    $stmt->close();

    // Step 4: Verify the password
    // password_verify() compares the plain text with the stored hash
    if ($user && password_verify($pass, $user['password'])) {

        // --- LOGIN SUCCESSFUL ---

        // Step 5: Store user data in the session
        // This keeps the user "logged in" across pages
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['is_subscribed'] = $user['is_subscribed'];  // 0 or 1

        // Regenerate session ID for security (prevents session fixation attacks)
        session_regenerate_id(true);

        // Step 6: Redirect to homepage after login
        header('Location: ../index.html');
        exit;

    } else {
        // --- LOGIN FAILED ---
        // Either email not found, or password wrong
        header('Location: ../login.html?error=invalid');
        exit;
    }

} else {
    // Direct access — redirect to login page
    header('Location: ../login.html');
    exit;
}

$conn->close();
?>
