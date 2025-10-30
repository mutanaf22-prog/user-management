<?php
require_once __DIR__ . '/../src/init.php';
$message = '';
if(isset($_GET['token'], $_GET['uid'])) {
    $token = $_GET['token'];
    $uid = (int)$_GET['uid'];

    $stmt = $pdo->prepare("SELECT id, status FROM users WHERE id = ? AND activation_token = ?");
    $stmt->execute([$uid, $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user) {
        if($user['status'] === 'ACTIVE') {
            $message = "Akun sudah aktif. Silakan login.";
        } else {
            $u = $pdo->prepare("UPDATE users SET status='ACTIVE', activation_token=NULL, updated_at = NOW() WHERE id = ?");
            $u->execute([$uid]);
            $message = "Aktivasi berhasil. Anda dapat login sekarang.";
        }
    } else {
        $message = "Token aktivasi tidak valid atau sudah kadaluarsa.";
    }
} else {
    $message = "Parameter tidak lengkap.";
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Aktivasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <div class="card"><div class="card-body">
    <h4>Aktivasi Akun</h4>
    <p><?php echo htmlspecialchars($message); ?></p>
    <p><a href="login.php" class="btn btn-sm btn-primary">Login</a></p>
  </div></div>
</div>
</body></html>
