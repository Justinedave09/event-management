<?php
// Simple email test to isolate the issue
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Email Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .result { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        .code { background: #f5f5f5; padding: 10px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Simple Email Test</h1>
    
    <?php
    if ($_POST && isset($_POST['email'])) {
        $to = $_POST['email'];
        $subject = "Simple Test Email";
        $message = "This is a simple test email from your XAMPP server.";
        $headers = "From: test@localhost";
        
        echo "<div class='info'><strong>Attempting to send email...</strong></div>";
        echo "<div class='info'>To: $to<br>Subject: $subject</div>";
        
        // Capture any errors
        ob_start();
        $result = mail($to, $subject, $message, $headers);
        $output = ob_get_clean();
        
        if ($result) {
            echo "<div class='success'>✅ mail() function returned TRUE</div>";
        } else {
            echo "<div class='error'>❌ mail() function returned FALSE</div>";
        }
        
        // Show current configuration
        echo "<div class='info'>";
        echo "<strong>Current PHP mail settings:</strong><br>";
        echo "SMTP: " . (ini_get('SMTP') ?: 'Not set') . "<br>";
        echo "SMTP Port: " . (ini_get('smtp_port') ?: 'Not set') . "<br>";
        echo "Sendmail From: " . (ini_get('sendmail_from') ?: 'Not set') . "<br>";
        echo "Sendmail Path: " . (ini_get('sendmail_path') ?: 'Not set') . "<br>";
        echo "</div>";
        
        // Check for errors
        $error = error_get_last();
        if ($error && (strpos($error['message'], 'mail') !== false || strpos($error['message'], 'smtp') !== false)) {
            echo "<div class='error'><strong>PHP Error:</strong><br>" . $error['message'] . "</div>";
        }
        
        // Show what needs to be configured
        if (!ini_get('SMTP') || ini_get('SMTP') === 'localhost') {
            echo "<div class='error'>";
            echo "<strong>❌ SMTP Configuration Missing</strong><br>";
            echo "Your php.ini file needs to be configured for Gmail SMTP.<br>";
            echo "Current SMTP setting: " . (ini_get('SMTP') ?: 'localhost (default)') . "<br>";
            echo "Should be: smtp.gmail.com";
            echo "</div>";
        }
    }
    ?>
    
    <form method="POST">
        <p>
            <label>Enter your email address:</label><br>
            <input type="email" name="email" required style="width: 300px; padding: 8px;">
        </p>
        <p>
            <button type="submit" style="padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px;">Send Simple Test Email</button>
        </p>
    </form>
    
    <h2>🔧 Quick Fix Instructions</h2>
    
    <div class="info">
        <strong>The issue:</strong> Your XAMPP is not configured to send emails through Gmail SMTP.
    </div>
    
    <h3>Step 1: Edit php.ini</h3>
    <p>File location: <code><?php echo php_ini_loaded_file(); ?></code></p>
    <p>Find the [mail function] section and change it to:</p>
    <div class="code">SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"</div>
    
    <h3>Step 2: Edit sendmail.ini</h3>
    <p>File location: <code>C:\xampp\sendmail\sendmail.ini</code></p>
    <p>Replace the entire file content with:</p>
    <div class="code">smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=tls
auth_username=your-email@gmail.com
auth_password=your-gmail-app-password
force_sender=your-email@gmail.com</div>
    
    <h3>Step 3: Get Gmail App Password</h3>
    <ol>
        <li>Go to <a href="https://myaccount.google.com/security">Google Account Security</a></li>
        <li>Enable 2-Step Verification (if not already enabled)</li>
        <li>Click "App passwords"</li>
        <li>Select "Mail" and "Windows Computer"</li>
        <li>Copy the 16-character password (no spaces)</li>
        <li>Use this password in sendmail.ini (not your regular Gmail password)</li>
    </ol>
    
    <h3>Step 4: Restart Apache</h3>
    <p>In XAMPP Control Panel: Stop Apache, then Start Apache</p>
    
    <h3>Alternative: Disable Email for Testing</h3>
    <p>If you just want to test the appointment system without email, I can disable email sending and just log what would be sent.</p>
    
    <div class="info">
        <strong>💡 Note:</strong> Email from localhost is tricky. Even if configured correctly, some email providers might reject emails from local development servers. Gmail App Passwords are required for SMTP authentication.
    </div>
</body>
</html>