<?php 

require_once '../library/config.php';
require_once '../library/functions.php';
require_once '../library/mail.php';

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

switch($cmd) {
	
	case 'create':
		createUser();
	break;
	
	case 'createstaff':
		createStaff();
	break;
	
	case 'change':
		changeStatus();
	break;
	
	case 'updatesettings':
		updateSettings();
	break;
	
	case 'updateUser':
		updateUser();
	break;
	
	case 'updateStaff':
		updateStaff();
	break;
	
	default :
	break;
}

function createUser() {
	$name 		= $_POST['name'];
	$address 	= $_POST['address'];
	$phone 		= $_POST['phone'];
	$email 		= $_POST['email'];
	$type		= isset($_POST['type']) ? $_POST['type'] : 'client'; // Default to client for pet owners
	
	// Check if user with same name already exists
	$hsql	= "SELECT * FROM tbl_users WHERE name = '$name'";
	$hresult = dbQuery($hsql);
	if (dbNumRows($hresult) > 0) {
		$errorMessage = 'User with same name already exists. Please try a different name.';
		header('Location: ../views/?v=CREATE&err=' . urlencode($errorMessage));
		exit();
	}
	
	// Generate random password for pet owners
	$pwd = random_string();
	$sql = "INSERT INTO tbl_users (name, pwd, address, phone, email, type, status, bdate)
			VALUES ('$name', '$pwd', '$address', '$phone', '$email', '$type', 'active', NOW())";	
	dbQuery($sql);
	
	// Send welcome email to pet owner
	$emailMsg = get_email_msg(array(
		'msg' => 'register',
		'body' => "Dear $name,<br/><br/>Welcome to our Veterinary Clinic!<br/><br/>Your account has been created successfully.<br/>Username: $name<br/>Temporary Password: $pwd<br/><br/>Please change your password after first login.<br/><br/>Best regards,<br/>Veterinary Clinic Team"
	));
	
	$emailData = array(
		'to' => $email, 
		'sub' => 'Welcome to Veterinary Clinic', 
		'msg' => $emailMsg
	);
	send_email($emailData);
	
	header('Location: ../views/?v=USERS&msg=' . urlencode('Pet owner successfully registered. Welcome email sent.'));
	exit();
}

function createStaff() {
	// Check if current user is admin
	if ($_SESSION['calendar_fd_user']['type'] !== 'admin') {
		header('Location: ../views/?v=USERS&err=' . urlencode('Access denied. Only administrators can create staff accounts.'));
		exit();
	}
	
	$name 		= $_POST['name'];
	$address 	= $_POST['address'];
	$phone 		= $_POST['phone'];
	$email 		= $_POST['email'];
	$password	= $_POST['pwd'];
	$type		= $_POST['type'];
	
	// Validate staff type
	if (!in_array($type, ['staff', 'admin'])) {
		header('Location: ../views/?v=STAFF&err=' . urlencode('Invalid staff type selected.'));
		exit();
	}
	
	// Check if user with same name already exists
	$hsql	= "SELECT * FROM tbl_users WHERE name = '$name'";
	$hresult = dbQuery($hsql);
	if (dbNumRows($hresult) > 0) {
		$errorMessage = 'User with same name already exists. Please try a different name.';
		header('Location: ../views/?v=STAFF&err=' . urlencode($errorMessage));
		exit();
	}
	
	// Check if email already exists
	$emailSql = "SELECT * FROM tbl_users WHERE email = '$email'";
	$emailResult = dbQuery($emailSql);
	if (dbNumRows($emailResult) > 0) {
		$errorMessage = 'User with same email already exists. Please try a different email.';
		header('Location: ../views/?v=STAFF&err=' . urlencode($errorMessage));
		exit();
	}
	
	$sql = "INSERT INTO tbl_users (name, pwd, address, phone, email, type, status, bdate)
			VALUES ('$name', '$password', '$address', '$phone', '$email', '$type', 'active', NOW())";	
	dbQuery($sql);
	
	// Send welcome email to staff
	$roleTitle = ($type === 'admin') ? 'Administrator' : 'Veterinary Staff';
	$emailMsg = get_email_msg(array(
		'msg' => 'register',
		'body' => "Dear $name,<br/><br/>Welcome to our Veterinary Clinic team!<br/><br/>Your $roleTitle account has been created successfully.<br/>Username: $name<br/>Password: $password<br/><br/>You can now log in to the system and start managing appointments.<br/><br/>Best regards,<br/>Veterinary Clinic Administration"
	));
	
	$emailData = array(
		'to' => $email, 
		'sub' => 'Welcome to Veterinary Clinic Team', 
		'msg' => $emailMsg
	);
	send_email($emailData);
	
	header('Location: ../views/?v=USERS&msg=' . urlencode("$roleTitle account created successfully. Welcome email sent."));
	exit();
}

//http://localhost/houda/views/process.php?cmd=change&action=inactive&userId=1
function changeStatus() {
	$action 	= $_GET['action'];
	$userId 	= (int)$_GET['userId'];
	
	
	$sql = "UPDATE tbl_users SET status = '$action' WHERE id = $userId";	
	dbQuery($sql);
	
	//send email on registration confirmation
	$bodymsg = "User $name booked the date slot on $bkdate. Requesting you to please take further action on user booking.<br/>Mbr/>Tousif Khan";
	$data = array('to' => '$email', 'sub' => 'Booking on $rdate.', 'msg' => $bodymsg);
	//send_email($data);
	header('Location: ../views/?v=USERS&msg=' . urlencode('User status successfully updated.'));
	exit();
}

function updateSettings() {
	// Check if current user is admin
	if ($_SESSION['calendar_fd_user']['type'] !== 'admin') {
		header('Location: ../views/?v=SETTINGS&err=' . urlencode('Access denied. Only administrators can update settings.'));
		exit();
	}
	
	$settingsData = array(
		'clinic_name' => $_POST['clinic_name'],
		'clinic_address' => $_POST['clinic_address'],
		'clinic_phone' => $_POST['clinic_phone'],
		'clinic_email' => $_POST['clinic_email'],
		'clinic_hours' => $_POST['clinic_hours'],
		'appointment_duration' => $_POST['appointment_duration'],
		'booking_advance_days' => $_POST['booking_advance_days'],
		'email_notifications' => isset($_POST['email_notifications']) ? '1' : '0'
	);
	
	if (updateSystemSettings($settingsData)) {
		header('Location: ../views/?v=SETTINGS&msg=' . urlencode('System settings updated successfully.'));
	} else {
		header('Location: ../views/?v=SETTINGS&err=' . urlencode('Failed to update system settings. Please try again.'));
	}
	exit();
}

function updateUser() {
	$userId = (int)$_POST['userId'];
	$name = $_POST['name'];
	$address = $_POST['address'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$status = $_POST['status'];
	$type = $_POST['type'];
	$pwd = isset($_POST['pwd']) && $_POST['pwd'] != '' ? $_POST['pwd'] : null;
	
	// Check if current user can edit this user
	$current_user_type = $_SESSION['calendar_fd_user']['type'];
	if ($current_user_type !== 'admin' && $current_user_type !== 'staff') {
		header('Location: ../views/?v=USERS&err=' . urlencode('Access denied'));
		exit();
	}
	
	// Check if name already exists for other users
	$nameSql = "SELECT * FROM tbl_users WHERE name = '$name' AND id != $userId";
	$nameResult = dbQuery($nameSql);
	if (dbNumRows($nameResult) > 0) {
		header('Location: ../views/?v=USEREDIT&ID=' . $userId . '&err=' . urlencode('User with same name already exists.'));
		exit();
	}
	
	// Check if email already exists for other users
	$emailSql = "SELECT * FROM tbl_users WHERE email = '$email' AND id != $userId";
	$emailResult = dbQuery($emailSql);
	if (dbNumRows($emailResult) > 0) {
		header('Location: ../views/?v=USEREDIT&ID=' . $userId . '&err=' . urlencode('User with same email already exists.'));
		exit();
	}
	
	// Build update query
	$updateFields = array(
		"name = '$name'",
		"address = '$address'",
		"phone = '$phone'",
		"email = '$email'",
		"status = '$status'",
		"type = '$type'"
	);
	
	// Add password to update if provided
	if ($pwd !== null) {
		$updateFields[] = "pwd = '$pwd'";
	}
	
	$sql = "UPDATE tbl_users SET " . implode(', ', $updateFields) . " WHERE id = $userId";
	dbQuery($sql);
	
	// Send email notification if password was changed
	if ($pwd !== null) {
		$emailMsg = get_email_msg(array(
			'msg' => 'change_pwd',
			'pwd' => $pwd
		));
		
		$emailData = array(
			'to' => $email, 
			'sub' => 'Password Updated - Veterinary Clinic', 
			'msg' => $emailMsg
		);
		send_email($emailData);
	}
	
	header('Location: ../views/?v=USERS&msg=' . urlencode('User successfully updated.'));
	exit();
}

function updateStaff() {
	// Check if current user is admin
	if ($_SESSION['calendar_fd_user']['type'] !== 'admin') {
		header('Location: ../views/?v=STAFF&err=' . urlencode('Access denied. Only administrators can edit staff accounts.'));
		exit();
	}
	
	$userId = (int)$_POST['userId'];
	$name = $_POST['name'];
	$address = $_POST['address'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$status = $_POST['status'];
	$type = $_POST['type'];
	$pwd = isset($_POST['pwd']) && $_POST['pwd'] != '' ? $_POST['pwd'] : null;
	
	// Validate staff type
	if (!in_array($type, ['staff', 'admin'])) {
		header('Location: ../views/?v=STAFFEDIT&ID=' . $userId . '&err=' . urlencode('Invalid staff type selected.'));
		exit();
	}
	
	// Check if name already exists for other users
	$nameSql = "SELECT * FROM tbl_users WHERE name = '$name' AND id != $userId";
	$nameResult = dbQuery($nameSql);
	if (dbNumRows($nameResult) > 0) {
		header('Location: ../views/?v=STAFFEDIT&ID=' . $userId . '&err=' . urlencode('User with same name already exists.'));
		exit();
	}
	
	// Check if email already exists for other users
	$emailSql = "SELECT * FROM tbl_users WHERE email = '$email' AND id != $userId";
	$emailResult = dbQuery($emailSql);
	if (dbNumRows($emailResult) > 0) {
		header('Location: ../views/?v=STAFFEDIT&ID=' . $userId . '&err=' . urlencode('User with same email already exists.'));
		exit();
	}
	
	// Build update query
	$updateFields = array(
		"name = '$name'",
		"address = '$address'",
		"phone = '$phone'",
		"email = '$email'",
		"status = '$status'",
		"type = '$type'"
	);
	
	// Add password to update if provided
	if ($pwd !== null) {
		$updateFields[] = "pwd = '$pwd'";
	}
	
	$sql = "UPDATE tbl_users SET " . implode(', ', $updateFields) . " WHERE id = $userId";
	dbQuery($sql);
	
	// Send email notification if password was changed
	if ($pwd !== null) {
		$roleTitle = ($type === 'admin') ? 'Administrator' : 'Veterinary Staff';
		$emailMsg = get_email_msg(array(
			'msg' => 'register',
			'body' => "Dear $name,<br/><br/>Your $roleTitle account has been updated.<br/><br/>New Password: $pwd<br/><br/>Please keep this information secure.<br/><br/>Best regards,<br/>Veterinary Clinic Administration"
		));
		
		$emailData = array(
			'to' => $email, 
			'sub' => 'Account Updated - Veterinary Clinic', 
			'msg' => $emailMsg
		);
		send_email($emailData);
	}
	
	header('Location: ../views/?v=STAFF&msg=' . urlencode('Staff member successfully updated.'));
	exit();
}
?>