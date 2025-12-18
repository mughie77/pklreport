<?php
// Database configuration - copy this file to database.php and fill in your details
// Or, for better security, use environment variables.

// Database host, e.g., 'localhost' or '127.0.0.1'
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');

// Database username
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'debian-sys-maint');

// Database password
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'mResD463BoH8pEWo');

// Database name
define('DB_NAME', getenv('DB_NAME') ?: 'pkl_report');

// Create a new database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
