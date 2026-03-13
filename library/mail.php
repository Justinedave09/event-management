<?php

require_once 'config.php';
require_once 'database.php';
require_once 'email_config.php';
require_once 'functions.php'; // Include functions.php to use getSystemSetting

function send_email($data) {
	$to 	= $data['to'];
	$sub 	= $data['sub'];
	$msg 	= $data['msg'];
	
	// Get clinic name from settings
	$clinic_name = getSystemSetting('clinic_name', 'Veterinary Clinic');
	$clinic_email = getSystemSetting('clinic_email', EMAIL_FROM_ADDRESS);
	
	// Enhanced headers for better email delivery
	$headers = "From: " . $clinic_name . " <" . $clinic_email . ">\r\n";
	$headers .= "Reply-To: " . $clinic_email . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
	
	// Try to send email
	$retval = mail($to, $sub, $msg, $headers);
	
	// Enhanced logging for debugging
	$log_message = "\n" . date('Y-m-d H:i:s') . " - EMAIL ATTEMPT\n";
	$log_message .= "To: $to\n";
	$log_message .= "Subject: $sub\n";
	$log_message .= "PHP mail() returned: " . ($retval ? 'TRUE' : 'FALSE') . "\n";
	$log_message .= "Current SMTP: " . ini_get('SMTP') . "\n";
	$log_message .= "Current SMTP Port: " . ini_get('smtp_port') . "\n";
	$log_message .= "Sendmail From: " . ini_get('sendmail_from') . "\n";
	$log_message .= "Sendmail Path: " . ini_get('sendmail_path') . "\n";
	
	// Check for errors
	$error = error_get_last();
	if ($error && strpos($error['message'], 'mail') !== false) {
		$log_message .= "PHP Error: " . $error['message'] . "\n";
	}
	
	$log_message .= "-------------------\n";
	
	file_put_contents('email_debug.txt', $log_message, FILE_APPEND | LOCK_EX);
	
	return $retval;
}

// Alternative email function using SMTP (if needed)
function send_email_smtp($data) {
	// This is a placeholder for SMTP implementation
	// You can implement PHPMailer here if needed
	return send_email($data);
}

// Test email function
function test_email($email_address) {
	$test_data = array(
		'to' => $email_address,
		'sub' => 'Test Email from Veterinary Clinic',
		'msg' => '<html><body style="font-family: Arial, sans-serif;">
					<h2 style="color: #2c5aa0;">Test Email</h2>
					<p>If you receive this email, your email configuration is working correctly!</p>
					<div style="background-color: #f0f8ff; padding: 15px; border-left: 4px solid #2c5aa0; margin: 20px 0;">
						<strong>Configuration Status:</strong><br/>
						Method: ' . (USE_SMTP ? 'SMTP' : 'PHP Mail') . '<br/>
						Time: ' . date('Y-m-d H:i:s') . '
					</div>
					<p>Best regards,<br/>
					<strong>Veterinary Clinic Team</strong></p>
				</body></html>'
	);
	
	return send_email($test_data);
}

function get_email_msg($data) {
	$msg_text = "";
	$clinic_name = getSystemSetting('clinic_name', 'Veterinary Clinic');
	
	switch($data['msg']) {
		
		case 'appointment_booked':
			$msg_text = sprintf("
			<html>
			<body style='font-family: Arial, sans-serif;'>
				<h2 style='color: #2c5aa0;'>Appointment Confirmation</h2>
				<p>Dear %s,</p>
				<p>Your veterinary appointment has been successfully booked at %s.</p>
				<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #2c5aa0; margin: 20px 0;'>
					<strong>Appointment Details:</strong><br/>
					Pet Name: %s<br/>
					Pet Type: %s<br/>
					Appointment Date: %s<br/>
					Appointment Type: %s<br/>
					Status: <span style='color: #ff9800;'>Pending Confirmation</span>
				</div>
				<p>You will receive another email once your appointment is confirmed by our staff.</p>
				<p>Best regards,<br/>
				<strong>%s Team</strong></p>
			</body>
			</html>", 
				$data['name'], $clinic_name, $data['pet_name'], $data['pet_type'], $data['appointment_date'], $data['appointment_type'], $clinic_name);
		break;
		
		case 'appointment_confirmed':
			$msg_text = sprintf("
			<html>
			<body style='font-family: Arial, sans-serif;'>
				<h2 style='color: #4caf50;'>Appointment Confirmed!</h2>
				<p>Dear %s,</p>
				<p>Great news! Your veterinary appointment at %s has been <strong style='color: #4caf50;'>CONFIRMED</strong>.</p>
				<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4caf50; margin: 20px 0;'>
					<strong>Confirmed Appointment Details:</strong><br/>
					Pet Name: %s<br/>
					Appointment Date: %s<br/>
					Appointment Type: %s
				</div>
				<div style='background-color: #e8f5e8; padding: 10px; border-radius: 5px; margin: 20px 0;'>
					<strong>Important Reminders:</strong>
					<ul>
						<li>Please arrive 10 minutes early</li>
						<li>Bring your pet's vaccination records</li>
						<li>If you need to reschedule, please contact us as soon as possible</li>
					</ul>
				</div>
				<p>We look forward to seeing you and your pet!</p>
				<p>Best regards,<br/>
				<strong>%s Team</strong></p>
			</body>
			</html>", 
				$data['name'], $clinic_name, $data['pet_name'], $data['appointment_date'], $data['appointment_type'], $clinic_name);
		break;
		
		case 'appointment_denied':
			$msg_text = sprintf("
			<html>
			<body style='font-family: Arial, sans-serif;'>
				<h2 style='color: #f44336;'>Appointment Update</h2>
				<p>Dear %s,</p>
				<p>Unfortunately, your appointment request for %s has been declined.</p>
				<div style='background-color: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0;'>
					<strong>Reason:</strong> %s
				</div>
				<p>Please contact us to schedule an alternative appointment time. We apologize for any inconvenience.</p>
				<p>Best regards,<br/>
				<strong>%s Team</strong></p>
			</body>
			</html>", 
				$data['name'], $data['appointment_date'], $data['reason'], $clinic_name);
		break;
		
		case 'appointment_reminder':
			$msg_text = sprintf("
			<html>
			<body style='font-family: Arial, sans-serif;'>
				<h2 style='color: #ff9800;'>🔔 Appointment Reminder</h2>
				<p>Dear %s,</p>
				<p>This is a friendly reminder about your upcoming veterinary appointment at %s.</p>
				<div style='background-color: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 20px 0;'>
					<strong>Appointment Details:</strong><br/>
					Pet Name: %s<br/>
					Pet Type: %s<br/>
					Appointment Date: %s<br/>
					Appointment Type: %s<br/>
					Status: <span style='color: #4caf50;'>Confirmed</span>
				</div>
				<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;'>
					<strong>📋 Please Remember:</strong>
					<ul style='margin: 10px 0; padding-left: 20px;'>
						<li>Arrive 10-15 minutes early for check-in</li>
						<li>Bring your pet's vaccination records and medical history</li>
						<li>Bring a list of any medications your pet is currently taking</li>
						<li>If your pet is anxious, consider bringing their favorite toy or blanket</li>
						<li>For fasting procedures, follow the pre-appointment instructions</li>
					</ul>
				</div>
				<div style='background-color: #f1f8e9; padding: 10px; border-radius: 5px; margin: 20px 0;'>
					<strong>📞 Need to reschedule?</strong><br/>
					Please contact us as soon as possible if you need to change your appointment time.
				</div>
				<p>We look forward to seeing you and %s soon!</p>
				<p>Best regards,<br/>
				<strong>%s Team</strong></p>
			</body>
			</html>", 
				$data['name'], $clinic_name, $data['pet_name'], $data['pet_type'], $data['appointment_date'], $data['appointment_type'], $data['pet_name'], $clinic_name);
		break;
		
		case 'register':
			$msg_text = $data['body'];
		break;
		
		case 'otp':
			$msg_text = sprintf("Your one time Transaction Authorization Code : %u", $data['token']);
		break;
		
		case 'change_pwd':
			$msg_text = sprintf("Your password is successfully changed. New Password is : %s", $data['pwd']);
		break;
		
		case 'change_pin':
			$msg_text = sprintf("Your PIN is successfully changed. New PIN is : %u", $data['pin']);
		break;		
		
		case 'transfer':
			$msg_text = $data['body'];
		break;
		
	}//switch
	return $msg_text;
}

?>