<?php
/* ===========================
   php/subscribe.php
   Simulated subscription handler.
   Updates the user's is_subscribed = 1 in the database.
   No real payment is processed — this is a demo.
   =========================== */

// Start session to check if user is logged in
session_start();

// Include database connection
require_once 'config.php';

// Step 1: Check if the user is logged in
// If not, redirect to login page with an error message
if (!isset($_SESSION['user_id'])) {
    header('Location: ../subscription.html?error=login');
    exit;
}

// Step 2: Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the selected plan from the hidden form field
    // Allowed values: basic, standard, premium
    $plan    = $_POST['plan'] ?? 'basic';
    $user_id = $_SESSION['user_id'];  // Get logged-in user's ID from session

    // Step 3: Update the user's subscription status in the database
    // In a real project, you might also save the plan name, start date, etc.
    // Here we keep it simple: just set is_subscribed = 1
    $stmt = $conn->prepare("UPDATE users SET is_subscribed = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);  // "i" = integer type

    if ($stmt->execute()) {
        // --- UPDATE SUCCESSFUL ---

        // Also update the session variable so the current session reflects the change
        // (without this, user would need to log out and back in to see the change)
        $_SESSION['is_subscribed'] = 1;

        $stmt->close();

        // Redirect to subscription page with a success message
        header('Location: ../subscription.html?success=subscribed');
        exit;

    } else {
        // Database update failed
        $stmt->close();
        header('Location: ../subscription.html?error=db');
        exit;
    }

} else {
    // Direct access without POST — redirect to plans page
    header('Location: ../subscription.html');
    exit;
}

$conn->close();
?>
