# Panduan Deployment ke Ubuntu Server (192.168.10.77)

Panduan ini untuk menginstall IT Helpdesk di server Ubuntu baru dari nol.

## 1. Persiapan Server (Sekali Saja)
Masuk ke server via SSH atau Terminal langsung:
```bash
ssh user@192.168.10.77
```

Install Web Server, PHP, dan MySQL:
```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-mysql php-curl mysql-server git unzip -y
```

Pastikan Apache berjalan:
```bash
sudo systemctl enable apache2
sudo systemctl start apache2
```

## 2. Setup Database
Masuk ke MySQL console:
```bash
sudo mysql
```

Copy-paste perintah SQL berikut (Ganti 'password_rahasia' dengan password database yang diinginkan):
```sql
CREATE DATABASE it_helpdesk;
CREATE USER 'helpdesk_admin'@'localhost' IDENTIFIED BY 'password_rahasia';
GRANT ALL PRIVILEGES ON it_helpdesk.* TO 'helpdesk_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Clone Aplikasi
Kita akan taruh aplikasi di folder web server `/var/www/html/it-helpdesk`.

```bash
cd /var/www/html
sudo git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
sudo chown -R www-data:www-data it-helpdesk
sudo chmod -R 755 it-helpdesk
```

## 4. Konfigurasi Aplikasi
Masuk ke folder app dan buat file database config production:
```bash
cd it-helpdesk
cp config/database.php.example config/database.php
nano config/database.php
```
*Edit file tersebut, masukkan 'helpdesk_admin' dan 'password_rahasia' yang dibuat di langkah 2.*

Import Struktur Tabel:
```bash
mysql -u helpdesk_admin -p it_helpdesk < database/database.sql
```
*(Masukkan password saat diminta)*

Jalankan Script Migrasi (Untuk Login Google):
```bash
php public/migrate_google.php
```

## 5. Konfigurasi Apache (Virtual Host)
Agar URL-nya cantik (tanpa `public/index.php`).

```bash
sudo nano /etc/apache2/sites-available/it-helpdesk.conf
```

Isi dengan script ini:
```apache
<VirtualHost *:80>
    ServerAdmin admin@192.168.10.77
    DocumentRoot /var/www/html/it-helpdesk/public
    
    <Directory /var/www/html/it-helpdesk/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Aktifkan config baru:
```bash
sudo a2ensite it-helpdesk.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## 6. Update Aplikasi (Jika ada perubahan baru dari Laptop)
Jika kamu sudah push kode baru dari laptop ke GitHub, jalankan ini di server untuk update:

```bash
cd /var/www/html/it-helpdesk
sudo git pull origin main
```
Selesai! Akses aplikasi di `http://192.168.10.77/`
