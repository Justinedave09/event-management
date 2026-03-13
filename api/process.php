<?php 

require_once 'Booking.php';
require_once '../library/config.php';
require_once '../library/functions.php';
require_once '../library/mail.php';

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

switch($cmd) {
	
	case 'book':
		bookCalendar();
	break;
		
	case 'holiday':
		addHoliday();
	break;
	
	case 'hdelete':
		deleteHoliday();
	break;
		
	case 'calview':
		calendarView();
	break;

	case 'regConfirm':
		regConfirm();
	break;
			
	case 'delete':
		regDelete();
	break;
	
	case 'user':
		userDetails();
	break;
	
	case 'sendReminder':
		sendAppointmentReminder();
	break;
	
	case 'updateAppointment':
		updateAppointment();
	break;
	
	default :
	break;
}

function addHoliday() {
	$date 		= $_POST['date'];
	$reason 	= $_POST['reason'];	
	
	$errorMessage = '';
	
	$sql 	= "SELECT * FROM tbl_holidays WHERE date = '$date'";
	$result = dbQuery($sql);
	
	if (dbNumRows($result) > 0) {
		$errorMessage = 'Clinic is already closed on this date.';
		header('Location: ../views/?v=HOLY&err=' . urlencode($errorMessage));
		exit();
	}
	else {
		$sql = "INSERT INTO tbl_holidays (date, reason, bdate)
				VALUES ('$date', '$reason', NOW())";	
		dbQuery($sql);
		$msg = 'Clinic closed day successfully added to calendar.';
		header('Location: ../views/?v=HOLY&msg=' . urlencode($msg));
		exit();
	}
}

function bookCalendar() {
	$userId		= (int)$_POST['name']; // The select dropdown sends the user ID
	$address 	= $_POST['address'];
	$phone 		= $_POST['phone'];
	$email 		= $_POST['email'];
	$rdate		= $_POST['rdate'];
	$rtime		= $_POST['rtime'];
	$bkdate		= $rdate. ' '. $rtime;
	$pet_name	= $_POST['pet_name'];
	$pet_type	= $_POST['pet_type'];
	$pet_breed	= isset($_POST['pet_breed']) ? $_POST['pet_breed'] : '';
	$appointment_type = isset($_POST['appointment_type']) ? $_POST['appointment_type'] : 'General Checkup';
	
	// Get user name for email
	$userSql = "SELECT name FROM tbl_users WHERE id = $userId";
	$userResult = dbQuery($userSql);
	$name = '';
	if (dbNumRows($userResult) > 0) {
		$userRow = dbFetchAssoc($userResult);
		$name = $userRow['name'];
	}
	
	//Check if that date has a holiday
	$hsql	= "SELECT * FROM tbl_holidays WHERE date = '$rdate'";
	$hresult = dbQuery($hsql);
	if (dbNumRows($hresult) > 0) {
		$errorMessage = 'The clinic is closed on this date. Please select another day.';
		header('Location: ../views/?v=DB&err=' . urlencode($errorMessage));
		exit();
	}
	
	$sql = "INSERT INTO tbl_appointments (uid, pet_name, pet_type, pet_breed, appointment_date, appointment_type, status, comments, bdate) 
			VALUES ($userId, '$pet_name', '$pet_type', '$pet_breed', '$bkdate', '$appointment_type', 'PENDING', '', NOW())";
	dbQuery($sql);
	
	//Send email confirmation to user
	$emailMsg = get_email_msg(array(
		'msg' => 'appointment_booked',
		'name' => $name,
		'pet_name' => $pet_name,
		'pet_type' => $pet_type,
		'appointment_date' => $bkdate,
		'appointment_type' => $appointment_type
	));
	
	$emailData = array(
		'to' => $email, 
		'sub' => 'Veterinary Appointment Confirmation - Pending', 
		'msg' => $emailMsg
	);
	send_email($emailData);
	
	header('Location: ../index.php?msg=' . urlencode('Appointment successfully booked. Check your email for confirmation.'));
	exit();
}

function regConfirm() {
	$userId		= $_GET['userId'];
	$action 	= $_GET['action'];
	$stat		= ($action == 'approve') ? 'APPROVED' : 'DENIED';
	
	$sql		= "UPDATE tbl_appointments SET status = '$stat' WHERE uid = $userId";
	dbQuery($sql);
	
	//Get user and appointment details for email
	$userSql = "SELECT u.name, u.email, a.pet_name, a.appointment_date, a.appointment_type 
				FROM tbl_users u, tbl_appointments a 
				WHERE u.id = a.uid AND u.id = $userId 
				LIMIT 1";
	$userResult = dbQuery($userSql);
	
	if (dbNumRows($userResult) > 0) {
		$userData = dbFetchAssoc($userResult);
		
		if ($stat == 'APPROVED') {
			$emailMsg = get_email_msg(array(
				'msg' => 'appointment_confirmed',
				'name' => $userData['name'],
				'pet_name' => $userData['pet_name'],
				'appointment_date' => $userData['appointment_date'],
				'appointment_type' => $userData['appointment_type']
			));
			$subject = 'Veterinary Appointment CONFIRMED';
		} else {
			$emailMsg = get_email_msg(array(
				'msg' => 'appointment_denied',
				'name' => $userData['name'],
				'appointment_date' => $userData['appointment_date'],
				'reason' => 'Time slot no longer available'
			));
			$subject = 'Veterinary Appointment Update';
		}
		
		$emailData = array(
			'to' => $userData['email'], 
			'sub' => $subject, 
			'msg' => $emailMsg
		);
		send_email($emailData);
	}
	
	header('Location: ../views/?v=DB&msg=' . urlencode('Appointment status successfully changed and email sent to client.'));
	exit();
}

function regDelete() {
	$userId	= $_GET['userId'];
	$sql1	= "DELETE FROM tbl_appointments WHERE uid = $userId";
	dbQuery($sql1);
	$sql2	= "DELETE FROM tbl_users WHERE id = $userId";
	dbQuery($sql2);
	
	header('Location: ../views/?v=LIST&msg=' . urlencode('Appointment record successfully deleted.'));
	exit();
}

function deleteHoliday() {
	$holyId	= $_GET['hId'];
	$dsql	= "DELETE FROM tbl_holidays WHERE id = $holyId";
	dbQuery($dsql);
	header('Location: ../views/?v=HOLY&msg=' . urlencode('Clinic closed day successfully removed.'));
	exit();
}

function calendarView() {
	$start 	= $_POST['start'];
	$end 	= $_POST['end'];
	$bookings = array();
	$sql	= "SELECT u.name AS u_name, u.id AS user_id, a.appointment_date, a.status, a.pet_name, a.appointment_type 
			   FROM tbl_users u, tbl_appointments a 
			   WHERE u.id = a.uid  
			   AND (a.appointment_date BETWEEN '$start' AND '$end')";
	$result = dbQuery($sql);
	while($row = dbFetchAssoc($result)) {
		extract($row);
		$book = new Booking();
		$book->title = $u_name . ' - ' . $pet_name . ' (' . $appointment_type . ')';
		$book->start = $appointment_date; 
		$bgClr = '#f39c12';//pending
		if($status == 'DENIED') {$bgClr = '#ff0000';}
		else if($status == 'APPROVED') {$bgClr = '#00cc00';}
		$book->backgroundColor = $bgClr;
		$book->borderColor = $bgClr;
		$book->url = WEB_ROOT . 'views/?v=USER&ID='.$user_id;
		$bookings[] = $book; 
	}
	//Get clinic closed days
	$hsql	= "SELECT * FROM tbl_holidays 
			   WHERE (date BETWEEN '$start' AND '$end')";
	$hresult = dbQuery($hsql);
	while($hrow = dbFetchAssoc($hresult)) {	
		extract($hrow);	   
		$b = new Booking();
		$b->block = true;
		$b->title = $reason;
		$b->start = $date;
		$b->allDay = true; 
		$b->borderColor = '#F0F0F0';
		$b->className = 'fc-disabled';
		$bookings[] = $b;
	}
	echo json_encode($bookings);
}

function userDetails() {
	// Clean any output buffer to ensure clean JSON
	if (ob_get_level()) {
		ob_clean();
	}
	
	$userId	= (int)$_GET['userId'];
	
	// Validate user ID
	if ($userId <= 0) {
		header('Content-Type: application/json');
		echo json_encode(array('error' => 'Invalid user ID'));
		exit();
	}
	
	$hsql	= "SELECT * FROM tbl_users WHERE id = $userId AND type = 'client'";
	$hresult = dbQuery($hsql);
	$user = array();
	
	if ($hresult && dbNumRows($hresult) > 0) {
		$hrow = dbFetchAssoc($hresult);
		$user['user_id'] = $hrow['id'];
		$user['address'] = $hrow['address'] ? $hrow['address'] : '';
		$user['phone_no'] = $hrow['phone'] ? $hrow['phone'] : '';
		$user['email'] = $hrow['email'] ? $hrow['email'] : '';
	} else {
		$user['error'] = 'User not found';
	}
	
	// Set proper content type and output clean JSON
	header('Content-Type: application/json');
	echo json_encode($user);
	exit();
}

function sendAppointmentReminder() {
	$userId = $_GET['userId'];
	
	// Get user and appointment details
	$sql = "SELECT u.name, u.email, a.pet_name, a.pet_type, a.appointment_date, a.appointment_type, a.status 
			FROM tbl_users u, tbl_appointments a 
			WHERE u.id = a.uid AND u.id = $userId 
			AND a.status = 'APPROVED'
			LIMIT 1";
	$result = dbQuery($sql);
	
	if (dbNumRows($result) > 0) {
		$data = dbFetchAssoc($result);
		
		// Format appointment date for better display
		$appointmentDateTime = new DateTime($data['appointment_date']);
		$formattedDate = $appointmentDateTime->format('l, F j, Y \a\t g:i A');
		
		$emailMsg = get_email_msg(array(
			'msg' => 'appointment_reminder',
			'name' => $data['name'],
			'pet_name' => $data['pet_name'],
			'pet_type' => $data['pet_type'],
			'appointment_date' => $formattedDate,
			'appointment_type' => $data['appointment_type']
		));
		
		$emailData = array(
			'to' => $data['email'], 
			'sub' => '🔔 Appointment Reminder - ' . $data['pet_name'] . ' at Veterinary Clinic', 
			'msg' => $emailMsg
		);
		
		$emailSent = send_email($emailData);
		
		if ($emailSent) {
			// Log the reminder in database
			$staffId = isset($_SESSION['calendar_fd_user']['user_id']) ? $_SESSION['calendar_fd_user']['user_id'] : 1;
			$logSql = "INSERT INTO tbl_appointment_reminders (appointment_id, sent_date, sent_by, email_status) 
					   VALUES ($userId, NOW(), $staffId, 'sent')";
			dbQuery($logSql);
			
			$message = 'Reminder email successfully sent to ' . $data['name'] . ' (' . $data['email'] . ')';
			header('Location: ../views/?v=LIST&msg=' . urlencode($message));
		} else {
			$error = 'Failed to send reminder email. Please check email configuration.';
			header('Location: ../views/?v=LIST&err=' . urlencode($error));
		}
	} else {
		$error = 'Appointment not found or not approved. Only confirmed appointments can receive reminders.';
		header('Location: ../views/?v=LIST&err=' . urlencode($error));
	}
	exit();
}

function updateAppointment() {
	$appointmentId = (int)$_POST['appointmentId'];
	$userId = (int)$_POST['userId'];
	$name = $_POST['name'];
	$address = $_POST['address'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$pet_name = $_POST['pet_name'];
	$pet_type = $_POST['pet_type'];
	$pet_breed = isset($_POST['pet_breed']) ? $_POST['pet_breed'] : '';
	$appointment_type = $_POST['appointment_type'];
	$status = $_POST['status'];
	$comments = isset($_POST['comments']) ? $_POST['comments'] : '';
	$rdate = $_POST['rdate'];
	$rtime = $_POST['rtime'];
	$appointment_date = $rdate . ' ' . $rtime;
	
	// Get original status for email notification
	$originalStatusSql = "SELECT status FROM tbl_appointments WHERE id = $appointmentId";
	$originalResult = dbQuery($originalStatusSql);
	$originalStatus = '';
	if (dbNumRows($originalResult) > 0) {
		$originalRow = dbFetchAssoc($originalResult);
		$originalStatus = $originalRow['status'];
	}
	
	// Check if the new date has a holiday
	$hsql = "SELECT * FROM tbl_holidays WHERE date = '$rdate'";
	$hresult = dbQuery($hsql);
	if (dbNumRows($hresult) > 0) {
		$errorMessage = 'The clinic is closed on the selected date. Please choose another day.';
		header('Location: ../views/?v=EDIT&ID=' . $userId . '&err=' . urlencode($errorMessage));
		exit();
	}
	
	// Update user information
	$userSql = "UPDATE tbl_users SET 
				name = '$name',
				address = '$address',
				phone = '$phone',
				email = '$email'
				WHERE id = $userId";
	dbQuery($userSql);
	
	// Update appointment information
	$appointmentSql = "UPDATE tbl_appointments SET 
					   pet_name = '$pet_name',
					   pet_type = '$pet_type',
					   pet_breed = '$pet_breed',
					   appointment_date = '$appointment_date',
					   appointment_type = '$appointment_type',
					   status = '$status',
					   comments = '$comments'
					   WHERE id = $appointmentId";
	dbQuery($appointmentSql);
	
	// Send email notification if status changed
	if ($originalStatus != $status && ($status == 'APPROVED' || $status == 'DENIED')) {
		$formattedDate = date('l, F j, Y \a\t g:i A', strtotime($appointment_date));
		
		if ($status == 'APPROVED') {
			$emailMsg = get_email_msg(array(
				'msg' => 'appointment_confirmed',
				'name' => $name,
				'pet_name' => $pet_name,
				'appointment_date' => $formattedDate,
				'appointment_type' => $appointment_type
			));
			$subject = 'Veterinary Appointment CONFIRMED - Updated';
		} else {
			$emailMsg = get_email_msg(array(
				'msg' => 'appointment_denied',
				'name' => $name,
				'appointment_date' => $formattedDate,
				'reason' => 'Appointment has been updated and declined'
			));
			$subject = 'Veterinary Appointment Update';
		}
		
		$emailData = array(
			'to' => $email, 
			'sub' => $subject, 
			'msg' => $emailMsg
		);
		send_email($emailData);
	}
	
	// Log the update
	$staffId = isset($_SESSION['calendar_fd_user']['user_id']) ? $_SESSION['calendar_fd_user']['user_id'] : 1;
	$logSql = "INSERT INTO tbl_appointment_reminders (appointment_id, sent_date, sent_by, email_status) 
			   VALUES ($userId, NOW(), $staffId, 'updated')";
	dbQuery($logSql);
	
	$message = 'Appointment successfully updated.';
	if ($originalStatus != $status) {
		$message .= ' Email notification sent to client.';
	}
	
	header('Location: ../views/?v=LIST&msg=' . urlencode($message));
	exit();
}

?>