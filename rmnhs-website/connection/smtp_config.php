<?php
// SMTP configuration for sending admin login codes.
// Update SMTP_PASS with your Gmail app password (do NOT commit real passwords to source control).

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'ojtstudents2026@gmail.com');
define('SMTP_PASS', 'glwt jvrn mvnl vajv'); // Gmail App Password
define('SMTP_SECURE', 'tls');
define('SMTP_FROM', 'ojtstudents2026@gmail.com');
define('SMTP_FROM_NAME', 'RMNHS Admin');

// Check SMTP config
if (SMTP_PASS === 'REPLACE_WITH_APP_PASSWORD') {
	die('SMTP password not set. Please update SMTP_PASS in smtp_config.php with your Gmail App Password.');
}

// Notes:
// - For Gmail, create an App Password and use it here instead of your account password.
// - Install PHPMailer via Composer: `composer require phpmailer/phpmailer`
// - If PHPMailer isn't installed, the script will fallback to PHP's mail() and show guidance.

return true;