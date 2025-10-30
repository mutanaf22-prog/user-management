<?php
require_once __DIR__ . '/../src/init.php';
$errors = [];
$success = '';

$token = $_GET['token'] ?? null;
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? null;
    $uid = (int)($_POST['uid'] ?? 0);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if(strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
    if($password !== $password_confirm) $errors[] = "Konfirmasi password tidak cocok.";

    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, expires_at FROM password_resets WHERE user_id = ? AND reset_token = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$uid, $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            $errors[] = "Token reset tidak valid.";
        } else if(strtotime($row['expires_at']) < time()) {
            $errors[] = "Token reset sudah kadaluarsa.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $u = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $u->execute([$hash, $uid]);
            $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$uid]);
            $success = "Password berhasil di-reset. Silakan login.";
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4>Reset Password</h4>
          <?php if($success) echo "<div class='alert alert-success'>".$success."</div>"; ?>
          <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>
          <?php if(!$success): ?>
          <form method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
            <div class="mb-3">
              <label class="form-label">Password baru</label>
              <input class="form-control" name="password" type="password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi password</label>
              <input class="form-control" name="password_confirm" type="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Reset Password</button>
          </form>
          <?php endif; ?>
          <p class="mt-3"><a href="login.php">Kembali ke Login</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
