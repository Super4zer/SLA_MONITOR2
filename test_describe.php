<?php
include "dbase.php";
$stmt = $conn->query("DESCRIBE ruser");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
