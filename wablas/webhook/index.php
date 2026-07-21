<?php
/**
 * Webhook Proxy — /wablas/webhook
 * 
 * File ini meneruskan payload dari Wablas ke WebhookController 
 * di SLA_MONITORING, sehingga URL webhook yang didaftarkan di Wablas
 * cukup: https://surging-duplicate-tutu.ngrok-free.dev/wablas/webhook
 */

// Bootstrap SLA_MONITORING
require_once __DIR__ . '/../../SLA_MONITORING/vendor/autoload.php';

use App\Config\Env;

// Load .env SLA_MONITORING
$envPath = __DIR__ . '/../../SLA_MONITORING/.env';
if (file_exists($envPath)) {
    Env::load($envPath);
}

// Set timezone
$tz = Env::get('TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set($tz);

// Jalankan webhook handler
$controller = new \App\Controllers\WebhookController();
$result = $controller->handle();

// Kirim response JSON
header('Content-Type: application/json');
echo json_encode($result);
