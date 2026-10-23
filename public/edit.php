<?php
require_once __DIR__ . '/../config/db.php';

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(["id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    die("Produk tidak ditemukan.");
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "Umum");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3)  $errors["name"] = "Nama minimal 3 karakter.";
    if ($price === false || $price <= 0)  $errors["price"] = "Harga harus > 0.";
    if ($stock === false || $stock < 0)  $errors["stock"] = "Stok tidak boleh negatif.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name=:name, category=:category, price=:price, stock=:stock WHERE id=:id");
        $stmt->execute([
            "name" => $name,
            "category" => $category,
            "price" => $price,
            "stock" => $stock,
            "id" => $id
        ]);
        header("Location: index.php?status=updated");
        exit;
    }
} else {
    $name = $product["name"];
    $category = $product["category"];
    $price = $product["price"];
    $stock = $product["stock"];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Edit Produk</h2>
    <form action="edit.php?id=<?= $id ?>" method="POST">
        <label for="name">Nama Produk</label>
        <input id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="category">Kategori</label>
        <input id="category" name="category" value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="price">Harga</label>
        <input id="price" name="price" type="number" step="any" value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="stock">Stok</label>
        <input id="stock" name="stock" type="number" value="<?= htmlspecialchars($stock, ENT_QUOTES, 'UTF-8') ?>" required>

        <br>
        <button type="submit">Perbarui Produk</button>
        <a href="index.php" style="margin-left: 10px;">Batal</a>
    </form>
</div>
</body>
</html>