# User Management - Admin Gudang (PHP + MySQL)

Folder name: `user-management` — ready for XAMPP (put inside `htdocs` or set project root there).

## Quick setup (XAMPP)
1. Copy the `user-management` folder into `C:\xampp\htdocs\` (Windows) or your htdocs.
2. Start Apache & MySQL in XAMPP.
3. Import SQL:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create/import `sql/user_management.sql`
4. Edit `src/config.php` if you need to change DB credentials or mail settings.
5. Access app: `http://localhost/user-management/public/register.php`

## Default credentials/settings
- DB: `localhost`, user `root`, no password (adjust in src/config.php if different)
- MAIL_FROM default: mutanaf22@gmail.com (placeholder). Change in `src/config.php`.
- UI uses Bootstrap 5.

## Files included
- src/: config, init, auth, mailer
- public/: register, activate, login, logout, forgot/reset password, dashboard, products, profile, change password
- sql/user_management.sql
- README.md

