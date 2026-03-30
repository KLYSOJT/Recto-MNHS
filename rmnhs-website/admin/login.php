<?php
$error = '';
$success = false;

// Check for query parameters from verify_admin.php
if (isset($_GET['success'])) {
    $success = true;
    $error = 'A verification link has been sent to your email address.';
}
if (isset($_GET['error'])) {
    $error = urldecode($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recto Memorial NHS | Admin Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="portal-wrapper">
        <main class="glass-card">
            <div class="brand-panel">
                <div class="brand-inner">
                   
                </div>
            </div>

            <div class="login-panel">
                <div class="form-container">
                    <div class="form-header">
                        <span class="status-pill">Admin Portal </span>
                        <h2>Welcome Back</h2>
                        <p>Login to manage school operations.</p>
                    </div>
                    
                    <form method="POST" action="verify_admin.php">
                        <?php if ($error && !$success): ?>
                            <div class="error-message" style="color: #d32f2f; margin-bottom: 16px; padding: 12px; background-color: #ffebee; border-radius: 8px;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <!-- success modal -->
                            <div id="successModal" class="modal">
                                <div class="modal-content">
                                    <p><?php echo htmlspecialchars($error); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="floating-label-group">
                            <input type="email" name="email" id="email" required placeholder=" " value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <label for="email"> Email</label>
                        </div>
                        
                        <div class="floating-label-group">
                            <input type="password" name="password" id="password" required placeholder=" ">
                            <label for="password">Security Key</label>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-container">
                                <input type="checkbox"> <span class="checkmark"></span> Remember this device
                            </label>
                            <a href="#" class="forgot-link"></a>
                        </div>

                        <button type="submit" class="glow-button">
                            <span>Enter </span>
                        </button>
                    </form>

                    <footer class="portal-footer">
                        <p>&copy; 2026 RMNS Digital Infrastructure</p>
                    </footer>
                </div>
            </div>
        </main>
    </div>

    <?php if ($success): ?>
    <script>
        // close modal when clicking anywhere outside content
        window.addEventListener('click', function(event) {
            var modal = document.getElementById('successModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>