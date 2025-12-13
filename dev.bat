@echo off
cd public
echo Server started! Access via http://localhost:8000
echo Untuk akses dari HP, gunakan IP Address laptop Anda (cek dengan ipconfig), misal http://192.168.1.x:8000
"C:\xampp\php\php.exe" -S 0.0.0.0:8000
