@echo off
:: jalankan_cron.bat
:: File pemicu sinkronisasi fingerprint otomatis

set PHP_EXE="C:\xampp\php\php.exe"
set SCRIPT_PATH="C:\xampp\htdocs\logklikdsi-main\cron_sync.php"

echo Menjalankan Sinkronisasi Fingerprint...
%PHP_EXE% %SCRIPT_PATH%
echo Selesai.
