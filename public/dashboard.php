<?php
require_once __DIR__ . '/../src/init.php';
require_login();
$user = current_user($pdo);
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Dashboard Admin Gudang</h3>
    <div>
      <a href="profile.php" class="btn btn-sm btn-outline-secondary">Profil</a>
      <a href="products.php" class="btn btn-sm btn-outline-primary">Produk</a>
      <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
  </div>
  <div class="card p-3">
    <p>Selamat datang, <?php echo htmlspecialchars($user['fullname'] ?? $user['email']); ?>.</p>
    <?php
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM products");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
    echo "<p>Total produk: <strong>".(int)$count."</strong></p>";
    ?>
  </div>
</div>
</body></html>
