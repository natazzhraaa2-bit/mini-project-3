<?php
session_start(); // Sesi dinyalakan sekali di paling atas agar tidak error
require_once __DIR__ . '/../config/db.php';

// Generate CSRF token jika belum ada
$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

$q = trim($_GET["q"] ?? "");
if ($q !== "") {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q OR category LIKE :q ORDER BY id DESC");
    $stmt->execute(["q" => "%$q%"]);
} else {
    $stmt = $pdo->query("SELECT id, name, category, price, stock, image FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Store Case HP</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1>Katalog Case HP</h1>

    <?php if (($_GET["status"] ?? "") === "created"): ?>
        <div class="alert">Produk berhasil disimpan.</div>
    <?php elseif (($_GET["status"] ?? "") === "updated"): ?>
        <div class="alert">Produk berhasil diperbarui.</div>
    <?php elseif (($_GET["status"] ?? "") === "deleted"): ?>
        <div class="alert">Produk berhasil dihapus.</div>
    <?php endif; ?>

    <p><a href="create.php"><button>+ Tambah Case Baru</button></a></p>

    <form method="GET" style="padding: 10px; display: flex; gap: 8px;">
        <input name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari nama case atau tipe HP..." style="margin-bottom: 0;">
        <button type="submit">Cari</button>
    </form>

    <div class="products">
        <?php if (empty($products)): ?>
            <p>Belum ada produk case HP.</p>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <div class="card">
                    <!-- Tampilkan Gambar -->
                    <?php if (!empty($p["image"]) && file_exists(__DIR__ . '/uploads/' . $p["image"])): ?>
                        <img src="uploads/<?= htmlspecialchars($p["image"], ENT_QUOTES, 'UTF-8') ?>" alt="Foto Case">
                    <?php else: ?>
                        <div style="width: 100%; height: 180px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 12px; color: #64748b; font-size: 14px;">Tanpa Gambar</div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($p["name"], ENT_QUOTES, "UTF-8") ?></h3>
                    <p>Tipe: <?= htmlspecialchars($p["category"], ENT_QUOTES, "UTF-8") ?></p>
                    <p>Harga: Rp <?= number_format($p["price"], 0, ",", ".") ?></p>
                    <p>Stok: <?= (int)$p["stock"] ?></p>
                    
                    <div class="actions">
                        <a href="edit.php?id=<?= $p["id"] ?>"><button style="background: #eab308;">Edit</button></a>
                        
                        <form action="delete.php" method="POST" style="margin:0; padding:0; border:none; background:none;">
                            <input type="hidden" name="id" value="<?= $p["id"] ?>">
                            <input type="hidden" name="csrf" value="<?= $_SESSION["csrf"] ?>">
                            <button type="submit" style="background: #ef4444;" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>