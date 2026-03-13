<?php
// Quick Email Fix - Manual Configuration Helper
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Email Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 15px 0; border: 1px solid #f5c6cb; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 15px 0; border: 1px solid #c3e6cb; }
        .warning { background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 4px; margin: 15px 0; border: 1px solid #ffcc02; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 15px 0; border: 1px solid #bee5eb; }
        .code { background: #f5f5f5; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; white-space: pre-wrap; border: 1px solid #ddd; }
        .button { background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #c82333; }
        .button.primary { background: #007bff; }
        .button.primary:hover { background: #0056b3; }
        .step { background: #e8f4fd; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; border-radius: 4px; }
        h1, h2 { color: #333; }
        .file-path { background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 Email Configuration Issue Detected</h1>
        
        <div class="error">
            <h3>❌ Problem Identified:</h3>
            <p>Your XAMPP is still using default localhost settings instead of Gmail SMTP.</p>
            <ul>
                <li><strong>Current SMTP:</strong> localhost (❌ Wrong)</li>
                <li><strong>Current Port:</strong> 25 (❌ Wrong)</li>
                <li><strong>Should be:</strong> smtp.gmail.com:587</li>
            </ul>
        </div>
        
        <h2>🚀 Quick Fix Options</h2>
        
        <div class="step">
            <h3>Option 1: Use Automated Configuration Tool (Recommended)</h3>
            <p>I created an automated tool that will fix this for you:</p>
            <a href="auto_email_config.php" class="button primary">🔧 Open Automated Email Config</a>
            <p><strong>Steps:</strong></p>
            <ol>
                <li>Get your Gmail App Password (instructions below)</li>
                <li>Run XAMPP as Administrator</li>
                <li>Use the automated tool</li>
                <li>Restart Apache</li>
            </ol>
        </div>
        
        <div class="step">
            <h3>Option 2: Manual Configuration</h3>
            <p><strong>Your php.ini file location:</strong></p>
            <div class="file-path"><?php echo php_ini_loaded_file(); ?></div>
            
            <p><strong>Find the [mail function] section and replace it with:</strong></p>
            <div class="code">[mail function]
; For Win32 only.
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
mail.add_x_header = Off</div>
            
            <p><strong>Create/Edit sendmail.ini file at:</strong></p>
            <div class="file-path">C:\xampp\sendmail\sendmail.ini</div>
            
            <p><strong>Replace entire content with:</strong></p>
            <div class="code">smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=tls
default_domain=gmail.com
auth_username=your-email@gmail.com
auth_password=your-16-character-app-password
force_sender=your-email@gmail.com
error_logfile=error.log
debug_logfile=debug.log</div>
        </div>
        
        <div class="warning">
            <h3>📋 Gmail App Password Setup</h3>
            <p>You need a Gmail App Password (not your regular password):</p>
            <ol>
                <li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>
                <li>Enable <strong>2-Step Verification</strong> (if not already enabled)</li>
                <li>Click <strong>"App passwords"</strong></li>
                <li>Select <strong>"Mail"</strong> and <strong>"Windows Computer"</strong></li>
                <li>Copy the <strong>16-character password</strong> (no spaces)</li>
                <li>Use this password in the configuration, NOT your regular Gmail password</li>
            </ol>
        </div>
        
        <div class="info">
            <h3>⚠️ Important Steps After Configuration:</h3>
            <ol>
                <li><strong>Replace placeholders:</strong> Change "your-email@gmail.com" to your actual Gmail address</li>
                <li><strong>Replace password:</strong> Use your 16-character App Password</li>
                <li><strong>Restart Apache:</strong> Stop and start Apache in XAMPP Control Panel</li>
                <li><strong>Test email:</strong> Use the test page to verify it works</li>
            </ol>
        </div>
        
        <h2>🧪 Test After Configuration</h2>
        <div class="step">
            <p>After making changes and restarting Apache:</p>
            <a href="simple_email_test.php" class="button">🧪 Test Email Again</a>
            <a href="test_email.php" class="button">📧 Advanced Email Test</a>
        </div>
        
        <div class="error">
            <h3>🚨 Common Issues:</h3>
            <ul>
                <li><strong>Permission Denied:</strong> Run XAMPP as Administrator</li>
                <li><strong>File Not Found:</strong> Create the sendmail folder if it doesn't exist</li>
                <li><strong>Still localhost:</strong> Make sure you edited the correct php.ini file</li>
                <li><strong>Authentication Failed:</strong> Use App Password, not regular password</li>
            </ul>
        </div>
        
        <div class="success">
            <h3>✅ Alternative: Disable Email for Testing</h3>
            <p>If you just want to test the appointment system without email:</p>
            <a href="#" onclick="disableEmail()" class="button">🔇 Disable Email (Testing Mode)</a>
            <p>This will make the system work normally but just log emails instead of sending them.</p>
        </div>
    </div>
    
    <script>
    function disableEmail() {
        if (confirm('This will disable actual email sending and just log emails to a file. The appointment system will work normally. Continue?')) {
            fetch('disable_email.php', {method: 'POST'})
            .then(response => response.text())
            .then(data => {
                alert('Email disabled for testing. Check email_log.txt for what would be sent.');
                location.reload();
            });
        }
    }
    </script>
</body>
</html>