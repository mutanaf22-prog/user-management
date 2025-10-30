<?php
require_once __DIR__ . '/../src/init.php';
session_unset();
session_destroy();
header('Location: login.php');
exit;
