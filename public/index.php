<?php
// Main Entry Point
// Di sini kita bisa load autoloader atau config nantinya

require_once __DIR__ . '/../config/app.php';

// Routing sederhana (contoh)
$page = $_GET['page'] ?? 'home';

if ($page === 'change_password') {
    require_once __DIR__ . '/../views/change_password.php';
} else {
    echo "<h1>Welcome to IT Helpdesk</h1>";
}
