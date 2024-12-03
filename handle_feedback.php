<?php
require 'db.php'; // Koneksi database

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$feedback = $data['feedback'] ?? null;

// Validasi input
if (!$user_id || !is_numeric($user_id) || !$feedback) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input.',
    ]);
    exit;
}

try {
    // Simpan tanggapan ke chat_logs
    $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'user', 'progress')");
    $stmt->execute([$user_id, "Anda menjawab: $feedback"]);

    echo json_encode([
        'success' => true,
        'message' => 'Tanggapan berhasil disimpan.',
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
    ]);
}
