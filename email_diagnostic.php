<?php
require_once 'library/config.php';
require_once 'library/mail.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section { background: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 4px; border-left: 4px solid #2196F3; }
        .error { background: #ffebee; border-left-color: #f44336; color: #c62828; }
        .success { background: #e8f5e8; border-left-color: #4caf50; color: #2e7d32; }
        .warning { background: #fff3e0; border-left-color: #ff9800; color: #ef6c00; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; white-space: pre-wrap; }
        .button { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #1976D2; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Email Diagnostic Tool</h1>
        
        <?php
        if ($_POST && isset($_POST['test_email'])) {
            $email = $_POST['test_email'];
            echo "<div class='section'><h3>Testing email to: $email</h3></div>";
            
            // Test email
            $result = test_email($email);
            
            if ($result) {
                echo "<div class='success'><strong>PHP mail() function returned TRUE</strong><br>But this doesn't guarantee delivery. Check the diagnostic information below.</div>";
            } else {
                echo "<div class='error'><strong>PHP mail() function returned FALSE</strong><br>There's definitely a configuration issue.</div>";
            }
        }
        ?>
        
        <form method="POST">
            <div style="margin: 20px 0;">
                <label for="test_email"><strong>Enter your email address to test:</strong></label><br>
                <input type="email" id="test_email" name="test_email" required style="width: 300px; padding: 8px; margin: 5px 0;">
                <button type="submit" class="button">Send Test Email</button>
            </div>
        </form>
        
        <h2>📊 Current PHP Mail Configuration</h2>
        <div class="section">
            <table>
                <tr><th>Setting</th><th>Current Value</th><th>Status</th></tr>
                <tr>
                    <td>SMTP Server</td>
                    <td><?php echo ini_get('SMTP') ?: 'Not set'; ?></td>
                    <td><?php echo ini_get('SMTP') ? '✅' : '❌ Should be smtp.gmail.com'; ?></td>
                </tr>
                <tr>
                    <td>SMTP Port</td>
                    <td><?php echo ini_get('smtp_port') ?: 'Not set'; ?></td>
                    <td><?php echo (ini_get('smtp_port') == '587') ? '✅' : '❌ Should be 587'; ?></td>
                </tr>
                <tr>
                    <td>Sendmail From</td>
                    <td><?php echo ini_get('sendmail_from') ?: 'Not set'; ?></td>
                    <td><?php echo ini_get('sendmail_from') ? '✅' : '❌ Should be your Gmail'; ?></td>
                </tr>
                <tr>
                    <td>Sendmail Path</td>
                    <td><?php echo ini_get('sendmail_path') ?: 'Not set'; ?></td>
                    <td><?php echo (strpos(ini_get('sendmail_path'), 'sendmail.exe') !== false) ? '✅' : '❌ Should point to sendmail.exe'; ?></td>
                </tr>
            </table>
        </div>
        
        <h2>📁 File Locations</h2>
        <div class="section">
            <strong>PHP Configuration File:</strong><br>
            <div class="code"><?php echo php_ini_loaded_file(); ?></div>
            
            <strong>Expected Sendmail Location:</strong><br>
            <div class="code">C:\xampp\sendmail\sendmail.exe</div>
            
            <strong>Sendmail Config File:</strong><br>
            <div class="code">C:\xampp\sendmail\sendmail.ini</div>
        </div>
        
        <h2>🔧 Configuration Issues Found</h2>
        <div class="section">
            <?php
            $issues = [];
            
            if (!ini_get('SMTP') || ini_get('SMTP') === 'localhost') {
                $issues[] = "SMTP server not configured for Gmail";
            }
            
            if (!ini_get('smtp_port') || ini_get('smtp_port') != '587') {
                $issues[] = "SMTP port should be 587 for Gmail";
            }
            
            if (!ini_get('sendmail_from')) {
                $issues[] = "sendmail_from not configured";
            }
            
            if (!ini_get('sendmail_path') || strpos(ini_get('sendmail_path'), 'sendmail.exe') === false) {
                $issues[] = "sendmail_path not pointing to sendmail.exe";
            }
            
            if (empty($issues)) {
                echo "<div class='success'>✅ PHP configuration looks correct!</div>";
            } else {
                echo "<div class='error'>";
                echo "<strong>Issues found:</strong><ul>";
                foreach ($issues as $issue) {
                    echo "<li>$issue</li>";
                }
                echo "</ul></div>";
            }
            ?>
        </div>
        
        <h2>📝 Debug Log</h2>
        <div class="section">
            <?php
            if (file_exists('email_debug.txt')) {
                echo "<strong>Recent email attempts:</strong>";
                echo "<div class='code'>" . htmlspecialchars(file_get_contents('email_debug.txt')) . "</div>";
            } else {
                echo "<div class='warning'>No debug log found. Send a test email to generate debug information.</div>";
            }
            ?>
        </div>
        
        <h2>🛠️ Quick Fixes</h2>
        
        <div class="section">
            <h3>Option 1: Fix XAMPP Configuration</h3>
            <p><strong>1. Edit php.ini file:</strong> <?php echo php_ini_loaded_file(); ?></p>
            <p>Find the [mail function] section and replace with:</p>
            <div class="code">SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"</div>
            
            <p><strong>2. Edit sendmail.ini file:</strong> C:\xampp\sendmail\sendmail.ini</p>
            <div class="code">smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=tls
auth_username=your-email@gmail.com
auth_password=your-16-character-app-password
force_sender=your-email@gmail.com</div>
            
            <p><strong>3. Get Gmail App Password:</strong></p>
            <ol>
                <li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>
                <li>Enable 2-Step Verification</li>
                <li>Generate App Password for "Mail"</li>
                <li>Use the 16-character password (no spaces)</li>
            </ol>
            
            <p><strong>4. Restart Apache in XAMPP</strong></p>
        </div>
        
        <div class="section">
            <h3>Option 2: Use Alternative Email Service</h3>
            <p>For easier setup, consider using:</p>
            <ul>
                <li><strong>Mailtrap</strong> - Email testing service</li>
                <li><strong>SendGrid</strong> - Reliable email delivery</li>
                <li><strong>PHPMailer</strong> - Better SMTP handling</li>
            </ul>
        </div>
        
        <div class="section">
            <h3>Option 3: Disable Email for Testing</h3>
            <p>If you just want to test the appointment system without email:</p>
            <div class="code">// In library/mail.php, replace send_email function with:
function send_email($data) {
    file_put_contents('email_log.txt', 
        date('Y-m-d H:i:s') . " - Would send to: " . $data['to'] . 
        " Subject: " . $data['sub'] . "\n", FILE_APPEND);
    return true; // Always return success
}</div>
        </div>
        
        <h2>🔍 Next Steps</h2>
        <div class="section">
            <ol>
                <li><strong>Check your spam folder</strong> - Gmail might be filtering the emails</li>
                <li><strong>Try a different email address</strong> - Some providers block emails from localhost</li>
                <li><strong>Check XAMPP error logs</strong> - Look in C:\xampp\apache\logs\error.log</li>
                <li><strong>Verify sendmail.exe exists</strong> - Check if C:\xampp\sendmail\sendmail.exe is present</li>
                <li><strong>Test with a simple script</strong> - Try the basic PHP mail() function</li>
            </ol>
        </div>
        
        <div class="warning">
            <strong>💡 Pro Tip:</strong> Email delivery from localhost is notoriously difficult. Many developers use email testing services like Mailtrap or simply log emails during development and only enable real email sending in production.
        </div>
    </div>
</body>
</html>