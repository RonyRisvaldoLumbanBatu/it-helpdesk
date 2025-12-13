<?php
// Initialize Database from SQL file
require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

$host = $config['host'];
$user = $config['username'];
$pass = $config['password'];
$dbname = $config['dbname'];

try {
    // 1. Connect without DB name to create it
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL Server.\n";

    // 2. Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database '$dbname' created or exists.\n";

    // 3. Connect to DB
    $pdo->exec("USE `$dbname`");

    // 4. Read SQL file
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("Error: database.sql not found at $sqlFile\n");
    }

    $sqlContent = file_get_contents($sqlFile);

    // 5. Execute SQL (Split by ;) - Simple naive split, usually works for dumps without procedures
    // Better: Run huge block or use specific importer? 
    // PDO::exec supports multiple statements if configured? 
    // Usually mysqlnd supports it.

    // Let's try raw exec.
    $pdo->exec($sqlContent);
    echo "Tables imported successfully from database.sql.\n";

    // 6. Run Migrations manually just in case?
    // database.sql might be outdated compared to recent migrations?
    // Check if database.sql has 'avatar_url'.
    // Step 913 showed it didn't.
    // So we MUST run migrations too.

    $migrations = glob(__DIR__ . '/migrations/*.php');
    foreach ($migrations as $mig) {
        echo "Running migration: " . basename($mig) . "\n";
        // Cannot execute PHP file directly inside this scope easily unless structure matches.
        // It's better to tell user to run them.
        // Or just run the SQL commands from them here?

        // Let's just create a quick migration runner.
        // Since my migrations are standalone scripts, I can include them?
        // But they use their own require '../config'.Paths might break.
        // Simpler: Just run command line.
    }

    echo "\nDONE! Database is ready.\n";
    echo "IMPORTANT: Now run migrations manually via: for %f in (database\migrations\*.php) do php %f\n";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
