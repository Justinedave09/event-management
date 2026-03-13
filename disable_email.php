<?php
// Disable email for testing - modify mail.php to just log emails
$mail_file = 'library/mail.php';

if (file_exists($mail_file)) {
    $content = file_get_contents($mail_file);
    
    // Replace the send_email function with a logging version
    $new_send_email = 'function send_email($data) {
	$to 	= $data[\'to\'];
	$sub 	= $data[\'sub\'];
	$msg 	= $data[\'msg\'];
	
	// Log email instead of sending for testing
	$log_message = "\n" . date(\'Y-m-d H:i:s\') . " - EMAIL LOG (Testing Mode)\n";
	$log_message .= "To: $to\n";
	$log_message .= "Subject: $sub\n";
	$log_message .= "Message: " . strip_tags($msg) . "\n";
	$log_message .= "Status: Would be sent (Testing Mode)\n";
	$log_message .= "-------------------\n";
	
	file_put_contents(\'email_log.txt\', $log_message, FILE_APPEND | LOCK_EX);
	
	// Always return true for testing
	return true;
}';
    
    // Find and replace the send_email function
    $pattern = '/function send_email\([^}]+\}(?:\s*\/\*.*?\*\/)?/s';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $new_send_email, $content);
        
        if (file_put_contents($mail_file, $content)) {
            echo "Email disabled for testing. All emails will be logged to email_log.txt";
        } else {
            echo "Failed to modify mail.php - check file permissions";
        }
    } else {
        echo "Could not find send_email function to modify";
    }
} else {
    echo "mail.php file not found";
}
?>