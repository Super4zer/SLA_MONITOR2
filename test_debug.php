<?php
session_start();
error_reporting(E_ALL);
// Mock PDO to prevent errors
class MockPDOStmt {
    public function execute($params = []) { return true; }
    public function fetch() { return false; }
}
class MockPDO {
    public function query($sql) { return new MockPDOStmt(); }
    public function prepare($sql) { return new MockPDOStmt(); }
}
$conn = new MockPDO();

// Tangkap output html dari include
ob_start();
include "57_sync_finger.php";
$included_html = ob_get_clean();

echo "Running Login...\n";
$ip = '192.168.1.201';
$login = finger_login($ip, '2', '11234');

if (!$login) {
    echo "Login Failed.\n";
} else {
    echo "Login OK. SID: " . $login[1] . "\n";
    echo "Fetch query page...\n";
    [$jar, $sid] = $login;
    $ch = curl_init("http://$ip/csl/query?SessionID=$sid");
    curl_setopt_array($ch, [
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Cookie: SessionID=$sid"]
    ]);
    $query_page = curl_exec($ch); curl_close($ch);
    // STEP 4: Dump User Administration Pages
    $endpoints = [
        "http://$ip/csl/user"     => "response_user.html",
        "http://$ip/csl/user?action=add"      => "response_user_add.html",
    ];
    foreach ($endpoints as $url => $file) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_COOKIEJAR      => $jar,
            CURLOPT_COOKIEFILE     => $jar,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Cookie: SessionID=$sid"],
        ]);
        
        $r = curl_exec($ch);
        curl_close($ch);
        
        file_put_contents($file, $r);
        echo "Saved $url to $file\n";
    }

}
?>
