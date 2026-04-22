<?php
/* ===========================
   php/register.php
   Handles new user registration.
   Receives POST data from register.html,
   validates it, and inserts into the database.
   =========================== */

// --- Include database connection ---
require_once 'config.php';

// --- Only process if the form was submitted via POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: Get and sanitize form inputs
    // trim() removes extra spaces; htmlspecialchars() prevents XSS attacks
    $name  = trim(htmlspecialchars($_POST['name']  ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $pass  = $_POST['password']         ?? '';
    $conf  = $_POST['confirm_password'] ?? '';

    // Step 2: Basic server-side validation
    // (client-side JS validation is a first line of defense, but server check is required)
    if (empty($name) || empty($email) || empty($pass)) {
        header('Location: ../register.html?error=empty');
        exit;
    }

    // Check password is at least 6 characters
    if (strlen($pass) < 6) {
        header('Location: ../register.html?error=short');
        exit;
    }

    // Check passwords match
    if ($pass !== $conf) {
        header('Location: ../register.html?error=mismatch');
        exit;
    }

    // Step 3: Check if email already exists in the database
    // Use a prepared statement to prevent SQL injection
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);  // "s" = string type
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Email already registered — redirect back with error
        $check_stmt->close();
        header('Location: ../register.html?error=exists');
        exit;
    }
    $check_stmt->close();

    // Step 4: Hash the password before saving
    // NEVER store plain-text passwords!
    // password_hash() uses bcrypt by default (very secure)
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // Step 5: Insert the new user into the database
    // is_subscribed defaults to 0 (not subscribed)
    $insert_stmt = $conn->prepare(
        "INSERT INTO users (name, email, password, is_subscribed) VALUES (?, ?, ?, 0)"
    );
    $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($insert_stmt->execute()) {
        // Success! Redirect to login page with a success message
        $insert_stmt->close();
        header('Location: ../login.html?success=registered');
        exit;
    } else {
        // Database error during insert
        $insert_stmt->close();
        header('Location: ../register.html?error=db');
        exit;
    }

} else {
    // If someone visits register.php directly (not via form), redirect them
    header('Location: ../register.html');
    exit;
}

$conn->close();
?>
