<?php
// client_islogin.php
session_start();

function client_islogin() {
    if (isset($_SESSION['CLIENT_ISLOGIN']) && $_SESSION['CLIENT_ISLOGIN'] == "cLi3nt_s3cr3t_2026") {
        return true;
    }
    return false;
}

if (!client_islogin()) {
    header("Location: client_login.php");
    exit;
}

// Global variables for client
$client_id_user_pk = $_SESSION['CLIENT_ID_PK'];
$client_iduser = $_SESSION['CLIENT_IDUSER'];
$client_nama = $_SESSION['CLIENT_NAMA'];
$client_default_kodcustomer = $_SESSION['CLIENT_KODCUSTOMER'];

// Fetch allowed menus
require_once 'dbase.php';
$client_allowed_menus = [];
try {
    $stmt_akses = $conn->prepare("SELECT id_menu_client FROM r_akses_client WHERE id_user_client = ? AND stsaktif = 1");
    $stmt_akses->execute([$client_id_user_pk]);
    while ($row = $stmt_akses->fetch(PDO::FETCH_ASSOC)) {
        $client_allowed_menus[] = $row['id_menu_client'];
    }
} catch (PDOException $e) {}

// Helper function to check menu access
function client_has_menu($menu_id) {
    global $client_allowed_menus;
    return in_array($menu_id, $client_allowed_menus);
}
?>
