<?php
$ip = '192.168.1.201';

// Step 1: Login
$login_url = "http://$ip/csl/check";
$postFields = "sUserName=2&sPwd=11234&bacth=";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
curl_close($ch);

// Ambil Header Cookie (SessionID)
$session_id = '';
if (preg_match('/SessionID=(\w+)/i', $response, $matches)) {
    $session_id = $matches[1];
}

if (!$session_id) {
    echo "Gagal dapat SessionID.\n";
    die();
}

// Step 2: Download raw attlog
$download_url = "http://$ip/form/Download?SessionID=$session_id";
$dl_post = "sdate=2020-01-01&edate=2026-12-31&period=7&uid=0";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $download_url);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $dl_post);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Cookie: SessionID=$session_id"]);
$attlog = curl_exec($ch2);
curl_close($ch2);

echo "Panjang file yang di-download: " . strlen($attlog) . " bytes\n";
echo "Preview 500 karakter pertama:\n";
echo substr($attlog, 0, 500) . "\n";
?>
