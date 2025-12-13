<?php
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    echo "<h1>Migration: Modify Avatar URL to TEXT</h1>";
    echo "<ul>";

    $sql = "ALTER TABLE users MODIFY COLUMN avatar_url TEXT";
    $pdo->exec($sql);
    echo "<li style='color: green;'>Successfully modified 'avatar_url' to TEXT.</li>";

    echo "</ul>";
    echo "<p>Done.</p>";

} catch (Exception $e) {
    die("Migration Failed: " . $e->getMessage());
}
