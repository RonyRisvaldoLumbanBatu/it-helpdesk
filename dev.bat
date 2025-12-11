@echo off
echo Starting IT Helpdesk Server...
echo Akses web di: http://localhost:8080/
echo Tekan Ctrl+C untuk berhenti.
php -S localhost:8080 -t public
