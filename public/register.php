<?php
require_once __DIR__ . '/../src/init.php';
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if(!$email) $errors[] = "Email tidak valid.";
    if(strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
    if($password !== $password_confirm) $errors[] = "Konfirmasi password tidak cocok.";

    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $errors[] = "Email sudah terdaftar.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $activation_token = bin2hex(random_bytes(16));
            $insert = $pdo->prepare("INSERT INTO users (email, password, fullname, role, status, activation_token) VALUES (?, ?, ?, 'admin_gudang', 'PENDING', ?)");
            $insert->execute([$email, $password_hash, $fullname, $activation_token]);
            $user_id = $pdo->lastInsertId();

            $activation_link = BASE_URL . "/activate.php?token=" . $activation_token . "&uid=" . $user_id;
            $subject = "Aktivasi Akun - Sistem Admin Gudang";
            $body = "<p>Halo ".htmlspecialchars($fullname).",</p>";
            $body .= "<p>Terima kasih mendaftar. Klik tautan berikut untuk mengaktifkan akun Anda:</p>";
            $body .= "<p><a href='$activation_link'>$activation_link</a></p>";
            $body .= "<p>Jika bukan Anda, abaikan email ini.</p>";

            if(send_email($email, $subject, $body, $fullname)) {
                $success = "Pendaftaran berhasil. Silakan cek email untuk tautan aktivasi.";
            } else {
                $errors[] = "Gagal mengirim email aktivasi. Periksa konfigurasi mail server.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registrasi - Admin Gudang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Daftar Admin Gudang</h3>
          <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
          <?php if($errors): foreach($errors as $e){ echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; } endif; ?>
          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Nama lengkap</label>
              <input class="form-control" name="fullname" required value="<?php echo htmlspecialchars($fullname ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" name="password" type="password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input class="form-control" name="password_confirm" type="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Daftar</button>
          </form>
          <hr>
          <div class="text-center">
            <a href="login.php">Sudah punya akun? Login</a>
          </div>
        </div>
      </div>
      <p class="text-muted text-center mt-3">Folder untuk XAMPP: <code>htdocs/user-management</code></p>
    </div>
  </div>
</div>
</body>
</html>
