<?php
// Central configuration (edit these!)

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// SMTP settings
// If your host uses 587 (TLS): set SMTP_SECURE to 'tls' and SMTP_PORT 587
// If it uses 465 (SSL): set SMTP_SECURE to 'ssl' and SMTP_PORT 465

define('SMTP_HOST', 'smtp.yourdomain.com');
define('SMTP_USER', 'you@yourdomain.com');
define('SMTP_PASS', 'your_smtp_password_or_app_password');
define('SMTP_FROM', 'you@yourdomain.com');

define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'

define('SMTP_PORT', 587);     // 587 or 465
?>
