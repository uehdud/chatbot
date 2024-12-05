<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $full_name = $data['full_name'] ?? null;
    $email = $data['email'] ?? null;
    $phone_number = $data['phone_number'] ?? null;
    $first_message = $data['first_message'] ?? null;

    if (!$full_name || !$email || !$first_message) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields except phone number are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO user_details (full_name, email, phone_number, first_message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $phone_number, $first_message]);
    $user_id = $pdo->lastInsertId();

    $_SESSION['user_id'] = $user_id;
    $_SESSION['full_name'] = $full_name;

    http_response_code(201);
    echo json_encode(['success' => true, 'bot_response' => "Halo, $full_name! Apa yang bisa saya bantu?"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An unexpected error occurred.']);
}
