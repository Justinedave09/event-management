<?php



global $dbConn;
$conn = $dbConn;

$message = '';
$messageType = '';

// ── Handle form submissions ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $question = mysqli_real_escape_string($conn, $_POST['question']);
        $answer   = mysqli_real_escape_string($conn, $_POST['answer']);
        $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
        $priority = (int)$_POST['priority'];

        $sql = "INSERT INTO tbl_faqs (category, question, answer, keywords, priority) 
                VALUES ('$category', '$question', '$answer', '$keywords', $priority)";
        if (mysqli_query($conn, $sql)) {
            $message = 'FAQ added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    if ($action === 'update') {
        $id       = (int)$_POST['faq_id'];
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $question = mysqli_real_escape_string($conn, $_POST['question']);
        $answer   = mysqli_real_escape_string($conn, $_POST['answer']);
        $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
        $priority = (int)$_POST['priority'];
        $active   = (int)($_POST['is_active'] ?? 0);

        $sql = "UPDATE tbl_faqs SET 
                category='$category', question='$question', answer='$answer', 
                keywords='$keywords', priority=$priority, is_active=$active
                WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $message = 'FAQ updated!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    if ($action === 'delete') {
        $id = (int)$_POST['faq_id'];
        if (mysqli_query($conn, "DELETE FROM tbl_faqs WHERE id=$id")) {
            $message = 'FAQ deleted.';
            $messageType = 'success';
        }
    }
}

// ── Fetch all FAQs ─────────────────────────────────────────────────────────
$faqs = [];
$result = mysqli_query($conn, "SELECT * FROM tbl_faqs ORDER BY category, priority DESC, id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $faqs[] = $row;
}

$categories = array_unique(array_column($faqs, 'category'));
sort($categories);
?>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-question-circle"></i> FAQs Management</h3>
    <div class="pull-right">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addFaqModal">
        <i class="fa fa-plus"></i> Add FAQ
      </button>
    </div>
  </div>

  <div class="box-body">

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <div style="margin-bottom:15px;">
      <input type="text" id="faqSearch" class="form-control" 
             placeholder="🔍 Search FAQs..." style="max-width:300px; display:inline-block;">
      <select id="faqCategoryFilter" class="form-control" style="max-width:200px; display:inline-block; margin-left:10px;">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="faqsTable">
        <thead>
          <tr style="background:#f5f5f5;">
            <th width="40">#</th>
            <th width="100">Category</th>
            <th>Question</th>
            <th>Answer</th>
            <th width="120">Keywords</th>
            <th width="60">Priority</th>
            <th width="60">Active</th>
            <th width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($faqs)): ?>
            <tr><td colspan="8" class="text-center text-muted">No FAQs yet. Click "Add FAQ".</td></tr>
          <?php else: ?>
            <?php foreach ($faqs as $i => $faq): ?>
              <tr data-category="<?= htmlspecialchars($faq['category']) ?>">
                <td><?= $i + 1 ?></td>
                <td><span class="label label-info"><?= htmlspecialchars($faq['category']) ?></span></td>
                <td><strong><?= htmlspecialchars($faq['question']) ?></strong></td>
                <td><small><?= nl2br(htmlspecialchars(substr($faq['answer'], 0, 120))) ?>...</small></td>
                <td><small class="text-muted"><?= htmlspecialchars($faq['keywords']) ?></small></td>
                <td class="text-center"><?= $faq['priority'] ?></td>
                <td class="text-center">
                  <?= $faq['is_active'] 
                      ? '<span class="label label-success">Yes</span>' 
                      : '<span class="label label-default">No</span>' ?>
                </td>
                <td>
                  <button class="btn btn-xs btn-primary edit-faq"
                          data-faq='<?= htmlspecialchars(json_encode($faq), ENT_QUOTES) ?>'>
                    <i class="fa fa-edit"></i>
                  </button>
                  <form method="POST" style="display:inline;" 
                        onsubmit="return confirm('Delete this FAQ?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="faq_id" value="<?= $faq['id'] ?>">
                    <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <p class="text-muted" style="margin-top:10px;">
      <i class="fa fa-info-circle"></i> 
      Total: <strong><?= count($faqs) ?></strong> FAQs. Used by AI Chat to answer user questions.
    </p>

  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addFaqModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-plus"></i> Add New FAQ</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" class="form-control" 
                   placeholder="e.g., Appointments, Pets, Clinic Info" required list="categoryList">
            <datalist id="categoryList">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-group">
            <label>Question</label>
            <input type="text" name="question" class="form-control" required 
                   placeholder="e.g., How do I book an appointment?">
          </div>
          <div class="form-group">
            <label>Answer</label>
            <textarea name="answer" class="form-control" rows="4" required 
                      placeholder="Provide a clear, helpful answer..."></textarea>
          </div>
          <div class="form-group">
            <label>Keywords <small class="text-muted">(comma-separated)</small></label>
            <input type="text" name="keywords" class="form-control" 
                   placeholder="e.g., book, appointment, schedule">
          </div>
          <div class="form-group">
            <label>Priority <small class="text-muted">(0-10, higher = shown first)</small></label>
            <input type="number" name="priority" class="form-control" value="5" min="0" max="10">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save FAQ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editFaqModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="faq_id" id="edit_faq_id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-edit"></i> Edit FAQ</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" id="edit_category" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Question</label>
            <input type="text" name="question" id="edit_question" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Answer</label>
            <textarea name="answer" id="edit_answer" class="form-control" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Keywords</label>
            <input type="text" name="keywords" id="edit_keywords" class="form-control">
          </div>
          <div class="form-group">
            <label>Priority</label>
            <input type="number" name="priority" id="edit_priority" class="form-control" min="0" max="10">
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Active
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.edit-faq').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const faq = JSON.parse(this.dataset.faq);
      document.getElementById('edit_faq_id').value      = faq.id;
      document.getElementById('edit_category').value    = faq.category;
      document.getElementById('edit_question').value    = faq.question;
      document.getElementById('edit_answer').value      = faq.answer;
      document.getElementById('edit_keywords').value    = faq.keywords || '';
      document.getElementById('edit_priority').value    = faq.priority;
      document.getElementById('edit_is_active').checked = faq.is_active == 1;
      $('#editFaqModal').modal('show');
    });
  });

  const search    = document.getElementById('faqSearch');
  const catFilter = document.getElementById('faqCategoryFilter');
  const rows      = document.querySelectorAll('#faqsTable tbody tr');

  function filterRows() {
    const term = search.value.toLowerCase();
    const cat  = catFilter.value;
    rows.forEach(function (row) {
      if (!row.dataset.category) return;
      const text      = row.textContent.toLowerCase();
      const matchText = !term || text.includes(term);
      const matchCat  = !cat  || row.dataset.category === cat;
      row.style.display = (matchText && matchCat) ? '' : 'none';
    });
  }

  if (search)    search.addEventListener('input',  filterRows);
  if (catFilter) catFilter.addEventListener('change', filterRows);
});
</script>