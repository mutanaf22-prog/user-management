<?php
// src/config.php - edit as needed for your XAMPP environment

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'user_management');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty

// Base URL for activation/reset links (adjust if you place folder elsewhere)
define('BASE_URL', 'http://localhost/user-management/public');

// Mail settings - placeholder sender. Change to your real email & SMTP if you want sending to work.
define('MAIL_FROM', 'mutanaf22@gmail.com');
define('MAIL_NAME', 'Admin Gudang');

// If you want to use SMTP (recommended), set USE_SMTP = true and configure below.
define('USE_SMTP', false);

define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'smtp-user@example.com');
define('SMTP_PASS', 'smtp-password');
define('SMTP_SECURE', 'tls');
