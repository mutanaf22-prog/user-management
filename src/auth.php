<?php
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function require_login() {
    if(!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
function current_user($pdo) {
    if(!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT id, email, fullname, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
