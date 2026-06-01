<?php
// database.php
require_once 'config.php';

try {
    // charset kısmını utf8mb4 yaptık
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Veritabanı iletişim dilini de utf8mb4 olarak zorlayalım
    $db->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    // Hata mesajını temiz Türkçe karakterlerle yazalım
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>