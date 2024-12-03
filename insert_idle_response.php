<?php
require 'db.php'; // File koneksi database

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;

// Validasi user_id
if (!$user_id || !is_numeric($user_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing user ID.',
    ]);
    exit;
}

try {
    // Insert pesan idle ke chat_logs
    $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'bot', 'progress')");
    $stmt->execute([$user_id, 'Apakah Informasi ini membantu ?']);

    echo json_encode([
        'success' => true,
        'message' => 'Pesan idle berhasil disimpan.',
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
    ]);
}
