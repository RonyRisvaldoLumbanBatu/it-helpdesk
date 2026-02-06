# Panduan Kontribusi

Terima kasih sudah tertarik berkontribusi! 🎉

Dokumen ini menjelaskan alur kontribusi agar perubahan Anda konsisten, aman, dan mudah direview.

---

## 📌 Ruang Lingkup Kontribusi

Anda dapat membantu dalam:
- Perbaikan bug
- Peningkatan performa
- Peningkatan keamanan
- Peningkatan dokumentasi
- Fitur baru (sesuai roadmap)

---

## ✅ Cara Berkontribusi

1. **Fork** repository ini
2. **Buat branch** baru:
   ```bash
   git checkout -b feature/nama-fitur
   ```
3. **Lakukan perubahan** sesuai kebutuhan
4. **Pastikan proyek berjalan**:
   ```bash
   php -S localhost:8000 -t public
   ```
5. **Commit** dengan pesan jelas:
   ```bash
   git commit -m "feat: tambah fitur baru"
   ```
6. **Push** ke branch Anda:
   ```bash
   git push origin feature/nama-fitur
   ```
7. **Buat Pull Request** (PR)

---

## 🧩 Standar Kode

- **PHP**: PSR-12 (4 spasi)
- **Nama fungsi**: `camelCase`
- **Nama database**: `snake_case`
- **Validasi input** wajib sebelum menyimpan ke database
- **Jangan commit data sensitif** seperti `.env`

---

## 🧪 Testing Manual

- Login sebagai Admin & User
- Cek pembuatan tiket
- Cek chat/komentar tiket
- Cek update status tiket
- Cek notifikasi

---

## 📝 Scope PR yang Disarankan

- PR kecil dan terfokus
- Jelaskan perubahan di deskripsi PR
- Sertakan screenshot jika mengubah UI

---

## 📣 Butuh Bantuan?

Buat issue di:
- https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk/issues

Terima kasih! 🙌
