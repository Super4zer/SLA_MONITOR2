<?php
// client_logout.php
session_start();

// Unset only client session variables so internal DSI login is not affected if they happen to use the same browser
unset($_SESSION['CLIENT_ISLOGIN']);
unset($_SESSION['CLIENT_ID_PK']);
unset($_SESSION['CLIENT_IDUSER']);
unset($_SESSION['CLIENT_NAMA']);
unset($_SESSION['CLIENT_KODCUSTOMER']);

header("Location: client_login.php");
exit;
?>
