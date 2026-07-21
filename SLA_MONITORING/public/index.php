<?php

// Handle static files for PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($path && is_file($path) && strpos($path, __DIR__) === 0) {
        return false;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Routing\Router;

// Load environment variables
try {
    if (file_exists(__DIR__ . '/../.env')) {
        Env::load(__DIR__ . '/../.env');
    } elseif (file_exists(__DIR__ . '/../../.env')) {
        Env::load(__DIR__ . '/../../.env');
    } else {
        Env::load(__DIR__ . '/../.env'); // Biarkan melempar exception standar jika keduanya tidak ada
    }
} catch (\Exception $e) {
    // If .env is missing, we log it and continue if variables are provided in system env.
    error_log($e->getMessage());
}

// Set default timezone
date_default_timezone_set(Env::get('TIMEZONE', 'Asia/Jakarta'));

// Initialize Router
$router = new Router();

// Log requests for debugging
file_put_contents(__DIR__ . '/../logs/request.log', date('[Y-m-d H:i:s] ') . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . PHP_EOL, FILE_APPEND);

// Load routes
require_once __DIR__ . '/../routes/api.php';

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Fix for some server configurations where REQUEST_URI might include query string or subfolder
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// If the app is running in a subfolder, we should strip the subfolder path.
$basePath = $_SERVER['SCRIPT_NAME']; // E.g., /SLA_MONITORING/public/index.php
$baseDir = dirname($basePath);       // E.g., /SLA_MONITORING/public

if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
} elseif (str_starts_with($uri, $baseDir)) {
    $uri = substr($uri, strlen($baseDir));
}

// Ensure the URI has a leading slash
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

$router->dispatch($method, $uri);
