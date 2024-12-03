<?php require 'db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$full_name = $_SESSION['full_name'] ?? null;
// Jika session user ada, redirect langsung ke chat
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userExists = true;
} else {
    $userExists = false;
}
$stmt = $pdo->prepare("SELECT * FROM menu ");
$stmt->execute();
$submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Chatbot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        /* Custom styles for the chat interface */
        body {
            font-family: 'Arial', sans-serif;
        }

        .chat-box {
            height: 400px;
            /* Set the fixed height for the chat container */
            overflow-y: auto;
            /* Enable vertical scrolling */
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .chat-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .chat-bubble.user {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .chat-bubble.bot {
            background-color: #f1f1f1;
            align-self: flex-start;
            margin-right: auto;
        }

        .submenu-card {
            margin-top: 10px;
        }

        .submenu-card button {
            margin: 5px;
        }

        /* Ensure the chat output container is scrollable */
        #chat-output {
            max-height: 400px;
            /* Set max-height to the chat box */
            overflow-y: auto;
            /* Enable scrolling */
            flex-grow: 1;
            /* Make it flexible inside the container */
        }

        #chat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Fill the height of the parent container */
        }

        .input-group {
            margin-top: 15px;
        }

        .btn-outline-primary {
            margin: 5px;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        /* Card styling for user details form */
        .card {
            padding: 20px;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .interactive-button-ya {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button-end {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #ff0000;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #6c757d;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="content-wrapper ">
            <h1 class="text-center mb-4">Chatbot Menu</h1>

            <!-- Form Detail Pengguna -->
            <div id="user-form">
                <div class="card">
                    <h4 class="card-title p-3">Silakan masukkan detail Anda</h4>
                    <form id="user-details-form" class="p-3">
                        <div class="mb-3">
                            <label for="full-name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="full-name" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Masukkan email Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telepon (Opsional)</label>
                            <input type="text" class="form-control" id="phone" placeholder="Masukkan no. telepon">
                        </div>
                        <div class="mb-3">
                            <label for="first-message" class="form-label">Pesan Pertama</label>
                            <input type="text" class="form-control" id="first-message" placeholder="Apa yang ingin Anda sampaikan?" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Mulai Chat</button>
                    </form>
                </div>
            </div>

            <!-- Chat Box -->
            <div id="chat-box" style="width: 100%; max-width: 500px; display: none;">
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
                        <button class="btn btn-primary mt-2" id="send-btn">Kirim</button>
                        <button id="logout-btn" class="btn btn-success mt-2" style="display: none;">Buat Chat Baru</button>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        const userForm = document.getElementById('user-form');
        const chatBox = document.getElementById('chat-box');
        const chatOutput = document.getElementById('chat-output');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const chatContainer = document.getElementById('chat-container');
        const userDetailsForm = document.getElementById('user-details-form');
        let menuId = 1;
        let userId = null;
        let idleTimer; // Variabel untuk menyimpan timer
        let isIdleTimerActive = false; // Status apakah idle timer aktif atau tidak
        const submenus = <?php echo json_encode($submenus); ?>;


        document.addEventListener('DOMContentLoaded', function() {


            // Data submenu dari PHP


            // Periksa apakah sesi user_id dan nama sudah ada
            const storedUserId = localStorage.getItem('user_id');
            const storedFullName = localStorage.getItem('full_name');

            if (storedUserId && storedFullName) {
                // Langsung tampilkan chat box jika sesi tersedia
                userId = storedUserId;
                userForm.style.display = 'none';
                chatContainer.style.display = 'block';
                addMessage(`Halo, ${storedFullName}! Informasi apa yang Anda butuhkan?`, 'bot');

                // Tampilkan chat box
                document.getElementById('chat-box').style.display = 'block';

                // Render submenu yang dikirim dari PHP


                // Muat riwayat chat
                loadChatLogs();
            }





        });

        async function loadChatLogs() {
            try {
                const response = await fetch(`get_chat_logs.php?user_id=${userId}`);
                const data = await response.json();

                if (data.chat_logs) {
                    // Hapus isi chat sebelumnya
                    chatOutput.innerHTML = '';

                    // Tampilkan semua log chat
                    data.chat_logs.forEach(log => {
                        const bubble = document.createElement('div');
                        bubble.classList.add('chat-bubble', log.jenis);
                        bubble.textContent = log.isi_respon;

                        // Tambahkan bubble ke dalam kontainer chat
                        chatOutput.appendChild(bubble);
                    });




                    const lastMessage = data.chat_logs[data.chat_logs.length - 1].isi_respon;

                    if (lastMessage === 'Apakah Informasi ini membantu ?') {
                        addInteractiveButtons(); // Tambahkan tombol interaktif
                    } else if (lastMessage === 'Anda menjawab: ya') {
                        addEndChatButtons(); // Tambahkan tombol "Akhiri Chat" dan "Kembali ke Menu"
                    } else if (lastMessage === 'Chat telah diakhiri. Terima kasih telah menggunakan layanan kami!') {
                        // Sembunyikan tombol kirim
                        document.getElementById('send-btn').style.display = 'none';

                        // Tampilkan tombol logout
                        const logoutButton = document.getElementById('logout-btn');
                        if (logoutButton) {
                            logoutButton.style.display = 'block'; // Tampilkan tombol "Buat Chat Baru"
                        }
                    } else {
                        renderSubmenus(submenus); // Tampilkan submenus jika tidak ada kondisi khusus
                    }



                    // Scroll ke bagian bawah
                    chatOutput.scrollTop = chatOutput.scrollHeight;
                } else {
                    console.error('Failed to load chat logs:', data.message || 'Unknown error');
                    addMessage('Gagal memuat riwayat chat. Silakan coba lagi.', 'bot');
                }
            } catch (error) {
                console.error('Error loading chat logs:', error);
                addMessage('Terjadi kesalahan saat memuat riwayat chat.', 'bot');
            }
        }

        function addEndChatButtons() {
            const buttonContainer = document.createElement('div');
            buttonContainer.classList.add('button-container');

            // Tombol "Akhiri Chat"
            const endChatButton = document.createElement('button');
            endChatButton.textContent = 'Akhiri Chat';
            endChatButton.classList.add('interactive-button-end');
            endChatButton.addEventListener('click', () => handleEndChat());

            // Tombol "Kembali ke Menu"
            const backToMenuButton = document.createElement('button');
            backToMenuButton.textContent = 'Kembali ke Menu';
            backToMenuButton.classList.add('interactive-button');
            backToMenuButton.addEventListener('click', () => {
                deactivateIdleTimer(); // Nonaktifkan idle timer
                renderSubmenus(submenus); // Tampilkan submenu kembali
            });

            // Tambahkan tombol ke container
            buttonContainer.appendChild(endChatButton);
            buttonContainer.appendChild(backToMenuButton);

            // Tambahkan container ke chat output
            chatOutput.appendChild(buttonContainer);

            // Scroll ke bagian bawah untuk melihat tombol
            chatOutput.scrollTop = chatOutput.scrollHeight;
        }

        // Fungsi untuk menangani "Akhiri Chat"
        async function handleEndChat() {
            try {
                const response = await fetch('end_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: localStorage.getItem('user_id'), // Ambil user_id dari localStorage
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    addMessage('Chat telah diakhiri. Terima kasih telah menggunakan layanan kami!', 'bot');
                    loadChatLogs();
                } else {
                    console.error('Gagal mengakhiri chat:', result.message);
                }
            } catch (error) {
                console.error('Error saat mengakhiri chat:', error);
            }
        }

        // Fungsi untuk memulai idle timer
        function startIdleTimer() {
            if (!isIdleTimerActive) return; // Jangan jalankan jika timer tidak aktif

            clearTimeout(idleTimer); // Reset timer setiap ada aktivitas
            idleTimer = setTimeout(async () => {
                try {
                    const response = await fetch('insert_idle_response.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: localStorage.getItem('user_id'), // Ambil user_id dari localStorage
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        console.log('Pesan idle berhasil disimpan.');
                        loadChatLogs(); // Memuat ulang riwayat chat
                    } else {
                        console.error('Gagal menyimpan pesan idle:', result.message);
                    }
                } catch (error) {
                    console.error('Error saat menyimpan pesan idle:', error);
                }
            }, 5000); // 5 detik idle
        }

        // Fungsi untuk mengaktifkan idle timer (setelah klik menu)
        function activateIdleTimer() {
            isIdleTimerActive = true;
            startIdleTimer(); // Mulai timer
        }

        // Fungsi untuk menonaktifkan idle timer (contoh: setelah tombol "Tidak" diklik)
        function deactivateIdleTimer() {
            isIdleTimerActive = false;
            clearTimeout(idleTimer); // Hentikan timer
            console.log('Idle timer dinonaktifkan.');
        }

        // Deteksi aktivitas pengguna (mouse atau keyboard)
        document.addEventListener('mousemove', startIdleTimer);
        document.addEventListener('keydown', startIdleTimer);

        // Modifikasi tombol "Tidak"
        function addInteractiveButtons() {
            const buttonContainer = document.createElement('div');
            buttonContainer.classList.add('button-container');

            // Tombol "Ya"
            const yesButton = document.createElement('button');
            yesButton.textContent = 'Ya';
            yesButton.classList.add('interactive-button-ya');
            yesButton.addEventListener('click', () => handleFeedback('ya'));

            // Tombol "Tidak"
            const noButton = document.createElement('button');
            noButton.textContent = 'Tampilkan Menu';
            noButton.classList.add('interactive-button');
            noButton.addEventListener('click', () => {
                handleFeedback('menu kembali');
                deactivateIdleTimer(); // Nonaktifkan idle timer setelah "Tidak" diklik
            });

            // Tambahkan tombol ke container
            buttonContainer.appendChild(yesButton);
            buttonContainer.appendChild(noButton);

            // Tambahkan container ke chat output
            chatOutput.appendChild(buttonContainer);

            // Scroll ke bagian bawah untuk melihat tombol
            chatOutput.scrollTop = chatOutput.scrollHeight;
        }

        // Fungsi untuk menangani respons tombol interaktif
        async function handleFeedback(response) {
            try {
                const feedbackResponse = await fetch('handle_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: localStorage.getItem('user_id'),
                        feedback: response,
                    }),
                });

                const result = await feedbackResponse.json();

                if (result.success) {
                    addMessage('Terima kasih atas tanggapannya!', 'bot');
                    loadChatLogs(); // Muat ulang chat logs setelah tanggapan

                    if (response === 'menu kembali') {
                        renderSubmenus(submenus); // Render ulang menu jika respons adalah "Tidak"
                    }
                } else {
                    console.error('Gagal mengirim tanggapan:', result.message);
                }
            } catch (error) {
                console.error('Error saat mengirim tanggapan:', error);
            }
        }





        userDetailsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const fullName = document.getElementById('full-name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const firstMessage = document.getElementById('first-message').value.trim();

            // Kirim data pengguna ke server
            fetch('start_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        full_name: fullName,
                        email: email,
                        phone: phone,
                        first_message: firstMessage,
                    }),
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        userId = data.user_id; // Simpan user_id untuk log chat

                        // Simpan ke sesi browser
                        localStorage.setItem('user_id', data.user_id);
                        localStorage.setItem('full_name', fullName);

                        // Ubah tampilan
                        userForm.style.display = 'none';
                        chatContainer.style.display = 'block';
                        loadChatLogs();

                        // Tampilkan chat box
                        document.getElementById('chat-box').style.display = 'block';
                    } else {
                        alert('Error memulai percakapan.');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });


        // Handle sending user messages
        sendBtn.addEventListener('click', function() {
            const userMessage = userInput.value.trim();
            if (!userMessage) return;

            // Add user message to chat
            addMessage(userMessage, 'user');
            userInput.value = '';

            // Send message to server
            fetch('bot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        menu_id: menuId,
                        user_message: userMessage
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.response) {
                        addMessage(data.response, 'bot');
                    }

                    if (data.submenus && data.submenus.length > 0) {
                        renderSubmenus(data.submenus);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    addMessage('Oops, something went wrong. Please try again later.', 'bot');
                });
        });


        // Menambahkan pesan dari pengguna dan bot ke dalam chat
        function addMessage(message, sender) {
            const bubble = document.createElement('div');
            bubble.className = `chat-bubble ${sender}`;
            bubble.textContent = message;
            chatOutput.appendChild(bubble);
            chatOutput.scrollTop = chatOutput.scrollHeight;
        }



        // Render submenu untuk dipilih oleh pengguna
        function renderSubmenus(submenus) {
            const submenuContainer = document.createElement('div');
            submenuContainer.className = 'submenu-card card p-3';

            const title = document.createElement('h5');
            title.className = 'card-title mb-3';
            title.textContent = 'List Informasi:';
            submenuContainer.appendChild(title);

            submenus.forEach(menu => {
                const submenuButton = document.createElement('button');
                submenuButton.textContent = menu.menu_name;
                submenuButton.className = 'btn btn-outline-primary m-1';
                submenuButton.onclick = () => selectMenu(menu.id); // Ketika tombol submenu diklik
                submenuContainer.appendChild(submenuButton);
            });

            chatOutput.appendChild(submenuContainer);
            chatOutput.scrollTop = chatOutput.scrollHeight;
        }

        // Memilih menu dan meminta respons dari server
        function selectMenu(selectedMenuId) {
            menuId = selectedMenuId;
            console.log('menuId');
            console.log(menuId);

            activateIdleTimer();
            // addMessage('Loading submenu...', 'bot');

            fetch('bot_respon.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        menu_id: selectedMenuId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.response) {
                        addMessage(data.response, 'bot');
                    }
                    console.log(data);
                    return
                    if (data.submenus && data.submenus.length > 0) {
                        renderSubmenus(data.submenus); // Render submenu baru

                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    addMessage('Oops, something went wrong. Please try again later.', 'bot');
                });
        }

        // Menangani pesan yang dikirimkan oleh pengguna
        sendBtn.addEventListener('click', function() {
            const userMessage = userInput.value.trim();
            if (!userMessage) return;

            addMessage(userMessage, 'user');
            userInput.value = '';

            fetch('bot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        menu_id: menuId,
                        user_message: userMessage
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.response) {
                        addMessage(data.response, 'bot');
                    }

                    if (data.submenus && data.submenus.length > 0) {
                        renderSubmenus(data.submenus);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    addMessage('Oops, something went wrong. Please try again later.', 'bot');
                });
        });
        document.getElementById('logout-btn').addEventListener('click', function() {
            // Hapus sesi dari localStorage
            localStorage.removeItem('user_id');
            localStorage.removeItem('full_name');

            // Reload halaman
            location.reload();
        });
    </script>
</body>

</html>