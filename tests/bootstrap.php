<?php

declare(strict_types=1);

/**
 * Bootstrap untuk PHPUnit.
 *
 * Tujuan:
 *  - Load Composer autoloader (classmap untuk src/, PSR-4 untuk tests/).
 *  - Set timezone default agar test deterministik.
 */

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Asia/Jakarta');
