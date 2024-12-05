<?php
require 'db.php';

// Set the response header to JSON
header('Content-Type: application/json');

try {
    // Ambil `user_id` dari parameter GET atau POST
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id || !is_numeric($user_id)) {
        echo json_encode([
            'response' => 'Invalid or missing user ID.',
            'chat_logs' => []
        ]);
        exit;
    }

    // Ambil log percakapan dari database berdasarkan user_id
    $stmt = $pdo->prepare("SELECT * FROM chat_logs WHERE user_id = ? ORDER BY id ASC");
    $stmt->execute([$user_id]);
    $chat_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kirimkan log percakapan ke client
    echo json_encode([
        'response' => 'Chat logs retrieved successfully.',
        'chat_logs' => $chat_logs
    ]);
} catch (Exception $e) {
    // Penanganan error
    echo json_encode([
        'response' => 'An unexpected error occurred.',
        'error' => $e->getMessage(),
        'chat_logs' => []
    ]);
}
