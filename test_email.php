<?php
require_once 'library/config.php';
require_once 'library/mail.php';

// Simple email test page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test - Veterinary Clinic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #005a87; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Configuration Test</h1>
        <p>Use this page to test if your email configuration is working properly.</p>
        
        <?php
        if ($_POST && isset($_POST['test_email'])) {
            $email = $_POST['test_email'];
            
            echo "<div class='info'><strong>Testing email to:</strong> $email</div>";
            
            // Test basic mail function
            $result = test_email($email);
            
            if ($result) {
                echo "<div class='success'><strong>SUCCESS!</strong> Test email sent successfully. Check your inbox (and spam folder).</div>";
            } else {
                echo "<div class='error'><strong>FAILED!</strong> Email could not be sent. Check the configuration below.</div>";
            }
        }
        ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="test_email">Enter your email address to test:</label>
                <input type="email" id="test_email" name="test_email" required 
                       value="<?php echo isset($_POST['test_email']) ? htmlspecialchars($_POST['test_email']) : ''; ?>">
            </div>
            <button type="submit">Send Test Email</button>
        </form>
        
        <h2>Email Configuration Information</h2>
        <div class="info">
            <h3>Current PHP Mail Settings:</h3>
            <ul>
                <li><strong>SMTP:</strong> <?php echo ini_get('SMTP') ?: 'Not configured'; ?></li>
                <li><strong>SMTP Port:</strong> <?php echo ini_get('smtp_port') ?: 'Not configured'; ?></li>
                <li><strong>Sendmail From:</strong> <?php echo ini_get('sendmail_from') ?: 'Not configured'; ?></li>
                <li><strong>Sendmail Path:</strong> <?php echo ini_get('sendmail_path') ?: 'Not configured'; ?></li>
            </ul>
        </div>
        
        <h2>Configuration Instructions</h2>
        
        <h3>For XAMPP on Windows:</h3>
        <div class="info">
            <p><strong>1. Edit php.ini file:</strong></p>
            <pre>[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"</pre>
            
            <p><strong>2. Edit sendmail.ini file (in xampp/sendmail/ folder):</strong></p>
            <pre>smtp_server=smtp.gmail.com
smtp_port=587
auth_username=your-email@gmail.com
auth_password=your-app-password
force_sender=your-email@gmail.com</pre>
            
            <p><strong>Note:</strong> For Gmail, you need to use an "App Password" instead of your regular password.</p>
        </div>
        
        <h3>For WAMP on Windows:</h3>
        <div class="info">
            <p>Similar to XAMPP, but paths may be different. Check your WAMP installation directory.</p>
        </div>
        
        <h3>For Production Servers:</h3>
        <div class="info">
            <p>Contact your hosting provider for SMTP settings, or use a service like:</p>
            <ul>
                <li>SendGrid</li>
                <li>Mailgun</li>
                <li>Amazon SES</li>
                <li>PHPMailer with SMTP</li>
            </ul>
        </div>
        
        <h3>Quick Gmail Setup:</h3>
        <div class="info">
            <ol>
                <li>Enable 2-factor authentication on your Gmail account</li>
                <li>Generate an "App Password" for this application</li>
                <li>Use the app password in your sendmail.ini file</li>
                <li>Restart Apache after making changes</li>
            </ol>
        </div>
        
        <p><strong>After configuration:</strong> Restart your web server (Apache) and test again.</p>
    </div>
</body>
</html>