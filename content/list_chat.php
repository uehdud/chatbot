<?php
require __DIR__ . '/../db.php';


$title = "Manajemen Menu Chatbot";

// Ambil pesan notifikasi jika ada
$alert = isset($_GET['alert']) ? $_GET['alert'] : null;

try {
    try {
        $stmt = $pdo->prepare("
                    SELECT grouped_data.user_id, 
                        grouped_data.full_name, 
                        grouped_data.phone_number, 
                        grouped_data.email, 
                        grouped_data.start_time
                    FROM (
                        SELECT cl.user_id, 
                            ud.full_name, 
                            ud.phone_number, 
                            ud.email, 
                            MAX(CASE WHEN cl.sesi = 'start' THEN cl.created_at END) AS start_time,
                            MAX(cl.id) AS max_id
                        FROM chat_logs cl
                        JOIN user_details ud ON cl.user_id = ud.id
                        GROUP BY cl.user_id, ud.full_name, ud.phone_number, ud.email
                    ) AS grouped_data
                    ORDER BY grouped_data.max_id DESC
                ");
    

        $stmt->execute();
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $no = 1;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>

<div class="content p-3">
    <div class="container mt-5">
        <h1 class="text-center">List Chat User</h1>

        <?php if ($alert): ?>
            <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show" role="alert">
                <?= $alert['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>



        <table id="chat-table" class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID User</th>
                    <th>Nama User</th>
                    <th>No Hp</th>
                    <th>Email</th>
                    <th>Keterangan</th>
                    <th>Waktu Mulai Chat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                    <tr>
                        <td><?= htmlspecialchars($menu['user_id']) ?></td>
                        <td><?= htmlspecialchars($menu['full_name']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($menu['phone_number']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($menu['email']) ?: '-' ?></td>
                        <td>Chat dimulai</td>
                        <td><?= htmlspecialchars($menu['start_time']) ?: 'Belum memulai' ?></td>
                        <td>
                            <a href="get_chat_detail.php?user_id=<?= urlencode($menu['user_id']) ?>"
                                class="btn btn-primary btn-sm lihat-chat"
                                data-user-id="<?= htmlspecialchars($menu['user_id']) ?>">Lihat Chat</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<?php include './layouts/footer.php'; ?>