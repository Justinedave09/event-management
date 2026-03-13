<?php
// XAMPP Email Configuration Checker and Fixer
?>
<!DOCTYPE html>
<html>
<head>
    <title>XAMPP Email Configuration Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .step { background: #e8f4fd; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; border-radius: 4px; }
        .error { background: #ffebee; border-left-color: #f44336; color: #c62828; }
        .success { background: #e8f5e8; border-left-color: #4caf50; color: #2e7d32; }
        .warning { background: #fff3e0; border-left-color: #ff9800; color: #ef6c00; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .button { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #1976D2; }
        h1, h2 { color: #333; }
        .current-config { background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 XAMPP Email Configuration Fix</h1>
        
        <div class="error">
            <strong>Current Issue:</strong> PHP is trying to connect to localhost:25 instead of Gmail SMTP.
            <br>This means your php.ini file needs to be configured for Gmail SMTP.
        </div>

        <h2>📋 Current PHP Mail Settings</h2>
        <div class="current-config">
            <strong>SMTP Server:</strong> <?php echo ini_get('SMTP') ?: 'Not set (defaults to localhost)'; ?><br>
            <strong>SMTP Port:</strong> <?php echo ini_get('smtp_port') ?: 'Not set (defaults to 25)'; ?><br>
            <strong>Sendmail From:</strong> <?php echo ini_get('sendmail_from') ?: 'Not set'; ?><br>
            <strong>Sendmail Path:</strong> <?php echo ini_get('sendmail_path') ?: 'Not set'; ?><br>
        </div>

        <h2>🛠️ Step-by-Step Fix</h2>

        <div class="step">
            <h3>Step 1: Find Your php.ini File</h3>
            <p><strong>Your php.ini file is located at:</strong></p>
            <div class="code"><?php echo php_ini_loaded_file(); ?></div>
            <p>This is usually: <code>C:\xampp\php\php.ini</code></p>
        </div>

        <div class="step">
            <h3>Step 2: Edit php.ini File</h3>
            <p>Open the php.ini file in a text editor (like Notepad++) and find the <code>[mail function]</code> section.</p>
            <p><strong>Replace the entire [mail function] section with:</strong></p>
            <div class="code">[mail function]
; For Win32 only.
; http://php.net/smtp
SMTP = smtp.gmail.com
; http://php.net/smtp-port
smtp_port = 587

; For Win32 only.
; http://php.net/sendmail-from
sendmail_from = your-email@gmail.com

; For Unix only.  You may supply arguments as well (default: "sendmail -t -i").
; http://php.net/sendmail-path
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"

; Force the addition of the specified parameters to be passed as extra parameters
; to the sendmail binary. These parameters will always replace the value of
; the 5th parameter to mail().
;mail.force_extra_parameters =

; Add X-PHP-Originating-Script: that will include uid of the script followed by the filename
mail.add_x_header = Off

; The path to a log file that will log all mail() calls. Log entries include
; the full path of the script, line number, To address and headers.
;mail.log =
; Log mail to syslog (Event Log on Windows).
;mail.log = syslog</div>
            <p><strong>⚠️ Important:</strong> Replace <code>your-email@gmail.com</code> with your actual Gmail address!</p>
        </div>

        <div class="step">
            <h3>Step 3: Configure sendmail.ini</h3>
            <p>Open <code>C:\xampp\sendmail\sendmail.ini</code> and replace its contents with:</p>
            <div class="code">[sendmail]

; you must change mail.mydomain.com to your smtp server,
; or to IIS's "pickup" directory.  (generally C:\Inetpub\mailroot\Pickup)
; emails delivered via IIS's pickup directory cause sendmail to
; run quicker, but you won't get error messages back to the calling
; application.

smtp_server=smtp.gmail.com

; smtp port (normally 25)
smtp_port=587

; SMTPS (SSL) support
;   auto = use SSL for port 465, otherwise try to use TLS
;   ssl  = alway use SSL
;   tls  = always use TLS
;   none = never try to use SSL
smtp_ssl=tls

; the default domain for this server will be read from the registry
; this will be appended to email addresses when one isn't provided
; if you want to override the value in the registry, uncomment and modify

default_domain=gmail.com

; log smtp errors to error.log (defaults to same directory as sendmail.exe)
; uncomment to enable logging
error_logfile=error.log

; create debug log as debug.log (defaults to same directory as sendmail.exe)
; uncomment to enable debugging
debug_logfile=debug.log

; if your smtp server requires authentication, modify the following two lines
auth_username=your-email@gmail.com
auth_password=your-16-character-app-password

; if your smtp server uses pop3 before smtp authentication, modify the 
; following three lines.  do not enable unless it is required.
pop3_server=
pop3_username=
pop3_password=

; force the sender to always be the following email address
; this will only affect the "MAIL FROM" command, it won't modify 
; the "From: " header of the email message.
force_sender=your-email@gmail.com

; force the sender to always be the following email address
; this will only affect the "RCTP TO" command, it won't modify 
; the "To: " header of the email message.
force_recipient=

; sendmail will use your hostname and your default_domain in the ehlo/helo
; smtp greeting.  you can manually set the ehlo/helo name if required
hostname=</div>
            <p><strong>⚠️ Critical:</strong> You need to:</p>
            <ul>
                <li>Replace <code>your-email@gmail.com</code> with your Gmail address</li>
                <li>Replace <code>your-16-character-app-password</code> with your Gmail App Password</li>
            </ul>
        </div>

        <div class="step">
            <h3>Step 4: Get Gmail App Password</h3>
            <p>You need a Gmail App Password (not your regular password):</p>
            <ol>
                <li>Go to <a href="https://myaccount.google.com/" target="_blank">Google Account Settings</a></li>
                <li>Click "Security" → "2-Step Verification" (enable if not already enabled)</li>
                <li>Click "App passwords"</li>
                <li>Select "Mail" and "Windows Computer"</li>
                <li>Copy the 16-character password (no spaces)</li>
            </ol>
        </div>

        <div class="step">
            <h3>Step 5: Restart Apache</h3>
            <p>After making these changes:</p>
            <ol>
                <li>Open XAMPP Control Panel</li>
                <li>Stop Apache</li>
                <li>Start Apache again</li>
            </ol>
        </div>

        <div class="step">
            <h3>Step 6: Test Email</h3>
            <p>After restarting Apache, test your email:</p>
            <a href="test_email.php" class="button">Test Email Configuration</a>
        </div>

        <h2>🚀 Quick Configuration Template</h2>
        <p>Copy and paste these configurations (remember to replace email addresses and password):</p>
        
        <h3>For php.ini [mail function] section:</h3>
        <div class="code">SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"</div>

        <h3>For sendmail.ini:</h3>
        <div class="code">smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=tls
default_domain=gmail.com
auth_username=your-email@gmail.com
auth_password=your-16-character-app-password
force_sender=your-email@gmail.com</div>

        <div class="warning">
            <strong>⚠️ Still Having Issues?</strong>
            <ul>
                <li>Make sure you're using an App Password, not your regular Gmail password</li>
                <li>Check that 2-Factor Authentication is enabled on Gmail</li>
                <li>Verify the file paths are correct</li>
                <li>Check XAMPP error logs in <code>C:\xampp\apache\logs\error.log</code></li>
            </ul>
        </div>

        <div class="success">
            <strong>✅ Alternative: Disable Email for Testing</strong>
            <p>If you want to test the appointment system without email, you can temporarily disable it by editing <code>api/process.php</code> and commenting out the email sending lines:</p>
            <div class="code">// send_email($emailData);  // Comment this line</div>
        </div>
    </div>
</body>
</html>