<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot with Rasa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styling untuk chat container */
        #chat-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: #f9f9f9;
        }

        #chat-window {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
        }

        /* Styling bubble chat */
        .chat-bubble {
            display: inline-block;
            max-width: 70%;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 15px;
            line-height: 1.5;
            font-size: 14px;
            word-wrap: break-word;
        }

        .user-message {
            background-color: #007bff;
            color: #fff;
            margin-left: auto;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: right;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }

        .bot-message {
            background-color: #f1f1f1;
            color: #333;
            margin-right: auto;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: left;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }

        /* Tambahkan agar teks panjang tidak melebihi batas */
        .chat-bubble p {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Styling tambahan untuk spasi */
        #messages {
            display: flex;
            flex-direction: column;
        }
    </style>

</head>

<body>
    <div class="container mt-5">
        <!-- Form Pengguna -->
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

        <!-- Chatbot -->
        <div id="chat-container" style="display: none;">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Chat with Rasa</h4>
                </div>
                <div class="card-body" id="chat-window" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                    <div id="messages"></div>
                </div>
                <div class="card-footer">
                    <div class="input-group">
                        <input type="text" id="user-input" class="form-control" placeholder="Ketik pesan anda disini">
                        <button id="send-btn" class="btn btn-primary">Kirim</button>
                        <button id="chat-ulang-btn" class="btn btn-success" style="display: none;" onclick="startNewChat()">Chat Ulang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        const apiUrl = "start_chat.php";
        const rasaUrl = "http://localhost:5005/webhooks/rest/webhook";
        const storedUserId = localStorage.getItem('user_id');
        const storedFullName = localStorage.getItem('full_name');
        const saveChatLog = (userId, message, jenis, sesi) => {
            $.ajax({
                url: "log_chat.php",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    user_id: userId,
                    message: message,
                    jenis: jenis,
                    sesi: sesi
                }),
                success: function(response) {
                    console.log("Chat log saved:", response);
                },
                error: function() {
                    console.error("Error saving chat log");
                }
            });
        };

        function startNewChat() {
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', function() {


            if (storedUserId && storedFullName) {
                $("#user-form").hide();
                $("#chat-container").show();
                loadChatLogs(storedUserId);
            } else {
                $("#user-form").show();
                $("#chat-container").hide();
            }

            // Tambahan untuk idle detection
            let idleTime = 0;
            let idlePromptTimeout;
            let sessionEndTimeout;

            // Fungsi untuk mereset timer idle
            function resetIdleTimer() {
                clearTimeout(idlePromptTimeout);
                clearTimeout(sessionEndTimeout);
                idleTime = 0;

                // Set ulang waktu prompt idle
                idlePromptTimeout = setTimeout(() => {
                    showIdlePrompt();
                }, 15000); // 15 detik untuk prompt
            }

            // Fungsi untuk menampilkan prompt idle dalam chat bubble
            function showIdlePrompt() {
                appendMessage("Bot", "Apakah Anda masih di sana?"); // Tampilkan pesan di chat bubble
                saveChatLog(storedUserId, "Apakah Anda masih di sana?", "bot", "idle_prompt"); // Simpan log chat

                // Tunggu respon dari user
                sessionEndTimeout = setTimeout(() => {
                    appendMessage("Bot", "Sesi telah berakhir karena tidak ada aktivitas.");
                    saveChatLog(storedUserId, "Sesi telah berakhir karena tidak ada aktivitas.", "bot", "session_end");
                    endSession();
                }, 15000); // 10 detik tambahan sebelum sesi berakhir
            }

            function endSession() {
                // Hapus data sesi dari localStorage
                localStorage.removeItem('user_id');
                localStorage.removeItem('full_name');

                // Nonaktifkan tombol kirim
                const sendButton = document.getElementById('send-btn');
                if (sendButton) {
                    sendButton.disabled = true;
                    sendButton.style.display = 'none'; // Sembunyikan tombol kirim
                }

                // Tampilkan tombol "Chat Ulang"
                const chatUlangButton = document.getElementById('chat-ulang-btn');
                if (chatUlangButton) {
                    chatUlangButton.style.display = 'inline-block'; // Tampilkan tombol
                }
            }



            // Event listener untuk aktivitas pengguna
            document.addEventListener('mousemove', resetIdleTimer);
            document.addEventListener('keypress', resetIdleTimer);

            // Mulai timer idle saat halaman dimuat
            resetIdleTimer();
        });

        async function loadChatLogs(userId) {
            console.log('Memuat log chat untuk userId:', userId);
            try {
                if (!userId) {
                    console.error("User ID tidak ditemukan.");
                    return;
                }
                const response = await fetch(`get_chat_logs.php?user_id=${userId}`);
                const data = await response.json();

                if (data.chat_logs && data.chat_logs.length > 0) {
                    $("#messages").html("");
                    data.chat_logs.forEach(log => {
                        const sender = log.jenis === "user" ? "You" : "Bot";
                        appendMessage(sender, log.isi_respon);
                    });
                    $("#chat-window").scrollTop($("#chat-window")[0].scrollHeight);
                } else {
                    appendMessage("Bot", "Tidak ada riwayat percakapan.");
                }
            } catch (error) {
                console.error("Error loading chat logs:", error);
                appendMessage("Bot", "Terjadi kesalahan saat memuat riwayat chat.");
            }
        }

        function appendMessage(sender, message) {
            const isUser = sender === "You";
            const messageHtml = `
            <div class="chat-bubble ${isUser ? 'user-message' : 'bot-message'}">
                <p>${message}</p>
            </div>
        `;
            $("#messages").append(messageHtml);
            $("#chat-window").scrollTop($("#chat-window")[0].scrollHeight);
        }

        $("#user-details-form").submit(function(e) {
            e.preventDefault();

            const userData = {
                full_name: $("#full-name").val(),
                email: $("#email").val(),
                phone_number: $("#phone").val(),
                first_message: $("#first-message").val()
            };

            $.ajax({
                url: apiUrl,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(userData),
                success: function(response) {
                    if (response.success) {
                        localStorage.setItem('user_id', response.user_id);
                        localStorage.setItem('full_name', userData.full_name);

                        saveChatLog(response.user_id, userData.first_message, "user", "start");
                        saveChatLog(response.user_id, response.bot_response, "bot", "start");

                        $("#user-form").hide();
                        $("#chat-container").show();

                        appendMessage("Bot", response.bot_response);
                    } else {
                        alert(response.error || "Failed to submit details.");
                    }
                },
                error: function() {
                    alert("Error: Unable to connect to the server.");
                }
            });
        });

        // Chat logic
        $("#send-btn").click(function() {
            const userMessage = $("#user-input").val();
            if (userMessage.trim() !== "") {
                appendMessage("You", userMessage);
                saveChatLog(storedUserId, userMessage, "user", "progress");
                $.ajax({
                    url: rasaUrl,
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        sender: "user",
                        message: userMessage
                    }),
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach((resp) => {
                                appendMessage("Bot", resp.text);
                                saveChatLog(storedUserId, resp.text, "bot", "progress");
                            });

                        } else {
                            appendMessage("Bot", "Sorry, I didn't understand that.");
                            saveChatLog(storedUserId, botMessage, "bot", "progress");
                        }
                    },
                    error: function() {
                        appendMessage("Bot", "Error: Unable to connect to Rasa server.");
                        saveChatLog(storedUserId, errorMessage, "bot", "progress");
                    }
                });

                $("#user-input").val("");
            }
        });

        $("#user-input").keypress(function(e) {
            if (e.which === 13) {
                $("#send-btn").click();
            }
        });
    </script>

</body>

</html>