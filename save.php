<?php
require 'db.php';

try {
    $menu_name = $_POST['menu_name'];
    $parent_id = $_POST['parent_id'] ?: null;
    $response = $_POST['response'] ?: null;

    $stmt = $pdo->prepare("INSERT INTO menu (menu_name, parent_id, response) VALUES (?, ?, ?)");
    $stmt->execute([$menu_name, $parent_id, $response]);

    header("Location: index.php?alert[type]=success&alert[message]=Menu berhasil ditambahkan!");
} catch (PDOException $e) {
    header("Location: index.php?alert[type]=danger&alert[message]=Gagal menambahkan menu: " . $e->getMessage());
}
