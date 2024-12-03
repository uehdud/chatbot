<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $menu_name = $_POST['menu_name'];
        $parent_id = $_POST['parent_id'] ?: null;
        $response = $_POST['response'] ?: null;

        $stmt = $pdo->prepare("UPDATE menu SET menu_name = ?, parent_id = ?, response = ? WHERE id = ?");
        $stmt->execute([$menu_name, $parent_id, $response, $id]);

        header("Location: index.php?alert[type]=success&alert[message]=Info berhasil di update!");
    } catch (PDOException $e) {
        header("Location: index.php?alert[type]=danger&alert[message]=Gagal Update: " . $e->getMessage());
    }
} else {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$menu) {
        header("Location: index.php?alert[type]=danger&alert[message]=Info not found!");
        exit;
    }
}
?>

<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>

<div class="content p-3">

    <div class="container mt-5">
        <h1>Update Info</h1>
        <form action="update.php" method="POST">
            <input type="hidden" name="id" value="<?= $menu['id'] ?>">
            <div class="mb-3">
                <label for="menu_name" class="form-label">Nama Info</label>
                <input type="text" id="menu_name" name="menu_name" class="form-control" value="<?= $menu['menu_name'] ?>" required>
            </div>
            <!-- <div class="mb-3">
                <label for="parent_id" class="form-label">Parent Menu</label>
                <select id="parent_id" name="parent_id" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($menus as $parent): ?>
                        <option value="<?= $parent['id'] ?>" <?= $menu['parent_id'] == $parent['id'] ? 'selected' : '' ?>><?= $parent['menu_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div> -->
            <div class="mb-3">
                <label for="response" class="form-label">Keterangan</label>
                <textarea id="response" name="response" class="form-control"><?= $menu['response'] ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">batal</a>
        </form>
    </div>
</div>

<?php include './layouts/footer.php'; ?>