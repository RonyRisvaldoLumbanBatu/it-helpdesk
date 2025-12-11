@echo off
echo Starting IT Helpdesk Server...
echo Akses web di: http://localhost:8080/?page=change_password
echo Tekan Ctrl+C untuk berhenti.
C:\xampp\php\php.exe -S localhost:8080 -t public
