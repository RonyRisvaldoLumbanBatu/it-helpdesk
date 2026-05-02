-- Buat Database (Jika belum ada, silakan buat manual di phpMyAdmin dengan nama 'it_helpdesk')
-- CREATE DATABASE IF NOT EXISTS it_helpdesk;
-- USE it_helpdesk;

-- Tabel Users (Pengguna Aplikasi)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Password akan di-hash
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user', 'staff', 'mahasiswa') NOT NULL DEFAULT 'mahasiswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data Users (Default Password: password123)
-- Hash password generated menggunakan password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator IT', 'admin'),
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Staff', 'user');

-- Tabel Tickets (Tiket Pengajuan)
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'resolved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel Login Attempts (Brute-force protection untuk endpoint auth_check)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(100) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    successful TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_ip_time (identifier, ip, attempted_at),
    INDEX idx_attempted_at (attempted_at)
);
