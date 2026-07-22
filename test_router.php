<?php
require_once __DIR__ . '/SLA_MONITORING/vendor/autoload.php';
require_once __DIR__ . '/SLA_MONITORING/src/Routing/Router.php';
$router = new \App\Routing\Router();
require_once __DIR__ . '/SLA_MONITORING/routes/api.php';
$router->dispatch('POST', '/webhook/wablas');
