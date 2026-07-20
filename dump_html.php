<?php
error_reporting(E_ALL);
$ip = '192.168.1.201';
$uid = '2';
$pwd = '11234';

$jar = __DIR__ . "/c_xray.txt";

// 1. GET /
$ch0 = curl_init("http://$ip/");
curl_setopt_array($ch0, [CURLOPT_RETURNTRANSFER=>1, CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_TIMEOUT=>5]);
$res0 = curl_exec($ch0); curl_close($ch0);
preg_match('/SessionID=(\d+)/i', $res0, $m);
$sid = $m[1] ?? '';

// 2. Login
$ch1 = curl_init("http://$ip/csl/check");
curl_setopt_array($ch1, [CURLOPT_POST=>1, CURLOPT_POSTFIELDS=>"username=$uid&userpwd=$pwd", CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_RETURNTRANSFER=>1, CURLOPT_TIMEOUT=>5, CURLOPT_HTTPHEADER=>["Cookie: SessionID=$sid"]]);
$r1 = curl_exec($ch1); curl_close($ch1);

// 3. GET Query menu to find USER LIST
$ch2 = curl_init("http://$ip/csl/query");
curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER=>1, CURLOPT_COOKIEJAR=>$jar, CURLOPT_COOKIEFILE=>$jar, CURLOPT_TIMEOUT=>5, CURLOPT_HTTPHEADER=>["Cookie: SessionID=$sid"]]);
$html_query = curl_exec($ch2); curl_close($ch2);

file_put_contents(__DIR__ . "/query_page.html", $html_query);
echo "Saved query_page.html. Bytes: " . strlen($html_query) . "\n";
unlink($jar);
?>
