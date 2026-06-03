<?php

// ===== DATABASE CONNECTION =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'new_collection_db');

// create Connection 
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// set Character set 
mysqli_set_charset($conn, 'utf8');

// start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include notification helper
require_once __DIR__ . '/notification_helper.php';
?>
