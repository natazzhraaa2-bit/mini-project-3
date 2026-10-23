<?php
require_once __DIR__ . '/../config/db.php';

$errors = [];
$name = "";
$category = "";
$price = "";
$stock = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "Umum");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3)  $errors["name"] = "Nama minimal 3 karakter.";
    if ($price === false || $price <= 0)  $errors["price"] = "Harga harus > 0.";
    if ($stock === false || $stock < 0)  $errors["stock"] = "Stok tidak boleh negatif.";

    // Tangani Upload Gambar
    $imageName = null;
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES["image"]["tmp_name"];
        $fileName = $_FILES["image"]["name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Validasi ekstensi gambar yang boleh diupload
        $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Buat nama file unik agar tidak bentrok
            $imageName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
            $uploadFileDir = __DIR__ . '/uploads/';
            
            if(!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $imageName;
            move_uploaded_file($fileTmpPath, $dest_path);
        } else {
            $errors["image"] = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image) VALUES (:name, :category, :price, :stock, :image)");
            $stmt->execute([
                "name" => $name, 
                "category" => $category, 
                "price" => $price, 
                "stock" => $stock,
                "image" => $imageName
            ]);
            
            header("Location: index.php?status=created");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors["name"] = "Nama produk sudah terdaftar.";
            } else {
                $errors["db"] = "Terjadi kesalahan pada database.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Tambah Case HP Baru</h2>
    
    <?php if (!empty($errors["db"])): ?>
        <div class="alert" style="background:#fee2e2; color:#991b1b;"><?= $errors["db"] ?></div>
    <?php endif; ?>

    <!-- PENTING: Tambahkan enctype="multipart/form-data" agar form bisa upload file -->
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <label for="name">Nama Case HP</label>
        <input id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errors["name"])): ?><small style="color:red;"><?= $errors["name"] ?></small><?php endif; ?>

        <label for="category">Tipe HP / Kategori</label>
        <input id="category" name="category" value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" placeholder="Contoh: iPhone 15, Samsung S24" required>

        <label for="price">Harga (Rp)</label>
        <input id="price" name="price" type="number" step="any" value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="stock">Stok</label>
        <input id="stock" name="stock" type="number" value="<?= htmlspecialchars($stock, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="image">Foto Case HP</label>
        <input id="image" name="image" type="file" accept="image/*">
        <?php if (!empty($errors["image"])): ?><small style="color:red;"><?= $errors["image"] ?></small><?php endif; ?>

        <br><br>
        <button type="submit">Simpan Produk</button>
        <a href="index.php" style="margin-left: 10px;">Batal</a>
    </form>
</div>
</body>
</html>