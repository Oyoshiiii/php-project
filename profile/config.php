<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mangawebsite');
define('DB_USER', 'root');
define('DB_PASS', '12345');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>