<?php
/* ===========================
   php/config.php
   Database connection settings
   Include this file at the top of every PHP file that needs the DB.
   =========================== */

// --- Database credentials ---
// Change these to match your local server settings (XAMPP, WAMP, etc.)
define('DB_HOST', 'localhost');      // Database host (usually 'localhost')
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password (empty for XAMPP default)
define('DB_NAME', 'cinestream_db');  // Database name (must match database.sql)

// --- Create connection using MySQLi ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Check if connection was successful ---
if ($conn->connect_error) {
    // Stop execution and show an error message if DB connection fails
    die('<h2 style="font-family:sans-serif;color:red;text-align:center;margin-top:100px;">
        ❌ Database connection failed: ' . $conn->connect_error . '
        <br><small>Check your config.php settings.</small>
    </h2>');
}

// Set character encoding to UTF-8 (handles special characters)
$conn->set_charset("utf8");
?>
