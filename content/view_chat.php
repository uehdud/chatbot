<?php
require __DIR__ . '/../db.php';


$title = "Manajemen Menu Chatbot";

// Ambil pesan notifikasi jika ada
$alert = isset($_GET['alert']) ? $_GET['alert'] : null;

try {

    // Ambil user_id dari URL
    $user_id = isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : null;

    if (!$user_id) {
        die('User ID tidak valid.');
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>

<div class="content p-3">
    <div id="chat-box" style="width: 100%; max-width: 500px; ">
        <div class="card mt-4">
            <div class=" card-header bg-primary text-white">
                Chat
            </div>
            <div class="card-body">
                <div id="chat-container">
                    <div id="chat-output" class="card-body overflow-auto" style="height: 300px; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">
                        <!-- Pesan akan muncul di sini -->
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="text" id="user-input" class="form-control" placeholder="Ketik pesan Anda...">
                <button class="btn btn-primary mt-2" id="send-btn" disabled>Kirim</button>
                <button id="logout-btn" class="btn btn-success mt-2" style="display: none;">Buat Chat Baru</button>

            </div>
        </div>
    </div>
</div>

<script>
    const userId = <?= json_encode($user_id); ?>;
    const userForm = document.getElementById('user-form');
    const chatBox = document.getElementById('chat-box');
    const chatOutput = document.getElementById('chat-output');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const chatContainer = document.getElementById('chat-container');
    const userDetailsForm = document.getElementById('user-details-form');
    document.addEventListener('DOMContentLoaded', function() {
        loadChatLogs();
    });

    async function loadChatLogs() {
        try {
            const response = await fetch(`get_chat_logs.php?user_id=${userId}`);
            const data = await response.json();
            console.log(data);

            if (data.chat_logs) {
                chatOutput.innerHTML = '';

                data.chat_logs.forEach(log => {
                    const bubble = document.createElement('div');
                    bubble.classList.add('chat-bubble', log.jenis);
                    bubble.textContent = log.isi_respon;
                    chatOutput.appendChild(bubble);
                });

                chatOutput.scrollTop = chatOutput.scrollHeight;
            } else {
                addMessage('Riwayat chat kosong.', 'bot');
            }
        } catch (error) {
            console.error('Error loading chat logs:', error);
            addMessage('Terjadi kesalahan saat memuat riwayat chat.', 'bot');
        }
    }
</script>

<?php include './layouts/footer.php'; ?>