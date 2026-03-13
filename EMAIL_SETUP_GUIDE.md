# Email Setup Guide for Veterinary Appointment System

## Quick Test

1. **Test your email configuration**: Visit `http://localhost/your-project/test_email.php`
2. Enter your email address and click "Send Test Email"
3. Check your inbox (and spam folder) for the test email

## Configuration Options

### Option 1: XAMPP with Gmail (Recommended for Development)

#### Step 1: Enable 2-Factor Authentication on Gmail
1. Go to your Google Account settings
2. Enable 2-Factor Authentication
3. Generate an "App Password" for this application

#### Step 2: Configure php.ini
Edit `C:\xampp\php\php.ini`:
```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

#### Step 3: Configure sendmail.ini
Edit `C:\xampp\sendmail\sendmail.ini`:
```ini
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=your-email@gmail.com
auth_password=your-16-character-app-password
force_sender=your-email@gmail.com
```

#### Step 4: Update Email Configuration
Edit `library/email_config.php`:
```php
define('EMAIL_FROM_ADDRESS', 'your-email@gmail.com');
define('EMAIL_FROM_NAME', 'Veterinary Clinic');
define('EMAIL_REPLY_TO', 'your-email@gmail.com');
```

#### Step 5: Restart Apache
Restart Apache in XAMPP Control Panel

### Option 2: WAMP Configuration

Similar to XAMPP but paths are different:
- php.ini: `C:\wamp64\bin\apache\apache2.x.x\bin\php.ini`
- sendmail: Usually needs to be downloaded separately

### Option 3: Production Server

For production servers, you have several options:

#### A. Use Server's Built-in Mail
Most hosting providers have mail configured. Just ensure:
```php
// In library/email_config.php
define('EMAIL_FROM_ADDRESS', 'appointments@yourdomain.com');
```

#### B. Use SMTP Service (Recommended)
Services like SendGrid, Mailgun, or Amazon SES:

1. Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

2. Update `library/email_config.php`:
```php
define('USE_SMTP', true);
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'apikey');
define('SMTP_PASSWORD', 'your-sendgrid-api-key');
```

## Troubleshooting

### Common Issues

1. **"mail() function not working"**
   - Check if PHP mail is enabled: `php -m | grep mail`
   - Verify SMTP settings in php.ini

2. **"Authentication failed"**
   - Use App Password for Gmail (not regular password)
   - Check username/password in sendmail.ini

3. **"Connection refused"**
   - Check SMTP server and port
   - Verify firewall settings

4. **Emails go to spam**
   - Add proper headers (already included in updated mail.php)
   - Use a real domain for sender address

### Debug Steps

1. **Check PHP Configuration**:
   ```php
   <?php
   echo "SMTP: " . ini_get('SMTP') . "<br>";
   echo "SMTP Port: " . ini_get('smtp_port') . "<br>";
   echo "Sendmail From: " . ini_get('sendmail_from') . "<br>";
   ?>
   ```

2. **Check Error Logs**:
   - PHP error log: `C:\xampp\php\logs\php_error_log`
   - Apache error log: `C:\xampp\apache\logs\error.log`
   - Custom email log: `email_log.txt` (created automatically)

3. **Test with Simple Script**:
   ```php
   <?php
   $to = "test@example.com";
   $subject = "Test";
   $message = "Test message";
   $headers = "From: test@yourdomain.com";
   
   if (mail($to, $subject, $message, $headers)) {
       echo "Email sent successfully";
   } else {
       echo "Email failed";
   }
   ?>
   ```

## Alternative Solutions

### 1. Disable Email Temporarily
If you want to test the system without email:

Edit `api/process.php` and comment out email sending:
```php
// send_email($emailData);  // Comment this line
```

### 2. Use Local Email Testing
Tools like MailHog or Mailtrap for development:
- MailHog: Catches emails locally
- Mailtrap: Online email testing service

### 3. Log Emails Instead
Modify `send_email()` function to log instead of send:
```php
function send_email($data) {
    $log = "Email would be sent to: " . $data['to'] . "\n";
    $log .= "Subject: " . $data['sub'] . "\n";
    $log .= "Message: " . $data['msg'] . "\n\n";
    file_put_contents('email_log.txt', $log, FILE_APPEND);
    return true; // Always return true for testing
}
```

## Testing Checklist

- [ ] Test email configuration with `test_email.php`
- [ ] Book a test appointment and check for booking confirmation email
- [ ] Approve/deny appointment and check for status update email
- [ ] Check spam folder if emails don't appear in inbox
- [ ] Verify email formatting looks correct
- [ ] Test with different email providers (Gmail, Yahoo, Outlook)

## Support

If you're still having issues:
1. Check the `email_log.txt` file for detailed logs
2. Review PHP error logs
3. Try the simple mail test script above
4. Consider using a third-party email service for production

Remember: Email configuration can be tricky, especially on Windows development environments. The test page will help you identify and fix configuration issues step by step.