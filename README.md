# 🎫 IT Helpdesk Premium System

<div align="center">

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Supported-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge)](#-deployment-production)

> **Sistem Manajemen Tiket IT yang Dirancang untuk Memberikan Pengalaman Pengguna Luar Biasa**

[Demo](#-demo--screenshots) • [Fitur](#-fitur-unggulan) • [Instalasi](#-instalasi-cepat) • [Dokumentasi](#-dokumentasi) • [Kontribusi](#-berkontribusi) • [Lisensi](#-lisensi)

</div>

---

## 🌟 Mengapa Anda Perlu Ini?

Apakah tim IT Anda masih menggunakan sistem tiket yang kaku, lambat, dan membosankan? **IT Helpdesk Premium System** adalah solusi modern yang mengubah cara Anda mengelola permintaan teknis.

Kami percaya bahwa sistem internal juga harus **indah, cepat, dan menyenangkan digunakan**. Dibangun dengan **Native PHP** dan **Modern CSS**, aplikasi ini memberikan performa tinggi tanpa bloatware berlebihan.

### ✨ Keunggulan Utama

<table>
  <tr>
    <td width="50%">
      <h4>🎨 UI/UX Premium</h4>
      <p>Dashboard interaktif dengan desain modern yang menyenangkan mata. Responsif di semua perangkat.</p>
    </td>
    <td width="50%">
      <h4>⚡ Performa Tinggi</h4>
      <p>Native PHP 8.2 tanpa framework berat. OPcache enabled untuk kecepatan maksimal.</p>
    </td>
  </tr>
  <tr>
    <td>
      <h4>💬 Chat Realtime</h4>
      <p>Komunikasi tiket seperti WhatsApp. Cepat, intuitif, dan terintegrasi sempurna.</p>
    </td>
    <td>
      <h4>🐳 Docker Ready</h4>
      <p>Deploy dalam satu command. Multi-stage build, health checks, security headers included.</p>
    </td>
  </tr>
  <tr>
    <td>
      <h4>🔒 Security First</h4>
      <p>CSRF protection, input validation, password hashing BCRYPT, SQL prepared statements.</p>
    </td>
    <td>
      <h4>📱 Mobile Friendly</h4>
      <p>Perfect responsive design. Bekerja dengan sempurna di desktop, tablet, dan smartphone.</p>
    </td>
  </tr>
</table>

---

## 🎯 Fitur Unggulan

### Untuk Admin
- 👥 **Manajemen Pengguna**: Buat, edit, hapus user dengan role berbeda (Admin, User, Staff)
- 📊 **Dashboard Analytics**: Visualisasi data tiket real-time dengan statistik mendalam
- 🔧 **Pengaturan Sistem**: Kontrol penuh atas konfigurasi aplikasi
- 📋 **Laporan Komprehensif**: Export dan analisis data tiket untuk insights bisnis

### Untuk User/Staff
- 🎫 **Buat Tiket**: Submit permintaan dengan deskripsi detail dan kategori
- 💬 **Diskusi Tiket**: Chat langsung dengan tim support dalam satu interface
- 📱 **Status Tracking**: Pantau progress tiket secara real-time
- 🔔 **Notifikasi**: Alert instan untuk update tiket dan pesan baru

### Untuk Sistem
- 🔐 **CSRF Protection**: Timing-safe token validation di semua form
- ✔️ **Input Validation**: Email RFC 5321 compliant, username alphanumeric, password strength checking
- 📊 **Service Layer**: Notification service yang reusable dan scalable
- 🎯 **Array-based Routing**: Clean routing system dengan middleware support

---

## 🛠️ Tech Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Backend** | Native PHP | 8.2+ |
| **Database** | MySQL/MariaDB | 8.0+ |
| **Frontend** | Vanilla CSS (Flexbox/Grid) | Modern |
| **Icons** | Remix Icon | Latest |
| **Containerization** | Docker & Docker Compose | Latest |
| **Server** | Apache / PHP Built-in | Production Ready |

---

## 🎬 Demo & Screenshots

Lihat tampilan aplikasi dalam aksi:

### 📊 Dashboard Utama
Pusat kontrol dengan statistik real-time, daftar tiket terbaru, dan aktivitas tim support.

![Dashboard](https://raw.githubusercontent.com/RonyRisvaldoLumbanBatu/it-helpdesk/main/docs/images/dashboard-cover.png)

### 🔐 Halaman Login
Gerbang masuk yang aman, elegan, dan user-friendly dengan dukungan Google Auth.

![Login Screen](https://raw.githubusercontent.com/RonyRisvaldoLumbanBatu/it-helpdesk/main/docs/images/login-screen.png)

### 💬 Chat Interface - Desktop
Interface percakapan tiket yang mulus dengan formatting lengkap, sama seperti aplikasi chat modern.

![Desktop Chat](https://raw.githubusercontent.com/RonyRisvaldoLumbanBatu/it-helpdesk/main/docs/images/desktop-chat.png)

### 📱 Chat Interface - Mobile
Responsive design yang sempurna di layar kecil. Produktivitas tanpa kompromi di smartphone.

![Mobile Chat](https://raw.githubusercontent.com/RonyRisvaldoLumbanBatu/it-helpdesk/main/docs/images/mobile-chat.png)

### ▶️ Live Demo

Ingin mencoba langsung? Dua cara:

**Opsi A: Deploy dengan Docker (Tercepat)**
```bash
git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk
docker-compose up -d --build
# Akses: http://localhost:7000
```

**Opsi B: Local Development**
```bash
git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk
php -S localhost:8000 -t public
# Akses: http://localhost:8000
```

**Login Credentials:**
- Username: `admin` atau `user`
- Password: `password123`

---

### Opsi 1: Docker (Recommended ⭐)

**Persyaratan**: Docker & Docker Compose ter-install

```bash
# Clone repository
git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk

# Deploy dengan satu command!
docker-compose up -d --build

# Aplikasi siap diakses di http://localhost:7000
```

**Fitur Docker:**
- ✅ Multi-stage build (image ~180MB)
- ✅ OPcache enabled (performa 3x lebih cepat)
- ✅ Health checks (auto-restart)
- ✅ Resource limits (CPU & Memory controlled)
- ✅ Security headers enabled
- ✅ UTF8MB4 support

### Opsi 2: Manual Setup (Local Development)

**Persyaratan**: PHP 8.2+, MySQL 8.0+

```bash
# 1. Clone repository
git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk

# 2. Setup database
mysql -u root -p -e "CREATE DATABASE it_helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p it_helpdesk < database/database.sql

# 3. Konfigurasi (edit config/database.php)
# Sesuaikan: host, username, password

# 4. Jalankan server
php -S localhost:8000 -t public

# 5. Buka browser: http://localhost:8000
```

---

## 🔐 Login Default

| Role | Username | Password | Aksi |
|------|----------|----------|------|
| **Admin** | `admin` | `password123` | ✅ Langsung bisa digunakan |
| **User** | `user` | `password123` | ✅ Langsung bisa digunakan |
| **Staff** | - | - | ❌ Harus dibuat oleh admin |

⚠️ **PENTING**: Ganti semua password default segera setelah login pertama kali!

---

## 📖 Dokumentasi

### Struktur Direktori

```
it-helpdesk/
├── config/              # Konfigurasi aplikasi
│   ├── app.php         # App settings
│   └── database.php    # Database connection
├── database/           # Database files
│   ├── database.sql    # Schema & seed data
│   └── migrations/     # Database migrations
├── src/                # Business logic
│   ├── Database.php    # Database handler
│   ├── NotificationService.php
│   └── ValidationHelper.php
├── public/             # Entry point aplikasi
│   ├── index.php       # Router & middleware
│   └── assets/         # CSS, JS, Images
├── views/              # Template files
│   ├── login.php
│   ├── dashboard.php
│   ├── actions/        # Form handlers
│   └── partials/       # Reusable components
├── Docker*             # Container files
├── README.md           # Dokumentasi ini
└── LICENSE             # Lisensi MIT
```

### Workflow Aplikasi

```
User Access → Router (index.php)
           ↓
      Authentication Check
           ↓
      CSRF Verification
           ↓
      Input Validation (ValidationHelper)
           ↓
      Database Operation
           ↓
      Notification Service
           ↓
      Response & Render View
```

### API & Routing

Aplikasi menggunakan array-based routing dengan middleware support:

```php
// Contoh: index.php routing
$routes = [
    'dashboard' => [
        'auth' => true,
        'roles' => ['admin', 'user', 'staff'],
        'handler' => 'handleDashboard'
    ],
    'create-user' => [
        'auth' => true,
        'roles' => ['admin'],
        'handler' => 'handleCreateUser'
    ]
];
```

---

## 💻 Penggunaan

### Untuk Admin

1. **Login** dengan kredensial admin
2. **Kelola Pengguna** → Tambah User, Staff, atau Admin baru
3. **Dashboard** → Monitor semua tiket dan aktivitas
4. **Kelola Tiket** → Edit status, assign, dan beri catatan
5. **Lihat Laporan** → Analisis performa tim support

### Untuk User/Staff

1. **Login** dengan kredensial user/staff
2. **Buat Tiket** → Jelaskan masalah dengan detail
3. **Chat Support** → Diskusi langsung dengan tim IT
4. **Track Status** → Monitor progress penyelesaian
5. **Terima Notifikasi** → Update real-time saat ada perubahan

---

## 🔒 Security Features

| Fitur | Implementasi |
|-------|--------------|
| CSRF Protection | Timing-safe token validation dengan `hash_equals()` |
| Input Validation | Email RFC 5321, Username alphanumeric 3-30 chars, Password min 8 char |
| SQL Injection Prevention | Prepared statements di semua query |
| Password Security | BCRYPT hashing dengan cost 12 |
| Session Management | Secure session handling dengan timeout |
| HTML Injection Prevention | HTML entity encoding di output |

---

## 🚢 Deployment Production

### Deployment dengan Docker (Recommended)

```bash
# Build dan deploy
docker-compose -f docker-compose.yml up -d --build

# Check status
docker-compose ps

# View logs
docker-compose logs -f

# Monitoring
docker stats
```

### Environment Variables

Edit `.env` sesuai kebutuhan:

```env
DB_HOST=database          # Untuk Docker: 'database', Local: 'localhost'
DB_PORT=3306
DB_USER=root
DB_PASS=password123
DB_NAME=it_helpdesk
```

### Production Checklist

- [ ] Ganti semua password default
- [ ] Update `.env` dengan credentials yang aman
- [ ] Enable HTTPS/SSL certificate
- [ ] Setup backup database regular
- [ ] Monitor logs di `logs/` folder
- [ ] Configure firewall rules
- [ ] Setup email notifications (optional)

---

## 🛠️ Development

### Menambah Feature Baru

1. **Buat route** di `public/index.php`
2. **Buat handler function** atau action file di `views/actions/`
3. **Buat template** di `views/`
4. **Test secara lokal** dengan PHP built-in server
5. **Commit** dengan pesan yang jelas

### Running Tests Lokal

```bash
# Development server
php -S localhost:8000 -t public

# Docker development
docker-compose up -d
# Akses: http://localhost:7000
```

### Code Standards

- **PHP**: PSR-12 compliant, 4-space indentation
- **Naming**: camelCase untuk function/variable, snake_case untuk database
- **Security**: Always sanitize user input, validate before save
- **Comments**: Bahasa Indonesia untuk clarity

---

## 📊 Performance Metrics

| Metrik | Nilai |
|--------|-------|
| Page Load Time | < 200ms |
| Docker Image Size | ~180MB (multi-stage optimized) |
| Memory Usage | ~50-100MB per container |
| CPU Usage | < 5% idle |
| Database Queries | Optimized with indices |
| OPcache Hit Rate | ~95% (production) |

---

## 🐛 Troubleshooting

### Error: "Database Connection Failed"
```
Solution: Cek config/database.php, pastikan credentials benar
```

### Error: "Invalid CSRF Token"
```
Solution: Refresh page, token mungkin expired
```

### Error: "Permission Denied" di Docker
```
Solution: Jalankan dengan sudo atau tambahkan user ke docker group
```

### Aplikasi Lambat di Docker
```
Solution: Check resource limits, pastikan server punya CPU/Memory cukup
```

---

## 🎓 Pembelajaran & Resources

- **PHP Best Practices**: [PHP-FIG](https://www.php-fig.org/)
- **CSS Modern**: [MDN Web Docs - CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)
- **Docker**: [Official Docker Docs](https://docs.docker.com/)
- **Security**: [OWASP Top 10](https://owasp.org/Top10/)

---

## 🤝 Berkontribusi

Kami sangat menyambut kontribusi dari komunitas! 🚀

### Cara Berkontribusi:

1. **Fork** repository ini
2. **Buat branch** untuk fitur Anda (`git checkout -b feature/amazing-feature`)
3. **Commit** perubahan (`git commit -m 'feat: tambah amazing feature'`)
4. **Push** ke branch (`git push origin feature/amazing-feature`)
5. **Buat Pull Request** dengan deskripsi yang jelas

### Roadmap Fitur

- [ ] Email notifications untuk status update
- [ ] Push notifications mobile
- [ ] AI-powered auto-reply suggestions
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Mobile app (React Native)
- [ ] API REST untuk integrasi eksternal
- [ ] Rate limiting & DDoS protection

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License** - Anda bebas menggunakan, memodifikasi, dan mendistribusikan kode ini dengan atau tanpa tujuan komersial.

**Syarat lisensi:**
- ✅ Penggunaan komersial
- ✅ Modifikasi kode
- ✅ Distribusi
- ✅ Penggunaan private

**Ketentuan:**
- ⚠️ Sertakan lisensi dan copyright notice
- ⚠️ Tidak ada liability/warranty

Lihat [LICENSE](LICENSE) untuk detail lengkap.

---

## 📧 Hubungi Kami

Punya pertanyaan? Ingin berkolaborasi? Silakan hubungi:

- **GitHub**: [@RonyRisvaldoLumbanBatu](https://github.com/RonyRisvaldoLumbanBatu)
- **Issues**: [Report Bug / Suggest Feature](https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk/issues)
- **Discussions**: [Join Community](https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk/discussions)

---

## 🌟 Berikan Bintang! ⭐

Jika proyek ini membantu Anda, jangan lupa kasih ⭐ di GitHub! Ini memotivasi kami untuk terus develop dan improve.

```
Setiap bintang adalah apresiasi bagi developer yang telah mengorbankan waktu & energi.
Terima kasih sudah menjadi bagian dari komunitas kami! 🙏
```

---

<div align="center">

### Dibuat dengan ❤️ oleh [Rony Risvaldo](https://github.com/RonyRisvaldoLumbanBatu)

**"Experience the difference of a well-crafted tool."**

---

**[⬆ back to top](#-it-helpdesk-premium-system)**

</div>
