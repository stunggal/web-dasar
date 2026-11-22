<?php
// logout.php — Hapus session & redirect ke login (Langkah 6: Logout)
session_start();

// Hapus semua session data
$_SESSION = [];

// Hapus session cookie jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke login dengan pesan
header('Location: login.php?logout=1');
exit;