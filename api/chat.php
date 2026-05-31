<?php
require_once '../library/config.php';
require_once '../library/ai_config.php';
require_once '../library/functions.php';

header('Content-Type: application/json');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $dbConn;
$conn = $dbConn;

// ── Only accept POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

// ── Parse body ─────────────────────────────────────────────
$body        = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($body['message'] ?? '');

if (!empty($body['reset'])) {
    $_SESSION['chat_history'] = [];
    exit(json_encode(['reply' => 'Conversation cleared.']));
}

if (empty($userMessage)) {
    http_response_code(400);
    exit(json_encode(['error' => 'No message provided']));
}

if (!defined('GROQ_API_KEY') || empty(GROQ_API_KEY)) {
    http_response_code(500);
    exit(json_encode(['error' => 'GROQ_API_KEY not set in config.php']));
}

// ── Fetch relevant FAQs ────────────────────────────────────
function getRelevantFAQs($conn, $userMessage, $limit = 10) {
    $message = mysqli_real_escape_string($conn, strtolower($userMessage));
    $words   = preg_split('/\s+/', $message);
    $words   = array_filter($words, function ($w) { return strlen($w) > 2; });

    if (empty($words)) {
        $sql = "SELECT category, question, answer FROM tbl_faqs 
                WHERE is_active = 1 
                ORDER BY priority DESC LIMIT $limit";
    } else {
        $conditions = [];
        foreach ($words as $word) {
            $word = mysqli_real_escape_string($conn, $word);
            $conditions[] = "(LOWER(question) LIKE '%$word%' 
                              OR LOWER(answer) LIKE '%$word%' 
                              OR LOWER(keywords) LIKE '%$word%')";
        }
        $where = implode(' OR ', $conditions);

        $sql = "SELECT category, question, answer FROM tbl_faqs 
                WHERE is_active = 1 AND ($where)
                ORDER BY priority DESC LIMIT $limit";
    }

    $result = mysqli_query($conn, $sql);
    $faqs   = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $faqs[] = $row;
        }
    }
    return $faqs;
}

// ── Fetch live system stats from your vet clinic tables ───
function getSystemStats($conn) {
    $stats = [];

    // Appointment stats
    $sql = "SELECT 
              COUNT(*) AS total,
              SUM(CASE WHEN status='pending'   THEN 1 ELSE 0 END) AS pending,
              SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) AS confirmed,
              SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed,
              SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) AS cancelled
            FROM tbl_appointments";
    $r = @mysqli_query($conn, $sql);
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats['Total Appointments']     = $row['total'];
        $stats['Pending Appointments']   = $row['pending'];
        $stats['Confirmed Appointments'] = $row['confirmed'];
        $stats['Completed Appointments'] = $row['completed'];
        $stats['Cancelled Appointments'] = $row['cancelled'];
    }

    // Today's appointments
    $r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_appointments 
                                WHERE DATE(appointment_date) = CURDATE()");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats["Today's Appointments"] = $row['c'];
    }

    // Upcoming appointments
    $r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_appointments 
                                WHERE appointment_date >= NOW() 
                                AND status IN ('pending','confirmed')");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats['Upcoming Appointments'] = $row['c'];
    }

    // User stats
    $r = @mysqli_query($conn, "SELECT 
                                  COUNT(*) AS total,
                                  SUM(CASE WHEN type='admin' THEN 1 ELSE 0 END) AS admins,
                                  SUM(CASE WHEN type='user'  THEN 1 ELSE 0 END) AS clients,
                                  SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) AS active
                               FROM tbl_users");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats['Total Users']    = $row['total'];
        $stats['Admin Users']    = $row['admins'];
        $stats['Client Users']   = $row['clients'];
        $stats['Active Users']   = $row['active'];
    }

    // Front desk staff
    $r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_frontdesk_users");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats['Front Desk Staff'] = $row['c'];
    }

    // Holidays
    $r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_holidays 
                                WHERE STR_TO_DATE(date, '%Y-%m-%d') >= CURDATE()");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $stats['Upcoming Holidays'] = $row['c'];
    }

    // Pet type breakdown
    $r = @mysqli_query($conn, "SELECT pet_type, COUNT(*) AS c FROM tbl_appointments 
                                GROUP BY pet_type ORDER BY c DESC LIMIT 5");
    if ($r) {
        $pets = [];
        while ($row = mysqli_fetch_assoc($r)) {
            $pets[] = $row['pet_type'] . ' (' . $row['c'] . ')';
        }
        if (!empty($pets)) {
            $stats['Pet Types Seen'] = implode(', ', $pets);
        }
    }
// Pet stats
$r = @mysqli_query($conn, "SELECT 
                              COUNT(*) AS total,
                              SUM(CASE WHEN pet_type='Dog' THEN 1 ELSE 0 END) AS dogs,
                              SUM(CASE WHEN pet_type='Cat' THEN 1 ELSE 0 END) AS cats
                           FROM tbl_pets WHERE is_active=1");
if ($r && $row = mysqli_fetch_assoc($r)) {
    $stats['Total Pets Registered'] = $row['total'];
    $stats['Total Dogs']            = $row['dogs'];
    $stats['Total Cats']            = $row['cats'];
}

// Medical visits this month
$r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_medical_history 
                            WHERE MONTH(visit_date) = MONTH(CURDATE()) 
                            AND YEAR(visit_date) = YEAR(CURDATE())");
if ($r && $row = mysqli_fetch_assoc($r)) {
    $stats['Medical Visits This Month'] = $row['c'];
}

// Vaccinations due soon (next 30 days)
$r = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_vaccinations 
                            WHERE next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
if ($r && $row = mysqli_fetch_assoc($r)) {
    $stats['Vaccinations Due Soon'] = $row['c'];
}
    return $stats;
}

// ── Fetch clinic settings ──────────────────────────────────
function getClinicSettings($conn) {
    $settings = [];
    $r = @mysqli_query($conn, "SELECT setting_key, setting_value FROM tbl_system_settings");
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

// ── Fetch upcoming holidays ────────────────────────────────
function getUpcomingHolidays($conn, $limit = 5) {
    $holidays = [];
    $sql = "SELECT date, reason FROM tbl_holidays 
            WHERE STR_TO_DATE(date, '%Y-%m-%d') >= CURDATE()
            ORDER BY date ASC LIMIT $limit";
    $r = @mysqli_query($conn, $sql);
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $holidays[] = $row['date'] . ' - ' . $row['reason'];
        }
    }
    return $holidays;
}

// ── Build system context ───────────────────────────────────
function buildSystemContext($conn, $userMessage) {
    $context = "You are a friendly AI assistant for a Veterinary Clinic Appointment Management System.\n";
    $context .= "Help users with appointments, pet care questions, and clinic information.\n\n";

    // Clinic info
    $settings = getClinicSettings($conn);
    if (!empty($settings)) {
        $context .= "=== CLINIC INFORMATION ===\n";
        if (!empty($settings['clinic_name']))    $context .= "Name: " . $settings['clinic_name'] . "\n";
        if (!empty($settings['clinic_address'])) $context .= "Address: " . $settings['clinic_address'] . "\n";
        if (!empty($settings['clinic_phone']))   $context .= "Phone: " . $settings['clinic_phone'] . "\n";
        if (!empty($settings['clinic_email']))   $context .= "Email: " . $settings['clinic_email'] . "\n";
        if (!empty($settings['clinic_hours']))   $context .= "Hours: " . $settings['clinic_hours'] . "\n";
        $context .= "\n";
    }

    // FAQs
    $faqs = getRelevantFAQs($conn, $userMessage);
    if (!empty($faqs)) {
        $context .= "=== KNOWLEDGE BASE (use these to answer questions) ===\n";
        foreach ($faqs as $faq) {
            $context .= "\n[{$faq['category']}]\n";
            $context .= "Q: {$faq['question']}\n";
            $context .= "A: {$faq['answer']}\n";
        }
        $context .= "\n";
    }

    // Live stats
    $stats = getSystemStats($conn);
    if (!empty($stats)) {
        $context .= "=== CURRENT SYSTEM DATA (live) ===\n";
        foreach ($stats as $key => $value) {
            $context .= "$key: $value\n";
        }
        $context .= "\n";
    }

    // Upcoming holidays
    $holidays = getUpcomingHolidays($conn);
    if (!empty($holidays)) {
        $context .= "=== UPCOMING HOLIDAYS (clinic closed) ===\n";
        foreach ($holidays as $holiday) {
            $context .= "- $holiday\n";
        }
        $context .= "\n";
    }

    $context .= "Instructions:\n";
    $context .= "- Use the KNOWLEDGE BASE to answer FAQ-type questions.\n";
    $context .= "- Use CURRENT SYSTEM DATA when asked about statistics or counts.\n";
    $context .= "- Use CLINIC INFORMATION for hours, contact details, address.\n";
    $context .= "- For pet health emergencies, always advise calling the clinic immediately.\n";
    $context .= "- Be warm, helpful, and concise. Use markdown formatting.\n";
    $context .= "- If you don't know the answer, suggest contacting the front desk.\n";

    return $context;
}

// ── Build messages ─────────────────────────────────────────
$systemContext = buildSystemContext($conn, $userMessage);

$messages = [['role' => 'system', 'content' => $systemContext]];

if (isset($_SESSION['chat_history']) && is_array($_SESSION['chat_history'])) {
    foreach ($_SESSION['chat_history'] as $msg) {
        if ($msg['role'] !== 'system') {
            $messages[] = $msg;
        }
    }
}

$userMsg = ['role' => 'user', 'content' => $userMessage];
$messages[] = $userMsg;

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}
$_SESSION['chat_history'][] = $userMsg;

$maxHistory = defined('AI_MAX_HISTORY') ? AI_MAX_HISTORY : 10;
if (count($_SESSION['chat_history']) > $maxHistory) {
    $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -$maxHistory);
}

// ── Send to Groq ───────────────────────────────────────────
$payload = json_encode([
    'model'       => GROQ_MODEL,
    'messages'    => $messages,
    'temperature' => defined('AI_TEMPERATURE') ? AI_TEMPERATURE : 0.7,
    'max_tokens'  => defined('AI_MAX_TOKENS')  ? AI_MAX_TOKENS  : 1024,
]);

$ch = curl_init(GROQ_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_CAINFO         => ini_get('curl.cainfo'),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY,
    ],
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(502);
    exit(json_encode(['error' => 'Connection failed: ' . $curlError]));
}

if ($httpCode !== 200) {
    $decoded = json_decode($response, true);
    http_response_code(502);
    exit(json_encode([
        'error'  => 'Groq returned HTTP ' . $httpCode,
        'detail' => $decoded['error']['message'] ?? $response,
    ]));
}

$data  = json_decode($response, true);
$reply = $data['choices'][0]['message']['content'] ?? '';

if (empty($reply)) {
    exit(json_encode(['reply' => '⚠️ No response generated.']));
}

$_SESSION['chat_history'][] = [
    'role'    => 'assistant',
    'content' => $reply,
];

echo json_encode(['reply' => trim($reply)]);