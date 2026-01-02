<?php
/**
 * Database Configuration
 * Update these values to match your XAMPP MySQL settings
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'ashels_autospare');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP has no password

// PDO Connection
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// MySQLi Connection (alternative)
function getMySQLiConnection() {
    static $mysqli = null;
    
    if ($mysqli === null) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            die("Database connection failed: " . $mysqli->connect_error);
        }
        
        $mysqli->set_charset("utf8mb4");
    }
    
    return $mysqli;
}
?>
