<?php
require_once __DIR__ . '/../src/init.php';
require_login();
$user = current_user($pdo);

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $desc = trim($_POST['description'] ?? '');

    if($name === '') $errors[] = "Nama produk wajib diisi.";
    if(empty($errors)) {
        if($_POST['op'] === 'create') {
            $ins = $pdo->prepare("INSERT INTO products (sku,name,description,price,stock,created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$sku, $name, $desc, $price, $stock, $user['id']]);
            $success = "Produk berhasil ditambahkan.";
        } elseif($_POST['op'] === 'update' && isset($_POST['id'])) {
            $pid = (int)$_POST['id'];
            $upd = $pdo->prepare("UPDATE products SET sku=?, name=?, description=?, price=?, stock=?, updated_at = NOW() WHERE id = ?");
            $upd->execute([$sku,$name,$desc,$price,$stock,$pid]);
            $success = "Produk berhasil diperbarui.";
            $action = 'list';
        }
    }
}

if($action === 'delete' && $id) {
    $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $del->execute([$id]);
    header('Location: products.php');
    exit;
}
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Produk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Kelola Produk</h3>
    <div>
      <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Dashboard</a>
      <a href="products.php?action=create" class="btn btn-sm btn-primary">Tambah Produk</a>
    </div>
  </div>

  <?php if($success) echo "<div class='alert alert-success'>".$success."</div>"; ?>
  <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>

  <?php if($action === 'list'): 
    $stmt = $pdo->query("SELECT p.*, u.email as creator FROM products p LEFT JOIN users u ON p.created_by = u.id ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>SKU</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Dibuat oleh</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach($products as $p): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php echo htmlspecialchars($p['sku']); ?></td>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo number_format($p['price'],2); ?></td>
          <td><?php echo (int)$p['stock']; ?></td>
          <td><?php echo htmlspecialchars($p['creator']); ?></td>
          <td>
            <a href="products.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            <a href="products.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php elseif($action === 'create' || ($action === 'edit' && $id)): 
    $product = ['id'=>'','sku'=>'','name'=>'','description'=>'','price'=>0,'stock'=>0];
    if($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if($prod) $product = $prod;
    }
  ?>
    <form method="post" class="card p-3">
      <input type="hidden" name="op" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
      <?php if($action === 'edit'): ?><input type="hidden" name="id" value="<?php echo $product['id']; ?>"><?php endif; ?>
      <div class="mb-3"><label class="form-label">SKU</label><input class="form-control" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>"></div>
      <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>"></div>
      <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea></div>
      <div class="mb-3"><label class="form-label">Harga</label><input class="form-control" name="price" type="number" step="0.01" required value="<?php echo htmlspecialchars($product['price']); ?>"></div>
      <div class="mb-3"><label class="form-label">Stok</label><input class="form-control" name="stock" type="number" required value="<?php echo htmlspecialchars($product['stock']); ?>"></div>
      <button class="btn btn-primary" type="submit"><?php echo $action === 'create' ? 'Tambah' : 'Update'; ?></button>
    </form>
  <?php endif; ?>
</div>
</body></html>
