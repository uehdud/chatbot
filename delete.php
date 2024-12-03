<?php
require 'db.php';

try {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php?alert[type]=success&alert[message]=Info berhasil dihapus!");
} catch (PDOException $e) {
    header("Location: index.php?alert[type]=danger&alert[message]=Gagal hapus info: " . $e->getMessage());
}
