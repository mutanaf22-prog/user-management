<?php
require_once __DIR__ . '/../src/init.php';
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD']==='POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if(!$email) $errors[] = "Email tidak valid.";
    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE email = ? AND status='ACTIVE'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$user) {
            $errors[] = "Email tidak terdaftar atau akun belum aktif.";
        } else {
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $ins = $pdo->prepare("INSERT INTO password_resets (user_id, reset_token, expires_at) VALUES (?, ?, ?)");
            $ins->execute([$user['id'], $token, $expires]);

            $link = BASE_URL . "/reset_password.php?token=$token&uid=".$user['id'];
            $subject = "Reset Password - Sistem Gudang";
            $body = "<p>Halo ".htmlspecialchars($user['fullname']).",</p>";
            $body .= "<p>Silakan klik link berikut untuk mereset password (berlaku 1 jam):</p>";
            $body .= "<p><a href='$link'>$link</a></p>";
            if(send_email($email, $subject, $body)) {
                $success = "Tautan reset password telah dikirim ke email Anda.";
            } else {
                $errors[] = "Gagal mengirim email reset. Periksa konfigurasi mail server.";
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
  <title>Lupa Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
          <h4>Lupa Password</h4>
          <?php if($success) echo "<div class='alert alert-success'>".$success."</div>"; ?>
          <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Kirim Tautan Reset</button>
          </form>
          <p class="mt-3"><a href="login.php">Kembali ke Login</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
