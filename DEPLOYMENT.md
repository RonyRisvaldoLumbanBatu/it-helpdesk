# Panduan Deployment dengan Docker Compose (Recommended)

Server ini menggunakan Docker Compose. Pastikan Docker sudah terinstall.

## 1. Persiapan Folder di Server
Masuk ke folder `/srv` atau tempat Anda ingin menaruh project.
```bash
cd /srv
sudo git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk
```

## 2. Jalankan Container
Cukup jalankan satu perintah ini. Docker akan otomatis:
- Membangun environment PHP & Apache (Web)
- Membuat database MySQL
- Mengimport tabel dari `database/database.sql`
- Menjalankan migrasi kolom tambahan

```bash
sudo docker compose up -d --build
```
*(Tunggu proses download & build selesai)*

## 3. Migrasi Google dan Cek Status
Container database butuh waktu beberapa detik untuk "bangun" pertama kali.
Tunggu 10-15 detik, lalu jalankan migrasi manual untuk memastikan kolom Google Login tersedia:

```bash
sudo docker compose exec web php public/migrate_google.php
```

## 4. Setting Nginx Proxy Manager (NPM)
Aplikasi sekarang berjalan di port **7000**.

1. Buka Admin NPM.
2. Add Proxy Host.
3. Forward Host: IP Server Docker (misal `192.168.10.77` atau `172.17.0.1` gateway docker).
4. Forward Port: `7000`.
5. Save.

Selesai!

---
**Catatan:**
- Database data tersimpan aman di volume `mysql_data`.
- Jika ada update code: `git pull` lalu `docker compose up -d --build`.
