<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['user_id'], $input['message'], $input['jenis'], $input['sesi'])) {
        $user_id = $input['user_id'];
        $message = $input['message'];
        $jenis = $input['jenis'];
        $sesi = $input['sesi'];

        // Simpan log ke database
        $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (:user_id, :isi_respon, :jenis, :sesi)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':isi_respon' => $message,
            ':jenis' => $jenis,
            ':sesi' => $sesi
        ]);

        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An unexpected error occurred.']);
}
