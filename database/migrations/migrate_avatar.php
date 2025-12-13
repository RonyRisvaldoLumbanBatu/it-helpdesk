<?php
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    echo "<h1>Migration: Add Avatar URL to Users</h1>";
    echo "<ul>";

    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'avatar_url'");
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "<li style='color: orange;'>Column 'avatar_url' already exists.</li>";
    } else {
        $sql = "ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255) DEFAULT NULL";
        $pdo->exec($sql);
        echo "<li style='color: green;'>Successfully added 'avatar_url' column to 'users' table.</li>";
    }

    echo "</ul>";
    echo "<p>Done.</p>";
    echo "<a href='index.php'>Back to Home</a>";

} catch (Exception $e) {
    die("Migration Failed: " . $e->getMessage());
}
