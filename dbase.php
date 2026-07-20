<?php
$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    die("Fatal Error: File .env tidak ditemukan di: " . $envPath);
}

$env = [];
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue; 
    
    if (strpos($line, '=') !== false) {
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

// Menggunakan nama kunci yang SESUAI dengan hasil debug
$servername = $env['DB_HOST'];
$port       = $env['DB_PORT'];
$dbname     = $env['DB_NAME'];
$username   = $env['DB_USERNAME']; // Sesuaikan dengan DB_USERNAME
$password   = $env['DB_PASSWORD']; // Sesuaikan dengan DB_PASSWORD

$secret_key = isset($env['SECRET_KEY']) ? $env['SECRET_KEY'] : 'default_secret';
define('SECRET_KEY', $secret_key);
define('ENCRYPT_METHOD', 'aes-256-gcm');

try {
    $dsn = "mysql:host={$servername};port={$port};dbname={$dbname}";
    $conn = new PDO($dsn, $username, $password);
    
    // Set PDO error mode ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>