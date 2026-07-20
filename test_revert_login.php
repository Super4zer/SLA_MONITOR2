<?php
/**
 * test_revert_login.php
 * Mengetes fungsi finger_login asli dari Modul 57
 */
require_once "57_sync_finger.php";

$ip = "192.168.1.49";
$uid = "2";
$pass = "11234";

echo "<h3>🔎 TESTING ORIGINAL WEB LOGIN (PORT 80)</h3>";
echo "Mencoba login ke <b>$ip</b>...<br>";

$res = finger_login($ip, $uid, $pass);

if (isset($res['error'])) {
    echo "<span style='color:red;'><b>GAGAL!</b> " . $res['error'] . "</span><br>";
} else {
    echo "<span style='color:green;'><b>SUKSES!</b> Berhasil Login ke Mesin via Port 80.</span><br>";
    echo "Session ID: " . $res[1] . "<br>";
    echo "<br><b style='color:blue;'>KESIMPULAN: Mode Web bekerja sempurna di IP .49. Masalah Selesai!</b>";
}
?>
