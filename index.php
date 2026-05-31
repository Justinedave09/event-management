<?php
require_once './library/config.php';
require_once './library/functions.php';

checkFDUser();

$view = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : '';

switch ($view) {
    case 'CHAT':
        $content   = 'views/chat.php';
        $pageTitle = 'AI Chat';
        break;
    
    case 'FAQS':
        $content   = 'views/faqs.php';
        $pageTitle = 'FAQs Management';
        break;
    
    case 'PETS':
        $content   = 'views/pets.php';
        $pageTitle = 'Pet Management';
        break;
    
    default:
        $content   = 'views/dashboard.php';
        $pageTitle = 'Veterinary Appointment System';
}

$script = array();

require_once 'include/template.php';
?>