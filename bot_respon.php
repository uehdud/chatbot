<?php
require 'db.php';

// Set the response header to JSON
header('Content-Type: application/json');

try {
    // Get the POST request data
    $input = file_get_contents('php://input'); // Read the request body
    $data = json_decode($input, true); // Decode the JSON into an array

    // Extract user_id and menu_id from the request
    $user_id = $data['user_id'] ?? null;
    $menu_id = $data['menu_id'] ?? null;
   
    // Check if menu_id is provided and valid
    if (!$menu_id || !is_numeric($menu_id)) {
        echo json_encode([
            'response' => 'Invalid or missing menu ID.',
            'submenus' => []
        ]);
        exit;
    }

    // Get the menu data from the database
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id=?");
    $stmt->execute([$menu_id]); 
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if ($menu) {
        // Get submenus for the selected menu (if any)
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->execute([$menu_id]);
        $submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Save the user message to the chat_logs table
        $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'user', 'progress')");
        $stmt->execute([$user_id, 'Anda memilih menu ' . $submenus[0]['menu_name']]);

        // Prepare the bot's response
        $bot_response = $menu['response'] ?: 'No response available for this menu.';

        // Save the bot's response to the chat_logs table
        $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, isi_respon, jenis, sesi) VALUES (?, ?, 'bot', 'progress')");
        $stmt->execute([$user_id, $bot_response]);

        // Return the bot's response and submenus (if available)
        echo json_encode([
            'response' => $bot_response,
            'submenus' => $submenus
        ]);
    } else {
        echo json_encode([
            'response' => 'Menu not found.',
            'submenus' => []
        ]);
    }
} catch (Exception $e) {
    // If there is an error, return a generic error message
    echo json_encode([
        'response' => 'An unexpected error occurred: ' . $e->getMessage(),
        'submenus' => []
    ]);
}
