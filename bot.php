<?php
require 'db.php';

// Atur header respons ke JSON
header('Content-Type: application/json');

try {
    // Pastikan request method adalah POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'response' => 'Invalid request method. Only POST is allowed.',
            'submenus' => []
        ]);
        exit;
    }

    // Ambil body request JSON dan decode ke array PHP
    $input = file_get_contents('php://input'); // Membaca body request
    $data = json_decode($input, true); // Decode JSON menjadi array

    // Validasi menu_id dan user_message
    $menu_id = $data['menu_id'] ?? null; // Ambil menu_id dari JSON
    $user_id = $data['user_id'] ?? null; // Ambil menu_id dari JSON
    $user_message = $data['user_message'] ?? ''; // Ambil pesan dari user

    // Jika user_message kosong, tampilkan daftar menu
    if (empty($user_message)) {
        // Ambil semua menu dari database
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE parent_id IS NULL"); // Ambil menu utama (parent_id IS NULL)
        $stmt->execute();
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tampilkan daftar menu ke pengguna
        $response = "Daftar Menu:\n";
        foreach ($menus as $menu) {
            $response .= "ID: " . $menu['id'] . " - " . $menu['name'] . "\n"; // Menampilkan nama menu dan ID
        }

        echo json_encode([
            'response' => $response,
            'submenus' => []
        ]);
        exit;
    }

    // Validasi menu_id (untuk pesan yang mengandung menu_id)
    if (!$menu_id || !is_numeric($menu_id)) {
        echo json_encode([
            'response' => 'Invalid or missing menu ID.',
            'submenus' => []
        ]);
        exit;
    }

    // Ambil data menu berdasarkan ID
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    // if ($menu) {
        // Ambil submenu jika ada
        $stmt = $pdo->prepare("SELECT * FROM menu ");
        $stmt->execute();
        $submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Menyimpan pesan pengguna ke chat_logs
        if ($user_message) {
            // Gantilah ini dengan ID pengguna yang relevan dari sesi pengguna
            $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'user', 'progress')");
            $stmt->execute([$user_id, $user_message]);
        }

        // Respon dari bot
        $bot_response = $menu['response'] ?: 'Informasi Apa yang kamu butuhkan?';
        // Menyimpan respon bot ke chat_logs
        $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'bot', 'progress')");
        $stmt->execute([$user_id, $bot_response]);

        echo json_encode([
            'response' => $bot_response,
            'submenus' => $submenus
        ]);
    // } else {
    //     echo json_encode([
    //         'response' => 'Menu not found.',
    //         'submenus' => []
    //     ]);
    // }
} catch (Exception $e) {
    // Jika terjadi error di server
    echo json_encode([
        'response' => 'An unexpected error occurred: ' . $e->getMessage(),
        'submenus' => []
    ]);
}
