<?php
// Use your existing email system
require_once 'mail.php'; // ← path to your existing file with send_email() and get_email_msg()

/**
 * Send announcement to all active pet owners using existing email system
 */
function sendAnnouncementEmails($conn, $announcementId) {

    // ── Fetch announcement ─────────────────────────────────
    $r = mysqli_query($conn, "SELECT * FROM tbl_announcements WHERE id=$announcementId");
    $ann = mysqli_fetch_assoc($r);

    if (!$ann) {
        return ['success' => false, 'error' => 'Announcement not found'];
    }

    // ── Fetch pet owners with valid emails ────────────────
    $sql = "SELECT id, name, email FROM tbl_users 
            WHERE type IN ('client', 'user', 'owner') 
              AND status = 'active' 
              AND email IS NOT NULL 
              AND email != ''
              AND email LIKE '%@%'";
    $r = mysqli_query($conn, $sql);

    if (!$r) {
        return ['success' => false, 'error' => 'Failed to fetch recipients'];
    }

    $recipients = [];
    while ($row = mysqli_fetch_assoc($r)) {
        $recipients[] = $row;
    }

    if (empty($recipients)) {
        return ['success' => false, 'error' => 'No pet owners with valid email addresses'];
    }

    // ── Subject emoji map ──────────────────────────────────
    $emojis = [
        'event'   => '📅',
        'promo'   => '🏷️',
        'holiday' => '🎁',
        'urgent'  => '⚠️',
        'general' => '📢',
    ];
    $emoji = $emojis[$ann['type']] ?? '📢';

    $sentCount   = 0;
    $failedCount = 0;
    $errors      = [];

    // ── Send emails using your existing send_email() ──────
    foreach ($recipients as $recipient) {

        // Build message using existing get_email_msg()
        $msgData = [
            'msg'        => 'announcement',
            'name'       => $recipient['name'],
            'title'      => $ann['title'],
            'type'       => $ann['type'],
            'content'    => $ann['content'],
            'image_url'  => $ann['image_url'],
            'start_date' => $ann['start_date'],
            'end_date'   => $ann['end_date'],
        ];

        $emailBody = get_email_msg($msgData);

        // Send using your existing send_email()
        $result = send_email([
            'to'  => $recipient['email'],
            'sub' => $emoji . ' ' . $ann['title'],
            'msg' => $emailBody,
        ]);

        $emailSafe = mysqli_real_escape_string($conn, $recipient['email']);

        if ($result) {
            $sentCount++;
            mysqli_query($conn, 
                "INSERT INTO tbl_announcement_emails 
                 (announcement_id, recipient_id, recipient_email, status) 
                 VALUES ($announcementId, {$recipient['id']}, '$emailSafe', 'sent')"
            );
        } else {
            $failedCount++;
            mysqli_query($conn, 
                "INSERT INTO tbl_announcement_emails 
                 (announcement_id, recipient_id, recipient_email, status, error_message) 
                 VALUES ($announcementId, {$recipient['id']}, '$emailSafe', 'failed', 'mail() returned false')"
            );
            $errors[] = $recipient['email'];
        }

        // Small delay to avoid SMTP rate limits
        usleep(100000); // 0.1 second
    }

    // ── Update announcement status ─────────────────────────
    mysqli_query($conn, "UPDATE tbl_announcements SET 
                          email_sent = 1, 
                          email_sent_date = NOW(),
                          email_sent_count = email_sent_count + $sentCount
                         WHERE id=$announcementId");

    return [
        'success' => true,
        'sent'    => $sentCount,
        'failed'  => $failedCount,
        'total'   => count($recipients),
        'errors'  => $errors,
    ];
}