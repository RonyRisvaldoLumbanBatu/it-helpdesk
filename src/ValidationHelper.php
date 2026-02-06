<?php

/**
 * ValidationHelper
 * 
 * Helper class untuk validasi input data
 * Memastikan data yang masuk ke database sudah tervalidasi dengan baik
 */
class ValidationHelper
{
    /**
     * Validasi format email
     * 
     * @param string $email - Email yang akan divalidasi
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateEmail($email)
    {
        $email = trim($email);

        // Check if empty
        if (empty($email)) {
            return ['valid' => false, 'error' => 'Email tidak boleh kosong'];
        }

        // Check email format using filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Format email tidak valid'];
        }

        // Check length (RFC 5321)
        if (strlen($email) > 254) {
            return ['valid' => false, 'error' => 'Email terlalu panjang (maksimal 254 karakter)'];
        }

        // Additional check: must have @ and domain
        $parts = explode('@', $email);
        if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1])) {
            return ['valid' => false, 'error' => 'Format email tidak valid'];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Validasi username
     * Hanya boleh: huruf, angka, underscore, dan dash
     * Minimal 3 karakter, maksimal 30 karakter
     * 
     * @param string $username - Username yang akan divalidasi
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateUsername($username)
    {
        $username = trim($username);

        // Check if empty
        if (empty($username)) {
            return ['valid' => false, 'error' => 'Username tidak boleh kosong'];
        }

        // Check length
        if (strlen($username) < 3) {
            return ['valid' => false, 'error' => 'Username minimal 3 karakter'];
        }

        if (strlen($username) > 30) {
            return ['valid' => false, 'error' => 'Username maksimal 30 karakter'];
        }

        // Check format: only alphanumeric, underscore, and dash
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return ['valid' => false, 'error' => 'Username hanya boleh huruf, angka, underscore (_), dan dash (-)'];
        }

        // Check if starts with letter
        if (!preg_match('/^[a-zA-Z]/', $username)) {
            return ['valid' => false, 'error' => 'Username harus diawali dengan huruf'];
        }

        // Check for spaces
        if (strpos($username, ' ') !== false) {
            return ['valid' => false, 'error' => 'Username tidak boleh mengandung spasi'];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Validasi password
     * Minimal 8 karakter, harus ada huruf dan angka
     * 
     * @param string $password - Password yang akan divalidasi
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validatePassword($password)
    {
        // Check if empty
        if (empty($password)) {
            return ['valid' => false, 'error' => 'Password tidak boleh kosong'];
        }

        // Check minimum length
        if (strlen($password) < 8) {
            return ['valid' => false, 'error' => 'Password minimal 8 karakter'];
        }

        // Check maximum length (for security)
        if (strlen($password) > 128) {
            return ['valid' => false, 'error' => 'Password maksimal 128 karakter'];
        }

        // Check for at least one letter
        if (!preg_match('/[a-zA-Z]/', $password)) {
            return ['valid' => false, 'error' => 'Password harus mengandung minimal 1 huruf'];
        }

        // Check for at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'error' => 'Password harus mengandung minimal 1 angka'];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Validasi nama lengkap
     * Minimal 3 karakter, hanya huruf dan spasi
     * 
     * @param string $name - Nama yang akan divalidasi
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateName($name)
    {
        $name = trim($name);

        // Check if empty
        if (empty($name)) {
            return ['valid' => false, 'error' => 'Nama tidak boleh kosong'];
        }

        // Check minimum length
        if (strlen($name) < 3) {
            return ['valid' => false, 'error' => 'Nama minimal 3 karakter'];
        }

        // Check maximum length
        if (strlen($name) > 100) {
            return ['valid' => false, 'error' => 'Nama maksimal 100 karakter'];
        }

        // Check format: only letters, spaces, and some special characters (', -, .)
        if (!preg_match('/^[a-zA-Z\s\'-\.]+$/', $name)) {
            return ['valid' => false, 'error' => 'Nama hanya boleh huruf, spasi, tanda petik (\'), dash (-), dan titik (.)'];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Validasi role
     * Hanya boleh role yang sudah ditentukan
     * 
     * @param string $role - Role yang akan divalidasi
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateRole($role)
    {
        $allowedRoles = ['admin', 'staff', 'mahasiswa', 'user'];

        if (!in_array($role, $allowedRoles)) {
            return ['valid' => false, 'error' => 'Role tidak valid. Pilih: ' . implode(', ', $allowedRoles)];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Sanitasi string untuk mencegah XSS
     * 
     * @param string $input - String yang akan disanitasi
     * @return string - String yang sudah disanitasi
     */
    public static function sanitizeString($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validasi panjang text
     * 
     * @param string $text - Text yang akan divalidasi
     * @param int $minLength - Panjang minimal
     * @param int $maxLength - Panjang maksimal
     * @param string $fieldName - Nama field (untuk error message)
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateLength($text, $minLength, $maxLength, $fieldName = 'Field')
    {
        $text = trim($text);
        $length = strlen($text);

        if ($length < $minLength) {
            return ['valid' => false, 'error' => "$fieldName minimal $minLength karakter"];
        }

        if ($length > $maxLength) {
            return ['valid' => false, 'error' => "$fieldName maksimal $maxLength karakter"];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * Validasi multiple fields sekaligus
     * 
     * @param array $validations - Array of validation results
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateMultiple($validations)
    {
        $errors = [];
        $allValid = true;

        foreach ($validations as $field => $result) {
            if (!$result['valid']) {
                $errors[$field] = $result['error'];
                $allValid = false;
            }
        }

        return ['valid' => $allValid, 'errors' => $errors];
    }

    /**
     * Format error messages untuk display
     * 
     * @param array $errors - Array of error messages
     * @return string - HTML formatted error list
     */
    public static function formatErrors($errors)
    {
        if (empty($errors)) {
            return '';
        }

        $html = '<ul style="color: #ef4444; margin: 10px 0; padding-left: 20px;">';
        foreach ($errors as $field => $error) {
            $html .= '<li><strong>' . ucfirst($field) . '</strong>: ' . htmlspecialchars($error) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
