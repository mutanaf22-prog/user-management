<?php
require_once __DIR__ . '/../src/init.php';
$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if(!$email) $errors[] = "Email tidak valid.";
    if(empty($password)) $errors[] = "Masukkan password.";

    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, password, status, fullname FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$user) {
            $errors[] = "Email tidak terdaftar.";
        } else {
            if($user['status'] !== 'ACTIVE') {
                $errors[] = "Akun belum aktif atau diblokir.";
            } else if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = "Password salah.";
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
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Login</h3>
          <?php foreach($errors as $e) echo "<div class='alert alert-danger'>".htmlspecialchars($e)."</div>"; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" name="password" type="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>
          <hr>
          <div class="d-flex justify-content-between">
            <a href="register.php">Daftar</a>
            <a href="forgot_password.php">Lupa Password?</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
