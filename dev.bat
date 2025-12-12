@echo off
setlocal

:: Try to find PHP
set PHP_BIN=php

if exist "C:\xampp\php\php.exe" (
    set PHP_BIN="C:\xampp\php\php.exe"
)
if exist "C:\php\php.exe" (
    set PHP_BIN="C:\php\php.exe"
)

echo Found PHP at: %PHP_BIN%
echo Starting Server at http://localhost:8000
echo Tekan CTRL+C untuk stop.

%PHP_BIN% -S localhost:8000 -t public

if %errorlevel% neq 0 (
    echo.
    echo GAGAL: PHP tidak ditemukan atau Port 8000 terpakai.
    echo Pastikan XAMPP/PHP terinstall dengan benar.
    echo Path yang dicoba: %PHP_BIN%
)

pause
