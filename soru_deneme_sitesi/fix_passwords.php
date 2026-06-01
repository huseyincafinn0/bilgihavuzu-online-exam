<?php
require_once 'database.php';
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$ogrenci_hash = password_hash('ogrenci123', PASSWORD_DEFAULT);

$db->query("UPDATE users SET password = '$admin_hash' WHERE username = 'admin'");
$db->query("UPDATE users SET password = '$ogrenci_hash' WHERE username = 'ogrenci'");
echo "Passwords updated.";
?>
