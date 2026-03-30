<?php
// Handles admin email verification
session_start();
require_once '../connection/db_connection.php';
require_once '../connection/smtp_config.php';
require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';
require_once '../phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle login form submission (POST from login.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    $required_email = 'ojtstudents2026@gmail.com';
    $required_password = '123';
    
    if ($email === $required_email && $password === $required_password) {
        // Generate verification token
        $token = bin2hex(random_bytes(32));
        // Verification link to be sent via email
        $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify_admin.php?token=" . $token;

        // Store token in DB
        $stmt = $conn->prepare("INSERT INTO admin_verification (email, token, verified) VALUES (?, ?, 0)");
        $stmt->bind_param('ss', $email, $token);
        $stmt->execute();

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'RMNHS Admin Verification';
            $mail->Body = 'Click <a href="' . $verification_link . '">here</a> to verify and access the admin dashboard.';
            $mail->send();
            
            // Redirect back to login with success message
            header('Location: login.php?success=1');
            exit;
        } catch (Exception $e) {
            // Redirect back to login with error
            header('Location: login.php?error=Email+could+not+be+sent');
            exit;
        }
    } else {
        // Redirect back to login with error
        header('Location: login.php?error=Invalid+email+or+password');
        exit;
    }
}

// Handle token verification (GET from email link)
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check token validity
    $sql = "SELECT email FROM admin_verification WHERE token = ? AND verified = 0 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Mark as verified
        $update = $conn->prepare("UPDATE admin_verification SET verified = 1 WHERE token = ?");
        $update->bind_param('s', $token);
        $update->execute();
        $_SESSION['admin_logged_in'] = true;
        header('Location: home.php');
        exit;
    } else {
        // Display error page
        showVerificationError('Verification failed or already used', 'This verification link is either invalid or has already been used. Please request a new verification link by logging in again.');
    }
}

// If neither POST nor GET token, redirect to login
header('Location: login.php');
exit;

// Function to display verification error page
function showVerificationError($title, $message) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Error | RMNHS Admin</title>
    <link rel="stylesheet" href="verify-error.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            ⚠️
        </div>
        <h1 class="error-title"><?php echo htmlspecialchars($title); ?></h1>
        <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        
        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">Back to Login</a>
        </div>
        
        <div class="error-code">
            Error Code: VERIFICATION_INVALID
        </div>
        
        <p class="footer-text">
            If you continue to experience issues, please contact the IT department.
        </p>
    </div>
</body>
</html>
    <?php
    exit;
}
?>