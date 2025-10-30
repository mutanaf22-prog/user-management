<?php
require_once __DIR__ . '/../src/init.php';
require_login();
$user = current_user($pdo);
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if(strlen($new) < 6) $errors[] = "Password baru minimal 6 karakter.";
    if($new !== $confirm) $errors[] = "Konfirmasi password tidak cocok.";

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row || !password_verify($current, $row['password'])) $errors[] = "Password saat ini salah.";

    if(empty($errors)) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $u = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $u->execute([$hash, $user['id']]);
        $success = "Password berhasil diubah.";
    }
}
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Ubah Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <h3>Ubah Password</h3>
  <p><a href="profile.php" class="btn btn-sm btn-outline-secondary">← Profil</a></p>
  <?php if($success) echo "<div class='alert alert-success'>".$success."</div>"; ?>
  <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>
  <form method="post" class="card p-3">
    <div class="mb-3"><label class="form-label">Password saat ini</label><input class="form-control" name="current_password" type="password" required></div>
    <div class="mb-3"><label class="form-label">Password baru</label><input class="form-control" name="new_password" type="password" required></div>
    <div class="mb-3"><label class="form-label">Konfirmasi password baru</label><input class="form-control" name="confirm_password" type="password" required></div>
    <button class="btn btn-primary" type="submit">Ubah Password</button>
  </form>
</div>
</body></html>
