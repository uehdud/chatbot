<?php
$host = 'localhost';
$db = 'new_bot';
$user = 'postgres';
$pass = 'texas123'; // Sesuaikan password Anda

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
