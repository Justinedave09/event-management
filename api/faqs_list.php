<?php
require_once '../library/config.php';
require_once '../library/functions.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $dbConn;

if (!$dbConn instanceof mysqli) {
    http_response_code(500);
    exit(json_encode(['error' => 'Database not available']));
}

$faqs = [];
$sql = "SELECT id, category, question 
        FROM tbl_faqs 
        WHERE is_active = 1 
        ORDER BY category ASC, priority DESC, id ASC";

$result = mysqli_query($dbConn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = [
            'id'       => (int)$row['id'],
            'category' => $row['category'],
            'question' => $row['question'],
        ];
    }
}

echo json_encode(['faqs' => $faqs]);