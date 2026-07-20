<?php
require "zklib.php";
$ip = "192.168.1.201";
echo "Testing connection to $ip via ZKTeco library...\n";
$zk = new ZKTeco($ip);
if ($zk->connect()) {
    echo "SUCCESS: Connected via PHP Sockets!\n";
    $zk->disconnect();
} else {
    echo "FAILED: Could not connect via PHP Sockets. Please check if port 4370 is open over UDP.\n";
}
