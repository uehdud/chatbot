<?php
require __DIR__ . '/../db.php';


$title = "Manajemen Menu Chatbot";

// Ambil pesan notifikasi jika ada
$alert = isset($_GET['alert']) ? $_GET['alert'] : null;

try {
    $stmt = $pdo->prepare("SELECT * FROM menu ORDER BY parent_id, id");
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
        <h1 class="text-center">Manajemen Info</h1>

        <?php if ($alert): ?>
            <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show" role="alert">
                <?= $alert['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form action="save.php" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="menu_name" class="form-control" placeholder="Nama Info" required>
                </div>
                <!-- <div class="col-md-3">
                    <select name="parent_id" class="form-select">
                        <option value="">Menu Induk (Opsional)</option>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?= $menu['id'] ?>"><?= $menu['menu_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div> -->
                <div class="col-md-6">
                    <textarea name="response" class="form-control" placeholder="Keterangan"></textarea>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Tambah Menu</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Info</th>
                    <!-- <th>Induk</th> -->
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $menu['menu_name'] ?></td>
                        <!-- <td><?= $menu['parent_id'] ?: 'Tidak Ada' ?></td> -->
                        <td><?= $menu['response'] ?: 'Tidak Ada Respons' ?></td>
                        <td>
                            <a href="update.php?id=<?= $menu['id'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                            <a href="delete.php?id=<?= $menu['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include './layouts/footer.php'; ?>