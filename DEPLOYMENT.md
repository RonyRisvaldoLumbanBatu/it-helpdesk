# Panduan Deployment ke Server dengan Nginx Proxy Manager

Karena server sudah ada Nginx Proxy Manager, kita tidak perlu menginstall Apache. Kita akan menjalankan aplikasi menggunakan PHP Built-in Server.

## 1. Hapus Apache (Jika terlanjur install)
Agar tidak konflik port.
```bash
sudo systemctl stop apache2
sudo apt remove apache2 -y
sudo apt autoremove -y
```

## 2. Install PHP & Database
```bash
sudo apt update
sudo apt install php php-mysql php-cli mysql-server git unzip -y
```

## 3. Setup Database (Sekali Saja)
Masuk ke MySQL:
```bash
sudo mysql
```

Copy-paste:
```sql
CREATE DATABASE it_helpdesk;
CREATE USER 'helpdesk_admin'@'localhost' IDENTIFIED BY 'password_rahasia';
GRANT ALL PRIVILEGES ON it_helpdesk.* TO 'helpdesk_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 4. Install Aplikasi
```bash
cd /var/www
sudo mkdir html
cd html
sudo git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
cd it-helpdesk

# Config Database
cp config/database.php.example config/database.php
nano config/database.php
# (Isi user 'helpdesk_admin' dan password tadi)

# Import DB
mysql -u helpdesk_admin -p it_helpdesk < database/database.sql

# Migrasi Google Login
php public/migrate_google.php
```

## 5. Jalankan Aplikasi di Port 7000
Kita akan menjalankan PHP server di background.

**Cara Simpel (Command Line):**
```bash
nohup php -S 0.0.0.0:7000 -t public > /dev/null 2>&1 &
```
*(Aplikasi sekarang hidup di port 7000 selamanya, meski terminal ditutup)*

**Cara Profesional (Systemd Service) - Disarankan:**
Agar kalau server restart, aplikasi nyala sendiri.

1. Buat file service:
```bash
sudo nano /etc/systemd/system/helpdesk.service
```

2. Isi file tersebut:
```ini
[Unit]
Description=IT Helpdesk PHP Server
After=network.target

[Service]
User=root
WorkingDirectory=/var/www/html/it-helpdesk
ExecStart=/usr/bin/php -S 0.0.0.0:7000 -t public
Restart=always

[Install]
WantedBy=multi-user.target
```

3. Aktifkan Service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable helpdesk
sudo systemctl start helpdesk
```

Cek status:
```bash
sudo systemctl status helpdesk
```

## 6. Setting Nginx Proxy Manager (Di Browser)
1. Buka Admin Nginx Proxy Manager kamu.
2. Add **Proxy Host**.
3. **Domain Names**: Isi domain atau IP (misal `helpdesk.local` atau `192.168.10.77`).
4. **Forward Host**: `127.0.0.1` (karena PHP jalan di server yg sama).
5. **Forward Port**: `7000`.
6. Save.

Selesai! Web helpdesk aman berjalan tanpa mengganggu web lain.
