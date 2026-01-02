<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    logActivity('logout', 'user', $_SESSION['user_id']);
}

// Destroy session
session_destroy();

// Redirect to home
header('Location: /ashels-autospare/');
exit;
?>
