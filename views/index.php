<?php
require_once '../library/config.php';
require_once '../library/functions.php';

checkFDUser();

$view = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : '';

switch ($view) {
	case 'LIST' :
		$content 	= 'eventlist.php';		
		$pageTitle 	= 'View Appointment Details';
		break;
		
	case 'EDIT' :
		$content 	= 'appedit.php';		
		$pageTitle 	= 'Edit Appointment';
		break;

	case 'USERS' :
		$content 	= 'userlist.php';		
		$pageTitle 	= 'View Pet Owners';
		break;
		
	case 'USEREDIT' :
		$content 	= 'useredit.php';		
		$pageTitle 	= 'Edit User';
		break;
		
	case 'CREATE' :
		$content 	= 'userform.php';		
		$pageTitle 	= 'Register Pet Owner';
		break;
		
	case 'STAFF' :
		$content 	= 'staffform.php';		
		$pageTitle 	= 'Staff Management';
		break;
		
	case 'STAFFEDIT' :
		$content 	= 'staffedit.php';		
		$pageTitle 	= 'Edit Staff Member';
		break;
		
	case 'USER' :
		$content 	= 'user.php';		
		$pageTitle 	= 'View User Details';
		break;
	
	case 'HOLY' :
		$content 	= 'holidays.php';		
		$pageTitle 	= 'Clinic Closed Days';
		break;
		
	case 'SETTINGS' :
		$content 	= 'settings.php';		
		$pageTitle 	= 'System Settings';
		break;
		
	case 'MYAPPOINTMENTS' :
		$content 	= 'my_appointments.php';		
		$pageTitle 	= 'My Appointments';
		break;	
	
	default :
		$content 	= 'dashboard.php';		
		$pageTitle 	= 'Calendar Dashboard';
}

require_once '../include/template.php';
?>
