<?php
// Tentukan file layout dan konten default
$content = 'content/view_chat.php';
$title = 'Dashboard - Manajemen Menu Chatbot';

// Panggil file layout
include 'layouts/header.php';
include 'layouts/sidebar.php';

// Muat konten utama
if (file_exists($content)) {
    include $content;
} else {
    echo "<div class='main-content' style='margin-left: 300px; padding: 20px;'>
            <h1>Halaman tidak ditemukan</h1>
          </div>";
}

include 'layouts/footer.php';
