<?php

declare(strict_types=1);

/**
 * Helper global untuk view.
 *
 * Fungsi ini sengaja didefinisikan di file (bukan di class) supaya pemakaian
 * di template PHP klasik tetap ringkas, mis: `<?= e($user['name']) ?>`.
 *
 * Di-load via Composer `autoload.files` (lihat composer.json).
 */

if (!function_exists('e')) {
    /**
     * Escape string untuk konteks HTML / atribut HTML, anti-XSS.
     *
     * Memakai ENT_QUOTES sehingga aman untuk pemakaian di dalam atribut
     * yang dikutip ganda maupun tunggal. Nilai null/non-string dipaksa string.
     */
    function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    /**
     * Bangun URL aset publik dengan cache-busting berbasis mtime file.
     *
     * Jika file tidak ditemukan, kembalikan URL apa adanya (tanpa query).
     * Pemakaian: `<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">`
     */
    function asset(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');
        $publicDir = dirname(__DIR__) . '/public/';
        $absolute = $publicDir . $relativePath;

        if (is_file($absolute)) {
            $version = (string) filemtime($absolute);

            return $relativePath . '?v=' . $version;
        }

        return $relativePath;
    }
}
