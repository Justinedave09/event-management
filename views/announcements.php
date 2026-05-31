<?php
global $dbConn;
$conn = $dbConn;

$message     = '';
$messageType = '';
$currentUser = $_SESSION['calendar_fd_user'];
$userType    = $currentUser['type'];
$userId      = $currentUser['id'];

// Only admin/staff can manage; clients can only view
$canManage = ($userType !== 'client');

// ════════════════════════════════════════════════════════════
// HANDLE ACTIONS (Admin/Staff only)
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage) {
    $action = $_POST['action'] ?? '';

    // ── Add / Update ────────────────────────────────────────
    if ($action === 'add' || $action === 'update') {
        $title     = mysqli_real_escape_string($conn, $_POST['title']);
        $type      = mysqli_real_escape_string($conn, $_POST['type']);
        $content   = mysqli_real_escape_string($conn, $_POST['content']);
        $imageUrl  = mysqli_real_escape_string($conn, $_POST['image_url'] ?? '');
        $startDate = !empty($_POST['start_date']) 
                     ? "'" . mysqli_real_escape_string($conn, $_POST['start_date']) . "'" 
                     : 'NULL';
        $endDate   = !empty($_POST['end_date']) 
                     ? "'" . mysqli_real_escape_string($conn, $_POST['end_date']) . "'" 
                     : 'NULL';
        $isPinned  = !empty($_POST['is_pinned']) ? 1 : 0;
        $isActive  = !empty($_POST['is_active']) ? 1 : 0;

        if ($action === 'add') {
            $sql = "INSERT INTO tbl_announcements 
                    (title, type, content, image_url, start_date, end_date, is_pinned, is_active, created_by) 
                    VALUES 
                    ('$title', '$type', '$content', '$imageUrl', $startDate, $endDate, $isPinned, $isActive, $userId)";
        } else {
            $id = (int)$_POST['ann_id'];
            $sql = "UPDATE tbl_announcements SET 
                    title='$title', type='$type', content='$content', image_url='$imageUrl',
                    start_date=$startDate, end_date=$endDate, is_pinned=$isPinned, is_active=$isActive
                    WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            $message = 'Announcement ' . ($action === 'add' ? 'created' : 'updated') . ' successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Delete ──────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int)$_POST['ann_id'];
        // Delete email logs first
        mysqli_query($conn, "DELETE FROM tbl_announcement_emails WHERE announcement_id=$id");
        if (mysqli_query($conn, "DELETE FROM tbl_announcements WHERE id=$id")) {
            $message = 'Announcement deleted.';
            $messageType = 'success';
        }
    }

    // ── Send Email ──────────────────────────────────────────
    if ($action === 'send_email') {
        $id = (int)$_POST['ann_id'];
        require_once __DIR__ . '/../library/send_announcement.php';
        $result = sendAnnouncementEmails($conn, $id);
        
        if ($result['success']) {
            $message = '📧 Email sent to ' . $result['sent'] . ' pet owner(s)!';
            if ($result['failed'] > 0) {
                $message .= ' (' . $result['failed'] . ' failed)';
            }
            $messageType = 'success';
        } else {
            $message = '❌ Email error: ' . $result['error'];
            $messageType = 'danger';
        }
    }
}

// ════════════════════════════════════════════════════════════
// FETCH DATA
// ════════════════════════════════════════════════════════════

// For clients: only show active + not expired announcements
$whereClause = $canManage 
    ? "1=1" 
    : "is_active = 1 AND (end_date IS NULL OR end_date >= CURDATE())";

$announcements = [];
$sql = "SELECT a.*, u.name AS author_name 
        FROM tbl_announcements a 
        LEFT JOIN tbl_users u ON a.created_by = u.id
        WHERE $whereClause
        ORDER BY a.is_pinned DESC, a.created_date DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $announcements[] = $row;
    }
}

// Get pet owner count for admin
$ownerCount = 0;
if ($canManage) {
    $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_users 
                               WHERE type IN ('client','user','owner') AND status='active' 
                               AND email != '' AND email IS NOT NULL");
    if ($r) {
        $row = mysqli_fetch_assoc($r);
        $ownerCount = $row['c'];
    }
}

// Helper: get badge for announcement type
function getTypeBadge($type) {
    $badges = [
        'event'   => ['icon' => 'calendar-check-o', 'color' => '#3c8dbc', 'label' => 'EVENT'],
        'promo'   => ['icon' => 'tag',              'color' => '#00a65a', 'label' => 'PROMO'],
        'holiday' => ['icon' => 'gift',             'color' => '#f39c12', 'label' => 'HOLIDAY'],
        'urgent'  => ['icon' => 'exclamation-triangle', 'color' => '#dd4b39', 'label' => 'URGENT'],
        'general' => ['icon' => 'bullhorn',         'color' => '#605ca8', 'label' => 'NOTICE'],
    ];
    return $badges[$type] ?? $badges['general'];
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <i class="fa fa-info-circle"></i> <?= $message ?>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- HEADER                                                          -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">
      <i class="fa fa-bullhorn"></i> 
      <?= $canManage ? 'Announcements Management' : 'Clinic News & Announcements' ?>
    </h3>
    <?php if ($canManage): ?>
    <div class="pull-right">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addAnnModal">
        <i class="fa fa-plus"></i> New Announcement
      </button>
    </div>
    <?php endif; ?>
  </div>

  <div class="box-body">

    <?php if ($canManage): ?>
    <div class="alert alert-info" style="margin-bottom:15px;">
      <i class="fa fa-envelope"></i> 
      <strong><?= $ownerCount ?></strong> pet owner(s) with valid email addresses can receive announcements.
    </div>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════ -->
    <!-- ANNOUNCEMENTS LIST                                          -->
    <!-- ════════════════════════════════════════════════════════ -->

    <?php if (empty($announcements)): ?>
      <div class="text-center text-muted" style="padding:50px 20px;">
        <i class="fa fa-bullhorn fa-3x" style="color:#ddd; display:block; margin-bottom:15px;"></i>
        <h4 style="color:#999;">No announcements yet</h4>
        <p>
          <?php if ($canManage): ?>
            Click "New Announcement" to create your first one.
          <?php else: ?>
            Check back soon for clinic news and special offers!
          <?php endif; ?>
        </p>
      </div>
    <?php else: ?>

      <?php foreach ($announcements as $ann): 
        $badge     = getTypeBadge($ann['type']);
        $isExpired = !empty($ann['end_date']) && strtotime($ann['end_date']) < strtotime(date('Y-m-d'));
        $cardBg    = (!$ann['is_active'] || $isExpired) ? '#f9f9f9' : '#fff';
        $opacity   = (!$ann['is_active'] || $isExpired) ? '0.7' : '1';
      ?>
      <div class="announcement-card" 
           style="border-left:4px solid <?= $badge['color'] ?>; 
                  background:<?= $cardBg ?>; 
                  opacity:<?= $opacity ?>;
                  padding:18px 20px; margin-bottom:15px; 
                  border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.08);
                  transition:transform 0.15s, box-shadow 0.15s;"
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)';"
           onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)';">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:15px; flex-wrap:wrap;">
          
          <!-- ── Content ───────────────────────────────── -->
          <div style="flex:1; min-width:280px;">
            
            <!-- Tags -->
            <div style="margin-bottom:10px;">
              <span style="background:<?= $badge['color'] ?>; color:#fff; padding:3px 10px; 
                           border-radius:3px; font-size:11px; font-weight:bold; letter-spacing:0.5px;">
                <i class="fa fa-<?= $badge['icon'] ?>"></i> <?= $badge['label'] ?>
              </span>
              
              <?php if ($ann['is_pinned']): ?>
                <span class="label label-warning" style="font-size:10px;">
                  <i class="fa fa-thumb-tack"></i> PINNED
                </span>
              <?php endif; ?>
              
              <?php if ($canManage && !$ann['is_active']): ?>
                <span class="label label-default">📝 DRAFT</span>
              <?php endif; ?>
              
              <?php if ($canManage && $isExpired): ?>
                <span class="label label-default">⏰ EXPIRED</span>
              <?php endif; ?>
              
              <?php if ($canManage && $ann['email_sent']): ?>
                <span class="label label-success" 
                      title="Sent <?= date('M d, Y g:i A', strtotime($ann['email_sent_date'])) ?>">
                  <i class="fa fa-envelope"></i> EMAILED (<?= $ann['email_sent_count'] ?>)
                </span>
              <?php endif; ?>
            </div>
            
            <!-- Title -->
            <h3 style="margin:0 0 10px 0; color:#333; font-size:18px; font-weight:600;">
              <?= htmlspecialchars($ann['title']) ?>
            </h3>
            
            <!-- Content -->
            <div style="color:#555; font-size:14px; line-height:1.7; white-space:pre-wrap;">
              <?= nl2br(htmlspecialchars($ann['content'])) ?>
            </div>
            
            <!-- Image -->
            <?php if (!empty($ann['image_url'])): ?>
              <img src="<?= htmlspecialchars($ann['image_url']) ?>" 
                   style="max-width:100%; margin-top:12px; border-radius:6px; max-height:300px;" 
                   alt="Announcement image"
                   onerror="this.style.display='none';">
            <?php endif; ?>
            
            <!-- Date range (if present) -->
            <?php if (!empty($ann['start_date']) || !empty($ann['end_date'])): ?>
            <div style="margin-top:12px; padding:8px 12px; background:#f5f5f5; 
                        border-radius:4px; font-size:12px; color:#555; display:inline-block;">
              <i class="fa fa-calendar"></i>
              <?php if (!empty($ann['start_date'])): ?>
                <strong><?= date('M d, Y', strtotime($ann['start_date'])) ?></strong>
              <?php endif; ?>
              <?php if (!empty($ann['end_date'])): ?>
                → <strong><?= date('M d, Y', strtotime($ann['end_date'])) ?></strong>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Meta info -->
            <div style="margin-top:10px; font-size:11px; color:#999;">
              <i class="fa fa-clock-o"></i>
              Posted <?= date('M d, Y g:i A', strtotime($ann['created_date'])) ?>
              <?php if (!empty($ann['author_name'])): ?>
                by <strong><?= htmlspecialchars($ann['author_name']) ?></strong>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- ── Admin Actions ──────────────────────────── -->
          <?php if ($canManage): ?>
          <div style="display:flex; flex-direction:column; gap:5px; min-width:130px;">
            <button type="button" class="btn btn-sm btn-info send-email-btn"
                    data-ann-id="<?= $ann['id'] ?>"
                    data-ann-title="<?= htmlspecialchars($ann['title'], ENT_QUOTES) ?>"
                    <?= !$ann['is_active'] ? 'disabled title="Activate announcement first"' : '' ?>>
              <i class="fa fa-envelope"></i> 
              <?= $ann['email_sent'] ? 'Resend' : 'Send Email' ?>
            </button>
            <button type="button" class="btn btn-sm btn-primary edit-ann"
                    data-ann='<?= htmlspecialchars(json_encode($ann), ENT_QUOTES) ?>'>
              <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm btn-danger delete-ann"
                    data-ann-id="<?= $ann['id'] ?>"
                    data-ann-title="<?= htmlspecialchars($ann['title'], ENT_QUOTES) ?>">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
          <?php endif; ?>
          
        </div>
      </div>
      <?php endforeach; ?>
      
    <?php endif; ?>

    <?php if (!empty($announcements)): ?>
    <p class="text-muted" style="margin-top:15px; font-size:12px;">
      <i class="fa fa-info-circle"></i> 
      Showing <strong><?= count($announcements) ?></strong> announcement(s)
    </p>
    <?php endif; ?>

  </div>
</div>

<?php if ($canManage): ?>
<!-- ════════════════════════════════════════════════════════════ -->
<!-- ADD/EDIT MODAL                                                  -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="addAnnModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="annForm">
        <input type="hidden" name="action" value="add" id="annAction">
        <input type="hidden" name="ann_id" id="ann_id">
        
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="annModalTitle">
            <i class="fa fa-plus"></i> New Announcement
          </h4>
        </div>
        
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 form-group">
              <label>Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="title" class="form-control" required 
                     placeholder="e.g., Holiday Promo - 20% Off Vaccinations!"
                     maxlength="255">
            </div>
            <div class="col-md-4 form-group">
              <label>Type <span class="text-danger">*</span></label>
              <select name="type" id="type" class="form-control" required>
                <option value="general">📢 General Notice</option>
                <option value="event">📅 Event</option>
                <option value="promo">🏷️ Promo / Discount</option>
                <option value="holiday">🎁 Holiday</option>
                <option value="urgent">⚠️ Urgent</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label>Content <span class="text-danger">*</span></label>
            <textarea name="content" id="content" class="form-control" rows="6" required
                      placeholder="Write your announcement here. This is what pet owners will see and receive via email."></textarea>
            <small class="text-muted">
              <i class="fa fa-info-circle"></i> 
              Use plain text. Emojis are welcome! 🎉 Line breaks are preserved.
            </small>
          </div>
          
          <div class="form-group">
            <label>Image URL <small class="text-muted">(optional)</small></label>
            <input type="url" name="image_url" id="image_url" class="form-control" 
                   placeholder="https://example.com/image.jpg">
            <small class="text-muted">Direct link to an image to display with the announcement.</small>
          </div>
          
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Start Date <small class="text-muted">(optional)</small></label>
              <input type="date" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="col-md-6 form-group">
              <label>End Date <small class="text-muted">(optional)</small></label>
              <input type="date" name="end_date" id="end_date" class="form-control">
              <small class="text-muted">Leave blank if no expiration.</small>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="is_pinned" id="is_pinned" value="1">
                  <i class="fa fa-thumb-tack"></i> Pin to top (always show first)
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                  <i class="fa fa-eye"></i> Active (visible to pet owners)
                </label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save Announcement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Hidden forms -->
<form id="deleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="ann_id" id="delete_ann_id">
</form>

<form id="sendEmailForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="send_email">
  <input type="hidden" name="ann_id" id="email_ann_id">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Edit Announcement ──────────────────────────────────
  document.querySelectorAll('.edit-ann').forEach(function (btn) {
    btn.addEventListener('click', function () {
      try {
        const ann = JSON.parse(this.dataset.ann);
        document.getElementById('annAction').value = 'update';
        document.getElementById('ann_id').value = ann.id;
        document.getElementById('title').value = ann.title || '';
        document.getElementById('type').value = ann.type || 'general';
        document.getElementById('content').value = ann.content || '';
        document.getElementById('image_url').value = ann.image_url || '';
        document.getElementById('start_date').value = ann.start_date || '';
        document.getElementById('end_date').value = ann.end_date || '';
        document.getElementById('is_pinned').checked = ann.is_pinned == 1;
        document.getElementById('is_active').checked = ann.is_active == 1;
        document.getElementById('annModalTitle').innerHTML = 
          '<i class="fa fa-edit"></i> Edit Announcement';
        
        if (typeof $ !== 'undefined' && $.fn.modal) {
          $('#addAnnModal').modal('show');
        }
      } catch (e) {
        alert('Error loading announcement: ' + e.message);
      }
    });
  });

  // ── Reset modal on close ───────────────────────────────
  if (typeof $ !== 'undefined' && $.fn.modal) {
    $('#addAnnModal').on('hidden.bs.modal', function () {
      document.getElementById('annForm').reset();
      document.getElementById('annAction').value = 'add';
      document.getElementById('ann_id').value = '';
      document.getElementById('is_active').checked = true;
      document.getElementById('annModalTitle').innerHTML = 
        '<i class="fa fa-plus"></i> New Announcement';
    });
  }

  // ── Delete ─────────────────────────────────────────────
  document.querySelectorAll('.delete-ann').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id    = this.dataset.annId;
      const title = this.dataset.annTitle;
      
      if (confirm('Delete announcement?\n\n"' + title + '"\n\nThis cannot be undone.')) {
        document.getElementById('delete_ann_id').value = id;
        document.getElementById('deleteForm').submit();
      }
    });
  });

  // ── Send Email ─────────────────────────────────────────
  document.querySelectorAll('.send-email-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id    = this.dataset.annId;
      const title = this.dataset.annTitle;
      
      if (confirm('📧 Send this announcement via email to all <?= $ownerCount ?> pet owners?\n\n' +
                  '"' + title + '"\n\n' +
                  'This may take a moment depending on the number of recipients.\n\nContinue?')) {
        this.disabled = true;
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        document.getElementById('email_ann_id').value = id;
        document.getElementById('sendEmailForm').submit();
      }
    });
  });
});
</script>
<?php endif; ?>