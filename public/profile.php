<?php
require_once __DIR__ . '/../src/init.php';
require_login();
$user = current_user($pdo);
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    if($fullname === '') $errors[] = "Nama lengkap tidak boleh kosong.";
    if(empty($errors)) {
        $upd = $pdo->prepare("UPDATE users SET fullname = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$fullname, $user['id']]);
        $success = "Profil berhasil diperbarui.";
    }
}
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Profil</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <h3>Profil</h3>
  <p><a href="dashboard.php" class="btn btn-sm btn-outline-secondary">← Dashboard</a> <a href="change_password.php" class="btn btn-sm btn-outline-warning">Ubah Password</a></p>
  <?php if($success) echo "<div class='alert alert-success'>".$success."</div>"; ?>
  <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>
  <form method="post" class="card p-3">
    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input class="form-control" name="fullname" required value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>"></div>
    <div class="mb-3"><label class="form-label">Email (username)</label><input class="form-control" disabled value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"></div>
    <button class="btn btn-primary" type="submit">Simpan</button>
  </form>
</div>
</body></html>
