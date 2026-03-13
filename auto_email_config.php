<?php
// Automated Email Configuration Tool for XAMPP
?>
<!DOCTYPE html>
<html>
<head>
    <title>Automated Email Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input:focus { border-color: #2196F3; outline: none; }
        .button { background: #2196F3; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .button:hover { background: #1976D2; }
        .button:disabled { background: #ccc; cursor: not-allowed; }
        .result { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3e0; color: #ef6c00; border: 1px solid #ffcc02; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }
        .step { background: #e8f4fd; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; border-radius: 4px; }
        .file-info { background: #f9f9f9; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; }
        h1, h2 { color: #333; }
        .checkbox-group { display: flex; align-items: center; margin: 10px 0; }
        .checkbox-group input[type="checkbox"] { width: auto; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Automated Email Configuration for XAMPP</h1>
        
        <?php
        // Get current file locations
        $php_ini_file = php_ini_loaded_file();
        $sendmail_ini_file = 'C:\\xampp\\sendmail\\sendmail.ini';
        $xampp_path = 'C:\\xampp';
        
        // Try to detect XAMPP path
        if (strpos($php_ini_file, 'xampp') !== false) {
            $xampp_path = substr($php_ini_file, 0, strpos($php_ini_file, 'php\\php.ini'));
            $sendmail_ini_file = $xampp_path . 'sendmail\\sendmail.ini';
        }
        
        if ($_POST && isset($_POST['configure'])) {
            $gmail_email = $_POST['gmail_email'];
            $gmail_password = $_POST['gmail_password'];
            $backup_configs = isset($_POST['backup_configs']);
            
            $errors = [];
            $success_messages = [];
            
            // Validate inputs
            if (!filter_var($gmail_email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email address format";
            }
            
            if (strlen($gmail_password) !== 16) {
                $errors[] = "Gmail App Password should be exactly 16 characters";
            }
            
            if (empty($errors)) {
                // Backup original files if requested
                if ($backup_configs) {
                    if (file_exists($php_ini_file)) {
                        copy($php_ini_file, $php_ini_file . '.backup.' . date('Y-m-d-H-i-s'));
                        $success_messages[] = "✅ Backed up php.ini";
                    }
                    
                    if (file_exists($sendmail_ini_file)) {
                        copy($sendmail_ini_file, $sendmail_ini_file . '.backup.' . date('Y-m-d-H-i-s'));
                        $success_messages[] = "✅ Backed up sendmail.ini";
                    }
                }
                
                // Configure php.ini
                $php_ini_success = configure_php_ini($php_ini_file, $gmail_email, $xampp_path);
                if ($php_ini_success) {
                    $success_messages[] = "✅ Successfully configured php.ini";
                } else {
                    $errors[] = "❌ Failed to configure php.ini - check file permissions";
                }
                
                // Configure sendmail.ini
                $sendmail_ini_success = configure_sendmail_ini($sendmail_ini_file, $gmail_email, $gmail_password);
                if ($sendmail_ini_success) {
                    $success_messages[] = "✅ Successfully configured sendmail.ini";
                } else {
                    $errors[] = "❌ Failed to configure sendmail.ini - check file permissions";
                }
                
                // Show results
                if (!empty($success_messages)) {
                    echo "<div class='success'>";
                    echo "<h3>🎉 Configuration Successful!</h3>";
                    foreach ($success_messages as $msg) {
                        echo "<p>$msg</p>";
                    }
                    echo "<p><strong>⚠️ Important:</strong> You must restart Apache in XAMPP Control Panel for changes to take effect!</p>";
                    echo "</div>";
                }
                
                if (!empty($errors)) {
                    echo "<div class='error'>";
                    echo "<h3>❌ Configuration Errors:</h3>";
                    foreach ($errors as $error) {
                        echo "<p>$error</p>";
                    }
                    echo "</div>";
                }
                
                // Show what was configured
                if ($php_ini_success || $sendmail_ini_success) {
                    echo "<div class='info'>";
                    echo "<h3>📝 Configuration Applied:</h3>";
                    echo "<p><strong>Gmail Email:</strong> $gmail_email</p>";
                    echo "<p><strong>SMTP Server:</strong> smtp.gmail.com:587</p>";
                    echo "<p><strong>Encryption:</strong> TLS</p>";
                    echo "<p><strong>Files Modified:</strong></p>";
                    echo "<ul>";
                    if ($php_ini_success) echo "<li>$php_ini_file</li>";
                    if ($sendmail_ini_success) echo "<li>$sendmail_ini_file</li>";
                    echo "</ul>";
                    echo "</div>";
                }
            } else {
                echo "<div class='error'>";
                echo "<h3>❌ Validation Errors:</h3>";
                foreach ($errors as $error) {
                    echo "<p>$error</p>";
                }
                echo "</div>";
            }
        }
        ?>
        
        <div class="info">
            <h3>📍 Detected File Locations:</h3>
            <div class="file-info">
                <strong>PHP Configuration:</strong> <?php echo $php_ini_file; ?><br>
                <strong>Sendmail Configuration:</strong> <?php echo $sendmail_ini_file; ?><br>
                <strong>XAMPP Path:</strong> <?php echo $xampp_path; ?>
            </div>
        </div>
        
        <div class="step">
            <h3>📋 Before You Start:</h3>
            <ol>
                <li><strong>Enable 2-Factor Authentication</strong> on your Gmail account</li>
                <li><strong>Generate an App Password:</strong>
                    <ul>
                        <li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>
                        <li>Click "2-Step Verification" → "App passwords"</li>
                        <li>Select "Mail" and "Windows Computer"</li>
                        <li>Copy the 16-character password (no spaces)</li>
                    </ul>
                </li>
                <li><strong>Run XAMPP as Administrator</strong> (for file write permissions)</li>
            </ol>
        </div>
        
        <form method="POST">
            <h2>🔧 Email Configuration</h2>
            
            <div class="form-group">
                <label for="gmail_email">Gmail Email Address:</label>
                <input type="email" id="gmail_email" name="gmail_email" required 
                       placeholder="your-email@gmail.com"
                       value="<?php echo isset($_POST['gmail_email']) ? htmlspecialchars($_POST['gmail_email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="gmail_password">Gmail App Password (16 characters):</label>
                <input type="password" id="gmail_password" name="gmail_password" required 
                       placeholder="abcdefghijklmnop" maxlength="16" minlength="16"
                       pattern="[a-zA-Z0-9]{16}" title="Must be exactly 16 characters">
                <small style="color: #666;">⚠️ Use App Password, not your regular Gmail password!</small>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="backup_configs" name="backup_configs" checked>
                <label for="backup_configs">Create backup of original configuration files</label>
            </div>
            
            <div class="form-group">
                <button type="submit" name="configure" class="button">🚀 Configure Email Settings</button>
            </div>
        </form>
        
        <div class="warning">
            <h3>⚠️ Important Notes:</h3>
            <ul>
                <li><strong>File Permissions:</strong> Run XAMPP as Administrator if you get permission errors</li>
                <li><strong>Restart Required:</strong> You must restart Apache after configuration</li>
                <li><strong>App Password:</strong> Use Gmail App Password, not your regular password</li>
                <li><strong>Backup:</strong> Original files will be backed up with timestamp</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>🧪 After Configuration:</h3>
            <ol>
                <li><strong>Restart Apache</strong> in XAMPP Control Panel</li>
                <li><strong>Test Email:</strong> <a href="test_email.php" target="_blank">Use Email Test Page</a></li>
                <li><strong>Test Appointments:</strong> Book a test appointment to verify email notifications</li>
            </ol>
        </div>
        
        <div class="info">
            <h3>🔍 Troubleshooting:</h3>
            <ul>
                <li><strong>Permission Denied:</strong> Run XAMPP as Administrator</li>
                <li><strong>Files Not Found:</strong> Check if XAMPP sendmail folder exists</li>
                <li><strong>Still Not Working:</strong> Check <a href="email_diagnostic.php">Email Diagnostic Tool</a></li>
                <li><strong>Gmail Issues:</strong> Verify 2FA is enabled and App Password is correct</li>
            </ul>
        </div>
    </div>
</body>
</html>

<?php
function configure_php_ini($php_ini_file, $gmail_email, $xampp_path) {
    if (!file_exists($php_ini_file) || !is_writable($php_ini_file)) {
        return false;
    }
    
    $content = file_get_contents($php_ini_file);
    
    // Prepare the new mail function configuration
    $sendmail_path = str_replace('\\', '\\\\', $xampp_path) . 'sendmail\\\\sendmail.exe';
    
    $new_mail_config = "[mail function]\n";
    $new_mail_config .= "; For Win32 only.\n";
    $new_mail_config .= "; http://php.net/smtp\n";
    $new_mail_config .= "SMTP = smtp.gmail.com\n";
    $new_mail_config .= "; http://php.net/smtp-port\n";
    $new_mail_config .= "smtp_port = 587\n\n";
    $new_mail_config .= "; For Win32 only.\n";
    $new_mail_config .= "; http://php.net/sendmail-from\n";
    $new_mail_config .= "sendmail_from = $gmail_email\n\n";
    $new_mail_config .= "; For Unix only. You may supply arguments as well (default: \"sendmail -t -i\").\n";
    $new_mail_config .= "; http://php.net/sendmail-path\n";
    $new_mail_config .= "sendmail_path = \"\\\"$sendmail_path\\\" -t\"\n\n";
    $new_mail_config .= "; Force the addition of the specified parameters to be passed as extra parameters\n";
    $new_mail_config .= "; to the sendmail binary. These parameters will always replace the value of\n";
    $new_mail_config .= "; the 5th parameter to mail().\n";
    $new_mail_config .= ";mail.force_extra_parameters =\n\n";
    $new_mail_config .= "; Add X-PHP-Originating-Script: that will include uid of the script followed by the filename\n";
    $new_mail_config .= "mail.add_x_header = Off\n\n";
    $new_mail_config .= "; The path to a log file that will log all mail() calls. Log entries include\n";
    $new_mail_config .= "; the full path of the script, line number, To address and headers.\n";
    $new_mail_config .= ";mail.log =\n";
    $new_mail_config .= "; Log mail to syslog (Event Log on Windows).\n";
    $new_mail_config .= ";mail.log = syslog\n";
    
    // Replace the existing [mail function] section
    $pattern = '/\[mail function\].*?(?=\[|\Z)/s';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $new_mail_config, $content);
    } else {
        // If no [mail function] section exists, append it
        $content .= "\n\n" . $new_mail_config;
    }
    
    return file_put_contents($php_ini_file, $content) !== false;
}

function configure_sendmail_ini($sendmail_ini_file, $gmail_email, $gmail_password) {
    // Create sendmail directory if it doesn't exist
    $sendmail_dir = dirname($sendmail_ini_file);
    if (!is_dir($sendmail_dir)) {
        mkdir($sendmail_dir, 0755, true);
    }
    
    $sendmail_config = "[sendmail]\n\n";
    $sendmail_config .= "; you must change mail.mydomain.com to your smtp server,\n";
    $sendmail_config .= "; or to IIS's \"pickup\" directory. (generally C:\\Inetpub\\mailroot\\Pickup)\n";
    $sendmail_config .= "; emails delivered via IIS's pickup directory cause sendmail to\n";
    $sendmail_config .= "; run quicker, but you won't get error messages back to the calling\n";
    $sendmail_config .= "; application.\n\n";
    $sendmail_config .= "smtp_server=smtp.gmail.com\n\n";
    $sendmail_config .= "; smtp port (normally 25)\n";
    $sendmail_config .= "smtp_port=587\n\n";
    $sendmail_config .= "; SMTPS (SSL) support\n";
    $sendmail_config .= ";   auto = use SSL for port 465, otherwise try to use TLS\n";
    $sendmail_config .= ";   ssl  = alway use SSL\n";
    $sendmail_config .= ";   tls  = always use TLS\n";
    $sendmail_config .= ";   none = never try to use SSL\n";
    $sendmail_config .= "smtp_ssl=tls\n\n";
    $sendmail_config .= "; the default domain for this server will be read from the registry\n";
    $sendmail_config .= "; this will be appended to email addresses when one isn't provided\n";
    $sendmail_config .= "; if you want to override the value in the registry, uncomment and modify\n";
    $sendmail_config .= "default_domain=gmail.com\n\n";
    $sendmail_config .= "; log smtp errors to error.log (defaults to same directory as sendmail.exe)\n";
    $sendmail_config .= "; uncomment to enable logging\n";
    $sendmail_config .= "error_logfile=error.log\n\n";
    $sendmail_config .= "; create debug log as debug.log (defaults to same directory as sendmail.exe)\n";
    $sendmail_config .= "; uncomment to enable debugging\n";
    $sendmail_config .= "debug_logfile=debug.log\n\n";
    $sendmail_config .= "; if your smtp server requires authentication, modify the following two lines\n";
    $sendmail_config .= "auth_username=$gmail_email\n";
    $sendmail_config .= "auth_password=$gmail_password\n\n";
    $sendmail_config .= "; if your smtp server uses pop3 before smtp authentication, modify the\n";
    $sendmail_config .= "; following three lines. do not enable unless it is required.\n";
    $sendmail_config .= "pop3_server=\n";
    $sendmail_config .= "pop3_username=\n";
    $sendmail_config .= "pop3_password=\n\n";
    $sendmail_config .= "; force the sender to always be the following email address\n";
    $sendmail_config .= "; this will only affect the \"MAIL FROM\" command, it won't modify\n";
    $sendmail_config .= "; the \"From: \" header of the email message.\n";
    $sendmail_config .= "force_sender=$gmail_email\n\n";
    $sendmail_config .= "; force the sender to always be the following email address\n";
    $sendmail_config .= "; this will only affect the \"RCTP TO\" command, it won't modify\n";
    $sendmail_config .= "; the \"To: \" header of the email message.\n";
    $sendmail_config .= "force_recipient=\n\n";
    $sendmail_config .= "; sendmail will use your hostname and your default_domain in the ehlo/helo\n";
    $sendmail_config .= "; smtp greeting. you can manually set the ehlo/helo name if required\n";
    $sendmail_config .= "hostname=\n";
    
    return file_put_contents($sendmail_ini_file, $sendmail_config) !== false;
}
?>