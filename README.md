# 🎫 IT Helpdesk System

![Project Status](https://img.shields.io/badge/status-active-success.svg)
![PHP Version](https://img.shields.io/badge/php-%5E8.0-777bb4.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

Aplikasi **IT Helpdesk** modern berbasis web untuk mengelola tiket dukungan teknis, pelaporan insiden, dan permintaan layanan IT di **Universitas Satya Terra Bhinneka**. Dibangun dengan PHP Native yang bersih, performa tinggi, dan mudah dikembangkan.

---

## ✨ Fitur Utama

### 🔐 Otentikasi & Keamanan
*   **Login Aman**: Menggunakan hashing password `Bcrypt` standar industri.
*   **Role-Based Access Control (RBAC)**: Pemisahan akses total antara **Admin** dan **User**.
*   **Google Login Integration**: (Coming Soon) Mendukung login SSO Universitas.
*   **Session Management**: Proteksi halaman ketat berbasis sesi PHP.

### 👤 Portal User (Mahasiswa/Staff)
*   **Dashboard Personal**: Ringkasan aktivitas tiket.
*   **Buat Tiket Baru**: Form pengajuan masalah yang mudah dengan kategori.
*   **History Tiket**: Melacak status pengajuan (Pending, Diproses, Selesai).
*   **Detail Tiket**: Melihat respon status dari tim IT.

### 🛠️ Portal Admin (Tim IT)
*   **Dashboard Monitoring**: Statistik realtime jumlah tiket pending vs selesai.
*   **Manajemen Tiket Masuk**:
    *   Melihat semua tiket masuk.
    *   Filter berdasarkan status (Pending, Resolved, dll).
    *   **Update Status**: Mengubah status tiket (Pending -> In Progress -> Resolved/Rejected).
*   **Manajemen User**: Tambah user baru dan reset password user.

---

## 🚀 Teknologi

Project ini dibangun dengan filosofi **"Simple yet Powerful"**:

*   **Backend**: PHP 8.x (Native, No Framework bloat).
*   **Database**: MySQL / MariaDB.
*   **Frontend**: HTML5, CSS3 (Modern Variables), RemixIcon.
*   **Infrastructure**: Docker & Docker Compose Support.

---

## 💻 Cara Install & Menjalankan (Local Development)

### Prasyarat
*   PHP >= 8.0
*   MySQL Server (bisa via XAMPP/Laragon)
*   Git

### Langkah 1: Clone Repository
```bash
git clone https://github.com/rony-it/it-helpdesk.git
cd it-helpdesk
```

### Langkah 2: Setup Database
1.  Buat database baru di MySQL bernama `it_helpdesk`.
2.  Import file `database/database.sql` ke database tersebut.
    *   File ini berisi struktur tabel dan **data dummy** awal.

### Langkah 3: Konfigurasi Koneksi
Duplikasi/Edit file `config/database.php` dan sesuaikan kredensial Anda:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'it_helpdesk');
define('DB_USER', 'root'); // Sesuaikan user DB lokal
define('DB_PASS', '');     // Sesuaikan password DB lokal
```

### Langkah 4: Jalankan Aplikasi
Jika menggunakan PHP Built-in Server (Cara paling mudah):
**Windows:**
Double klik file `dev.bat`
*atau jalankan di terminal:*
```powershell
.\dev.bat
```

**Linux/Mac:**
```bash
php -S localhost:8000 -t public
```

Buka browser di: **http://localhost:8000**

---

## 🐳 Cara Menjalankan dengan Docker

Jika Anda malas install PHP/MySQL manual, gunakan Docker:

```bash
docker compose up -d --build
```
Aplikasi akan berjalan di port `7000`: **http://localhost:7000**

---

## 🔑 Akun Demo (Default)

Gunakan akun berikut untuk pengujian:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin` | `password123` |
| **User Biasa** | `user` | `password123` |

> ⚠️ **PENTING:** Segera ganti password default ini jika digunakan di production!

---

## 📂 Struktur Project

```
it-helpdesk/
├── config/             # Konfigurasi Database & App
├── public/             # Folder yang terekspos ke publik (Web Root)
│   ├── assets/         # CSS, Images, JS
│   └── index.php       # Entry Point (Router Utama)
├── src/                # Class Logic PHP (Database Connection)
├── views/              # Tampilan HTML (Pages & Partials)
│   ├── actions/        # Script pemroses Form (POST request)
│   ├── partials/       # Potongan kode UI reusable
│   └── dashboard.php   # Layout utama Dashboard
├── database/           # File SQL untuk inisialisasi
├── docker-compose.yml  # Orkestrasi Docker
└── README.md           # Dokumentasi ini
```

---

Built with ❤️ by **Rony (Tim PDSI)**.
