<?php
// Note: mail.php functions are included separately to avoid circular dependency

function random_string($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return strtoupper($randomString);
}

/*
	Check if a session user id exist or not. If not set redirect
	to login page. If the user session id exist and there's found
	$_GET['logout'] in the query string logout the user
*/
function checkFDUser()
{
	// if the session id is not set, redirect to login page
	if (!isset($_SESSION['calendar_fd_user'])) {
		header('Location: ' . WEB_ROOT . 'login.php');
		exit;
	}
	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}

function doLogin()
{
	$name 	= $_POST['name'];
	$pwd 	= $_POST['pwd'];
	
	$errorMessage = '';
	
	//$sql 	= "SELECT * FROM tbl_frontdesk_users WHERE username = '$name' AND pwd = PASSWORD('$pwd')";
	$sql 	= "SELECT * FROM tbl_users WHERE name = '$name' AND pwd = '$pwd'";
	$result = dbQuery($sql);
	
	if (dbNumRows($result) == 1) {
		$row = dbFetchAssoc($result);
		$_SESSION['calendar_fd_user'] = $row;
		$_SESSION['calendar_fd_user_name'] = $row['username'];
		header('Location: index.php');
		exit();
	}
	else {
		$errorMessage = 'Invalid username / passsword. Please try again or contact to support.';
	}
	return $errorMessage;
}


/*
	Logout a user
*/
function doLogout()
{
	if (isset($_SESSION['calendar_fd_user'])) {
		unset($_SESSION['calendar_fd_user']);
		//session_unregister('hlbank_user');
	}
	header('Location: index.php');
	exit();
}

function getBookingRecords(){
	$per_page = 10;
	$page = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 1;
	$start 	= ($page-1)*$per_page;
	$sql 	= "SELECT u.id AS uid, u.name, u.phone, u.email,
			   a.id AS appointment_id, a.pet_name, a.pet_type, a.appointment_date, a.appointment_type, a.status, a.comments   
			   FROM tbl_users u, tbl_appointments a 
			   WHERE u.id = a.uid  
			   ORDER BY a.id DESC LIMIT $start, $per_page";
	//echo $sql;
	$result = dbQuery($sql);
	$records = array();
	while($row = dbFetchAssoc($result)) {
		extract($row);
		$records[] = array("user_id" => $uid,
							"appointment_id" => $appointment_id,
							"user_name" => $name,
							"user_phone" => $phone,
							"user_email" => $email,
							"pet_name" => $pet_name,
							"pet_type" => $pet_type,
							"appointment_date" => $appointment_date,
							"appointment_type" => $appointment_type,
							"status" => $status,
							"comments" => $comments);	
	}//while
	return $records;
}


function getUserRecords(){
	$per_page = 20;
	$page = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 1;
	$start 	= ($page-1)*$per_page;
	
	$type = $_SESSION['calendar_fd_user']['type'];
	if($type == 'client') {
		$id = $_SESSION['calendar_fd_user']['id'];
		$sql = "SELECT  * FROM tbl_users u WHERE type = 'client' AND id = $id ORDER BY u.id DESC";
	}
	else {
		// Show only pet owners (clients) in the user list
		$sql = "SELECT  * FROM tbl_users u WHERE type = 'client' ORDER BY u.id DESC LIMIT $start, $per_page";
	}
	
	//echo $sql;
	$result = dbQuery($sql);
	$records = array();
	while($row = dbFetchAssoc($result)) {
		extract($row);
		$records[] = array("user_id" => $id,
			"user_name" => $name,
			"user_phone" => $phone,
			"user_email" => $email,
			"type" => $type,
			"status" => $status,
			"bdate" => $bdate
		);	
	}
	return $records;
}

function getStaffRecords(){
	$per_page = 20;
	$page = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 1;
	$start 	= ($page-1)*$per_page;
	
	// Show only staff and admin users
	$sql = "SELECT  * FROM tbl_users u WHERE type IN ('staff', 'admin') ORDER BY u.id DESC LIMIT $start, $per_page";
	
	$result = dbQuery($sql);
	$records = array();
	while($row = dbFetchAssoc($result)) {
		extract($row);
		$records[] = array("user_id" => $id,
			"user_name" => $name,
			"user_phone" => $phone,
			"user_email" => $email,
			"type" => $type,
			"status" => $status,
			"bdate" => $bdate
		);	
	}
	return $records;
}

function getSystemSettings() {
	$sql = "SELECT setting_key, setting_value FROM tbl_system_settings";
	$result = dbQuery($sql);
	$settings = array();
	
	// Default settings in case table doesn't exist or is empty
	$defaults = array(
		'clinic_name' => 'Veterinary Clinic',
		'clinic_address' => '123 Main Street\nCity, State 12345',
		'clinic_phone' => '(555) 123-4567',
		'clinic_email' => 'info@vetclinic.com',
		'clinic_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed',
		'appointment_duration' => '30',
		'booking_advance_days' => '90',
		'email_notifications' => '1'
	);
	
	// If table exists and has data, use it
	if ($result && dbNumRows($result) > 0) {
		while($row = dbFetchAssoc($result)) {
			$settings[$row['setting_key']] = $row['setting_value'];
		}
		// Merge with defaults for any missing settings
		$settings = array_merge($defaults, $settings);
	} else {
		// Use defaults if table doesn't exist
		$settings = $defaults;
	}
	
	return $settings;
}

function updateSystemSettings($settingsData) {
	$success = true;
	
	foreach ($settingsData as $key => $value) {
		// Escape the value for SQL
		$value = addslashes($value);
		$key = addslashes($key);
		
		// Check if setting exists
		$checkSql = "SELECT id FROM tbl_system_settings WHERE setting_key = '$key'";
		$checkResult = dbQuery($checkSql);
		
		if ($checkResult && dbNumRows($checkResult) > 0) {
			// Update existing setting
			$sql = "UPDATE tbl_system_settings SET setting_value = '$value', updated_date = NOW() WHERE setting_key = '$key'";
		} else {
			// Insert new setting
			$sql = "INSERT INTO tbl_system_settings (setting_key, setting_value, updated_date) VALUES ('$key', '$value', NOW())";
		}
		
		if (!dbQuery($sql)) {
			$success = false;
		}
	}
	
	return $success;
}

function getSystemSetting($key, $default = '') {
	$key = addslashes($key);
	$sql = "SELECT setting_value FROM tbl_system_settings WHERE setting_key = '$key'";
	$result = dbQuery($sql);
	
	if ($result && dbNumRows($result) > 0) {
		$row = dbFetchAssoc($result);
		return $row['setting_value'];
	}
	
	return $default;
}

function getHolidayRecords() {
	$per_page = 10;
	$page 	= (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 1;
	$start 	= ($page-1)*$per_page;
	$sql 	= "SELECT * FROM tbl_holidays ORDER BY id DESC LIMIT $start, $per_page";
	//echo $sql;
	$result = dbQuery($sql);
	$records = array();
	while($row = dbFetchAssoc($result)) {
		extract($row);
		$records[] = array("hid" => $id, "hdate" => $date,"hreason" => $reason);	
	}//while
	return $records;
}

function generateHolidayPagination() {
	$per_page = 10;
	$sql 	= "SELECT * FROM tbl_holidays";
	$result = dbQuery($sql);
	$count 	= dbNumRows($result);
	$pages 	= ceil($count/$per_page);
	$pageno = '<ul class="pagination pagination-sm no-margin pull-right">';
	for($i=1; $i<=$pages; $i++)	{
		$pageno .= "<li><a href=\"?v=HOLY&page=$i\">".$i."</a></li>";
	}
	$pageno .= 	"</ul>";
	return $pageno;
}

function generatePagination(){
	$per_page = 10;
	$sql 	= "SELECT * FROM tbl_users";
	$result = dbQuery($sql);
	$count 	= dbNumRows($result);
	$pages 	= ceil($count/$per_page);
	$pageno = '<ul class="pagination pagination-sm no-margin pull-right">';
	for($i=1; $i<=$pages; $i++)	{
	//<li><a href="#">1</a></li>
		//$pageno .= "<a href=\"?v=USER&page=$i\"><li id=\".$i.\">".$i."</li></a> ";
		$pageno .= "<li><a href=\"?v=USER&page=$i\">".$i."</a></li>";
	}
	$pageno .= 	"</ul>";
	return $pageno;
}

?>