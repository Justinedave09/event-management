<?php
global $dbConn;
$conn = $dbConn;

$message     = '';
$messageType = '';
$currentUser = $_SESSION['calendar_fd_user'];
$userType    = $currentUser['type'];
$userId      = $currentUser['id'];

// ════════════════════════════════════════════════════════════
// HANDLE FORM ACTIONS
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Add / Update Pet ────────────────────────────────────
    if ($action === 'add' || $action === 'update') {
        $ownerId      = (int)$_POST['owner_id'];
        $petName      = mysqli_real_escape_string($conn, $_POST['pet_name']);
        $petType      = mysqli_real_escape_string($conn, $_POST['pet_type']);
        $petBreed     = mysqli_real_escape_string($conn, $_POST['pet_breed']);
        $petGender    = mysqli_real_escape_string($conn, $_POST['pet_gender']);
        $petColor     = mysqli_real_escape_string($conn, $_POST['pet_color']);
        $petWeight    = !empty($_POST['pet_weight']) ? (float)$_POST['pet_weight'] : 'NULL';
        $petBirthdate = !empty($_POST['pet_birthdate']) 
                        ? "'" . mysqli_real_escape_string($conn, $_POST['pet_birthdate']) . "'" 
                        : 'NULL';
        $petMicrochip = mysqli_real_escape_string($conn, $_POST['pet_microchip'] ?? '');
        $allergies    = mysqli_real_escape_string($conn, $_POST['allergies'] ?? '');
        $chronic      = mysqli_real_escape_string($conn, $_POST['chronic_conditions'] ?? '');
        $notes        = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

        if ($action === 'add') {
            $sql = "INSERT INTO tbl_pets 
                    (owner_id, pet_name, pet_type, pet_breed, pet_gender, pet_color, 
                     pet_weight, pet_birthdate, pet_microchip, allergies, chronic_conditions, notes) 
                    VALUES 
                    ($ownerId, '$petName', '$petType', '$petBreed', '$petGender', '$petColor',
                     $petWeight, $petBirthdate, '$petMicrochip', '$allergies', '$chronic', '$notes')";
        } else {
            $id  = (int)$_POST['pet_id'];
            $sql = "UPDATE tbl_pets SET 
                    owner_id=$ownerId, pet_name='$petName', pet_type='$petType', 
                    pet_breed='$petBreed', pet_gender='$petGender', pet_color='$petColor',
                    pet_weight=$petWeight, pet_birthdate=$petBirthdate, pet_microchip='$petMicrochip',
                    allergies='$allergies', chronic_conditions='$chronic', notes='$notes'
                    WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            $message = 'Pet ' . ($action === 'add' ? 'added' : 'updated') . ' successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Archive Pet (Soft Delete) ───────────────────────────
    if ($action === 'delete') {
        $id = (int)$_POST['pet_id'];
        $canDelete = true;

        // Clients can only archive their own pets
        if ($userType === 'client') {
            $check = mysqli_query($conn, "SELECT owner_id FROM tbl_pets WHERE id=$id");
            $pet = mysqli_fetch_assoc($check);
            if (!$pet || $pet['owner_id'] != $userId) {
                $message = 'You can only archive your own pets.';
                $messageType = 'danger';
                $canDelete = false;
            }
        }

        if ($canDelete) {
            if (mysqli_query($conn, "UPDATE tbl_pets SET is_active=0 WHERE id=$id")) {
                $message = 'Pet archived successfully. Medical records are preserved.';
                $messageType = 'success';
            } else {
                $message = 'Error: ' . mysqli_error($conn);
                $messageType = 'danger';
            }
        }
    }

    // ── Permanent Delete (Admin Only) ───────────────────────
    if ($action === 'permanent_delete' && $userType === 'admin') {
        $id = (int)$_POST['pet_id'];
        
        // Delete related records first (foreign key safety)
        mysqli_query($conn, "DELETE FROM tbl_vaccinations WHERE pet_id=$id");
        mysqli_query($conn, "DELETE FROM tbl_medical_history WHERE pet_id=$id");
        
        if (mysqli_query($conn, "DELETE FROM tbl_pets WHERE id=$id")) {
            $message = 'Pet permanently deleted along with all medical records.';
            $messageType = 'warning';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Restore Archived Pet ────────────────────────────────
    if ($action === 'restore' && $userType !== 'client') {
        $id = (int)$_POST['pet_id'];
        if (mysqli_query($conn, "UPDATE tbl_pets SET is_active=1 WHERE id=$id")) {
            $message = 'Pet restored successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Add Medical History ─────────────────────────────────
    if ($action === 'add_medical') {
        $petId       = (int)$_POST['pet_id'];
        $visitDate   = mysqli_real_escape_string($conn, $_POST['visit_date']);
        $visitType   = mysqli_real_escape_string($conn, $_POST['visit_type']);
        $diagnosis   = mysqli_real_escape_string($conn, $_POST['diagnosis']);
        $treatment   = mysqli_real_escape_string($conn, $_POST['treatment']);
        $medications = mysqli_real_escape_string($conn, $_POST['medications']);
        $weight      = !empty($_POST['weight_at_visit']) ? (float)$_POST['weight_at_visit'] : 'NULL';
        $temperature = !empty($_POST['temperature']) ? (float)$_POST['temperature'] : 'NULL';
        $medNotes    = mysqli_real_escape_string($conn, $_POST['med_notes'] ?? '');
        $vet         = mysqli_real_escape_string($conn, $_POST['veterinarian']);
        $nextVisit   = !empty($_POST['next_visit_date']) 
                       ? "'" . mysqli_real_escape_string($conn, $_POST['next_visit_date']) . "'" 
                       : 'NULL';

        $sql = "INSERT INTO tbl_medical_history 
                (pet_id, visit_date, visit_type, diagnosis, treatment, medications, 
                 weight_at_visit, temperature, notes, veterinarian, next_visit_date, created_by) 
                VALUES 
                ($petId, '$visitDate', '$visitType', '$diagnosis', '$treatment', '$medications',
                 $weight, $temperature, '$medNotes', '$vet', $nextVisit, $userId)";

        if (mysqli_query($conn, $sql)) {
            $message = 'Medical record added!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Add Vaccination ─────────────────────────────────────
    if ($action === 'add_vaccine') {
        $petId          = (int)$_POST['pet_id'];
        $vaccineName    = mysqli_real_escape_string($conn, $_POST['vaccine_name']);
        $dateAdmin      = mysqli_real_escape_string($conn, $_POST['date_administered']);
        $nextDue        = !empty($_POST['next_due_date']) 
                          ? "'" . mysqli_real_escape_string($conn, $_POST['next_due_date']) . "'" 
                          : 'NULL';
        $batchNumber    = mysqli_real_escape_string($conn, $_POST['batch_number'] ?? '');
        $administeredBy = mysqli_real_escape_string($conn, $_POST['administered_by'] ?? '');

        $sql = "INSERT INTO tbl_vaccinations 
                (pet_id, vaccine_name, date_administered, next_due_date, batch_number, administered_by) 
                VALUES 
                ($petId, '$vaccineName', '$dateAdmin', $nextDue, '$batchNumber', '$administeredBy')";

        if (mysqli_query($conn, $sql)) {
            $message = 'Vaccination recorded!';
            $messageType = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }

    // ── Delete Medical Record (Admin Only) ──────────────────
    if ($action === 'delete_medical' && $userType === 'admin') {
        $id = (int)$_POST['record_id'];
        if (mysqli_query($conn, "DELETE FROM tbl_medical_history WHERE id=$id")) {
            $message = 'Medical record deleted.';
            $messageType = 'success';
        }
    }

    // ── Delete Vaccination (Admin Only) ─────────────────────
    if ($action === 'delete_vaccine' && $userType === 'admin') {
        $id = (int)$_POST['vaccine_id'];
        if (mysqli_query($conn, "DELETE FROM tbl_vaccinations WHERE id=$id")) {
            $message = 'Vaccination record deleted.';
            $messageType = 'success';
        }
    }
}

// ════════════════════════════════════════════════════════════
// FETCH DATA
// ════════════════════════════════════════════════════════════

// Show archived or active pets toggle
$showArchived = isset($_GET['archived']) && $userType !== 'client';
$whereClause  = $showArchived ? "p.is_active = 0" : "p.is_active = 1";

if ($userType === 'client') {
    $whereClause .= " AND p.owner_id = $userId";
}

// Fetch pets
$pets = [];
$sql  = "SELECT p.*, u.name AS owner_name, u.email AS owner_email, u.phone AS owner_phone
         FROM tbl_pets p 
         LEFT JOIN tbl_users u ON p.owner_id = u.id 
         WHERE $whereClause
         ORDER BY p.pet_name ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pets[] = $row;
    }
}

// Count archived pets for badge
$archivedCount = 0;
if ($userType !== 'client') {
    $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_pets WHERE is_active=0");
    if ($r) {
        $row = mysqli_fetch_assoc($r);
        $archivedCount = $row['c'];
    }
}

// Fetch potential owners for the dropdown
$owners = [];
if ($userType !== 'client') {
    $ownerSql = "SELECT id, name, email, phone 
                 FROM tbl_users 
                 WHERE type IN ('client', 'user', 'owner', 'pet_owner') 
                   AND status = 'active' 
                 ORDER BY name ASC";
    $r = mysqli_query($conn, $ownerSql);
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $owners[] = $row;
        }
    }
}

// Fetch selected pet's medical history if viewing details
$selectedPetId  = isset($_GET['pet']) ? (int)$_GET['pet'] : 0;
$medicalHistory = [];
$vaccinations   = [];
$selectedPet    = null;

if ($selectedPetId > 0) {
    $r = mysqli_query($conn, "SELECT p.*, u.name AS owner_name 
                              FROM tbl_pets p 
                              LEFT JOIN tbl_users u ON p.owner_id = u.id 
                              WHERE p.id = $selectedPetId");
    $selectedPet = mysqli_fetch_assoc($r);

    if ($selectedPet) {
        // Security: clients can only view their own pets
        if ($userType === 'client' && $selectedPet['owner_id'] != $userId) {
            $selectedPet = null;
        } else {
            $r = mysqli_query($conn, "SELECT * FROM tbl_medical_history 
                                      WHERE pet_id=$selectedPetId 
                                      ORDER BY visit_date DESC");
            while ($row = mysqli_fetch_assoc($r)) {
                $medicalHistory[] = $row;
            }

            $r = mysqli_query($conn, "SELECT * FROM tbl_vaccinations 
                                      WHERE pet_id=$selectedPetId 
                                      ORDER BY date_administered DESC");
            while ($row = mysqli_fetch_assoc($r)) {
                $vaccinations[] = $row;
            }
        }
    }
}

// Helper function: calculate pet age
function calcAge($birthdate) {
    if (!$birthdate || $birthdate === '0000-00-00') return 'Unknown';
    $birth = new DateTime($birthdate);
    $now   = new DateTime();
    $diff  = $now->diff($birth);
    if ($diff->y > 0) return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return $diff->m . ' mo' . ($diff->m > 1 ? 's' : '');
    return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <i class="fa fa-info-circle"></i> <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<?php if ($selectedPet): ?>
<!-- ════════════════════════════════════════════════════════════ -->
<!-- PET DETAILS VIEW                                                -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="row">
  <div class="col-md-4">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-paw"></i> <?= htmlspecialchars($selectedPet['pet_name']) ?>
        </h3>
        <div class="pull-right">
          <a href="?v=PETS" class="btn btn-xs btn-default">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-striped">
          <tr><th width="40%">Name</th><td><?= htmlspecialchars($selectedPet['pet_name']) ?></td></tr>
          <tr><th>Type</th><td><?= htmlspecialchars($selectedPet['pet_type']) ?></td></tr>
          <tr><th>Breed</th><td><?= htmlspecialchars($selectedPet['pet_breed'] ?: 'N/A') ?></td></tr>
          <tr><th>Gender</th><td><?= htmlspecialchars($selectedPet['pet_gender'] ?: 'N/A') ?></td></tr>
          <tr><th>Color</th><td><?= htmlspecialchars($selectedPet['pet_color'] ?: 'N/A') ?></td></tr>
          <tr><th>Weight</th><td><?= $selectedPet['pet_weight'] ? $selectedPet['pet_weight'] . ' kg' : 'N/A' ?></td></tr>
          <tr><th>Age</th><td><?= calcAge($selectedPet['pet_birthdate']) ?></td></tr>
          <tr><th>Microchip</th><td><?= htmlspecialchars($selectedPet['pet_microchip'] ?: 'N/A') ?></td></tr>
          <tr><th>Owner</th><td><?= htmlspecialchars($selectedPet['owner_name']) ?></td></tr>
        </table>

        <?php if (!empty($selectedPet['allergies'])): ?>
          <div class="alert alert-warning" style="padding:8px; font-size:12px;">
            <strong><i class="fa fa-exclamation-triangle"></i> Allergies:</strong>
            <?= nl2br(htmlspecialchars($selectedPet['allergies'])) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($selectedPet['chronic_conditions'])): ?>
          <div class="alert alert-info" style="padding:8px; font-size:12px;">
            <strong><i class="fa fa-heartbeat"></i> Chronic Conditions:</strong>
            <?= nl2br(htmlspecialchars($selectedPet['chronic_conditions'])) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($selectedPet['notes'])): ?>
          <div class="alert" style="padding:8px; font-size:12px; background:#f5f5f5; border:1px solid #ddd;">
            <strong><i class="fa fa-sticky-note"></i> Notes:</strong>
            <?= nl2br(htmlspecialchars($selectedPet['notes'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <!-- Medical History -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-stethoscope"></i> Medical History</h3>
        <?php if ($userType !== 'client'): ?>
        <div class="pull-right">
          <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#addMedicalModal">
            <i class="fa fa-plus"></i> Add Record
          </button>
        </div>
        <?php endif; ?>
      </div>
      <div class="box-body">
        <?php if (empty($medicalHistory)): ?>
          <p class="text-muted text-center">No medical history yet.</p>
        <?php else: ?>
          <?php foreach ($medicalHistory as $record): ?>
            <div class="panel panel-default" style="margin-bottom:10px;">
              <div class="panel-heading" style="padding:8px 12px;">
                <strong><?= date('M d, Y', strtotime($record['visit_date'])) ?></strong>
                <span class="label label-info"><?= htmlspecialchars($record['visit_type']) ?></span>
                <?php if (!empty($record['veterinarian'])): ?>
                  <small class="text-muted">by <?= htmlspecialchars($record['veterinarian']) ?></small>
                <?php endif; ?>
                <?php if ($userType === 'admin'): ?>
                <form method="POST" style="display:inline; float:right;" 
                      onsubmit="return confirm('Delete this medical record?');">
                  <input type="hidden" name="action" value="delete_medical">
                  <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
                  <button class="btn btn-xs btn-danger" style="padding:1px 5px;" title="Delete record">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
              <div class="panel-body" style="padding:10px 12px; font-size:13px;">
                <?php if (!empty($record['diagnosis'])): ?>
                  <p><strong>Diagnosis:</strong> <?= nl2br(htmlspecialchars($record['diagnosis'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($record['treatment'])): ?>
                  <p><strong>Treatment:</strong> <?= nl2br(htmlspecialchars($record['treatment'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($record['medications'])): ?>
                  <p><strong>Medications:</strong> <?= nl2br(htmlspecialchars($record['medications'])) ?></p>
                <?php endif; ?>
                <p style="font-size:11px; color:#777; margin-top:8px;">
                  <?php if ($record['weight_at_visit']): ?>
                    Weight: <?= $record['weight_at_visit'] ?>kg | 
                  <?php endif; ?>
                  <?php if ($record['temperature']): ?>
                    Temp: <?= $record['temperature'] ?>°C | 
                  <?php endif; ?>
                  <?php if (!empty($record['next_visit_date'])): ?>
                    Next visit: <?= date('M d, Y', strtotime($record['next_visit_date'])) ?>
                  <?php endif; ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Vaccinations -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-shield"></i> Vaccinations</h3>
        <?php if ($userType !== 'client'): ?>
        <div class="pull-right">
          <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#addVaccineModal">
            <i class="fa fa-plus"></i> Add Vaccine
          </button>
        </div>
        <?php endif; ?>
      </div>
      <div class="box-body">
        <?php if (empty($vaccinations)): ?>
          <p class="text-muted text-center">No vaccinations recorded.</p>
        <?php else: ?>
          <table class="table table-bordered">
            <thead>
              <tr style="background:#f5f5f5;">
                <th>Vaccine</th>
                <th>Date Given</th>
                <th>Next Due</th>
                <th>By</th>
                <?php if ($userType === 'admin'): ?>
                  <th width="50">Actions</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vaccinations as $vac): 
                $dueClass = '';
                if (!empty($vac['next_due_date'])) {
                  $days = (strtotime($vac['next_due_date']) - time()) / 86400;
                  if ($days < 0)       $dueClass = 'danger';
                  elseif ($days < 30)  $dueClass = 'warning';
                }
              ?>
              <tr class="<?= $dueClass ? 'bg-' . $dueClass : '' ?>">
                <td><strong><?= htmlspecialchars($vac['vaccine_name']) ?></strong></td>
                <td><?= date('M d, Y', strtotime($vac['date_administered'])) ?></td>
                <td>
                  <?php if (!empty($vac['next_due_date'])): ?>
                    <?= date('M d, Y', strtotime($vac['next_due_date'])) ?>
                    <?php if ($dueClass === 'danger'): ?>
                      <span class="label label-danger">OVERDUE</span>
                    <?php elseif ($dueClass === 'warning'): ?>
                      <span class="label label-warning">Soon</span>
                    <?php endif; ?>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($vac['administered_by'] ?: '—') ?></td>
                <?php if ($userType === 'admin'): ?>
                <td>
                  <form method="POST" style="display:inline;" 
                        onsubmit="return confirm('Delete this vaccination record?');">
                    <input type="hidden" name="action" value="delete_vaccine">
                    <input type="hidden" name="vaccine_id" value="<?= $vac['id'] ?>">
                    <button class="btn btn-xs btn-danger" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
                <?php endif; ?>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Add Medical Record Modal -->
<div class="modal fade" id="addMedicalModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="action" value="add_medical">
        <input type="hidden" name="pet_id" value="<?= $selectedPetId ?>">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-plus"></i> Add Medical Record</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Visit Date *</label>
              <input type="date" name="visit_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 form-group">
              <label>Visit Type *</label>
              <select name="visit_type" class="form-control" required>
                <option value="Checkup">Checkup</option>
                <option value="Vaccination">Vaccination</option>
                <option value="Surgery">Surgery</option>
                <option value="Dental">Dental</option>
                <option value="Emergency">Emergency</option>
                <option value="Grooming">Grooming</option>
                <option value="Follow-up">Follow-up</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Diagnosis</label>
            <textarea name="diagnosis" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Treatment</label>
            <textarea name="treatment" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Medications</label>
            <textarea name="medications" class="form-control" rows="2"></textarea>
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Weight (kg)</label>
              <input type="number" step="0.01" name="weight_at_visit" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Temperature (°C)</label>
              <input type="number" step="0.1" name="temperature" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Next Visit</label>
              <input type="date" name="next_visit_date" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label>Veterinarian</label>
            <input type="text" name="veterinarian" class="form-control">
          </div>
          <div class="form-group">
            <label>Additional Notes</label>
            <textarea name="med_notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Vaccination Modal -->
<div class="modal fade" id="addVaccineModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="action" value="add_vaccine">
        <input type="hidden" name="pet_id" value="<?= $selectedPetId ?>">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-plus"></i> Add Vaccination</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Vaccine Name *</label>
            <input type="text" name="vaccine_name" class="form-control" required 
                   placeholder="e.g., Rabies, DHPP, FVRCP">
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Date Administered *</label>
              <input type="date" name="date_administered" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 form-group">
              <label>Next Due Date</label>
              <input type="date" name="next_due_date" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label>Batch Number</label>
            <input type="text" name="batch_number" class="form-control">
          </div>
          <div class="form-group">
            <label>Administered By</label>
            <input type="text" name="administered_by" class="form-control" placeholder="Dr. Name">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Vaccination</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php else: ?>
<!-- ════════════════════════════════════════════════════════════ -->
<!-- PETS LIST VIEW                                                  -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-paw"></i> 
      <?php if ($userType === 'client'): ?>
        My Pets
      <?php elseif ($showArchived): ?>
        Archived Pets
      <?php else: ?>
        Pets Management
      <?php endif; ?>
    </h3>
    <div class="pull-right">
      <?php if (!$showArchived): ?>
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addPetModal">
        <i class="fa fa-plus"></i> Add Pet
      </button>
      <?php endif; ?>
    </div>
  </div>

  <div class="box-body">

    <div style="margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
      <input type="text" id="petSearch" class="form-control" 
             placeholder="🔍 Search pets..." style="max-width:300px;">
      
      <?php if ($userType !== 'client'): ?>
      <div>
        <?php if ($showArchived): ?>
          <a href="?v=PETS" class="btn btn-sm btn-success">
            <i class="fa fa-eye"></i> Show Active Pets
          </a>
        <?php else: ?>
          <a href="?v=PETS&archived=1" class="btn btn-sm btn-default">
            <i class="fa fa-archive"></i> Show Archived
            <?php if ($archivedCount > 0): ?>
              <span class="badge"><?= $archivedCount ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <?php if (empty($pets)): ?>
      <p class="text-center text-muted" style="padding:30px;">
        <i class="fa fa-paw fa-3x" style="color:#ddd; display:block; margin-bottom:10px;"></i>
        <?= $showArchived ? 'No archived pets.' : 'No pets registered yet. Click "Add Pet" to get started.' ?>
      </p>
    <?php else: ?>
      <div class="row" id="petsList">
<?php foreach ($pets as $pet): ?>
<div class="col-md-4 pet-card" data-search="<?= htmlspecialchars(strtolower($pet['pet_name'] . ' ' . $pet['pet_type'] . ' ' . $pet['pet_breed'] . ' ' . $pet['owner_name'])) ?>">
  <div class="box box-widget widget-user-2" style="margin-bottom:15px;">
    
    <div class="widget-user-header" style="background:<?= $showArchived ? '#999' : '#3c8dbc' ?>; color:#fff; padding:12px;">
      <h3 class="widget-user-username" style="margin:0;">
        <i class="fa fa-paw"></i> <?= htmlspecialchars($pet['pet_name']) ?>
        <?php if ($showArchived): ?>
          <span class="label label-warning pull-right">ARCHIVED</span>
        <?php endif; ?>
      </h3>
      <h5 class="widget-user-desc" style="margin:5px 0 0;">
        <?= htmlspecialchars($pet['pet_type']) ?>
        <?= $pet['pet_breed'] ? ' • ' . htmlspecialchars($pet['pet_breed']) : '' ?>
      </h5>
    </div>
    
    <div class="box-footer no-padding">
      <ul class="nav nav-stacked" style="margin:0;">
        <li><a><strong>Owner:</strong> <span class="pull-right"><?= htmlspecialchars($pet['owner_name']) ?></span></a></li>
        <li><a><strong>Age:</strong> <span class="pull-right"><?= calcAge($pet['pet_birthdate']) ?></span></a></li>
        <li><a><strong>Gender:</strong> <span class="pull-right"><?= htmlspecialchars($pet['pet_gender'] ?: 'N/A') ?></span></a></li>
        <li><a><strong>Weight:</strong> <span class="pull-right"><?= $pet['pet_weight'] ? $pet['pet_weight'].'kg' : 'N/A' ?></span></a></li>
      </ul>
    </div>
    
    <!-- ACTION BUTTONS -->
    <div style="padding:10px; background:#f9f9f9; border-top:1px solid #eee; display:flex; flex-wrap:wrap; gap:5px;">
      
      <?php if ($showArchived): ?>
        <!-- ═══ ARCHIVED PET ACTIONS ═══ -->
        <button type="button" class="btn btn-sm btn-success restore-pet"
                data-pet-id="<?= $pet['id'] ?>"
                data-pet-name="<?= htmlspecialchars($pet['pet_name'], ENT_QUOTES) ?>">
          <i class="fa fa-undo"></i> Restore
        </button>
        
        <?php if ($userType === 'admin'): ?>
        <button type="button" class="btn btn-sm btn-danger permanent-delete"
                data-pet-id="<?= $pet['id'] ?>"
                data-pet-name="<?= htmlspecialchars($pet['pet_name'], ENT_QUOTES) ?>">
          <i class="fa fa-trash"></i> Delete Forever
        </button>
        <?php endif; ?>
        
      <?php else: ?>
        <!-- ═══ ACTIVE PET ACTIONS ═══ -->
        <a href="?v=PETS&pet=<?= $pet['id'] ?>" class="btn btn-sm btn-info">
          <i class="fa fa-eye"></i> View
        </a>
        
        <button type="button" class="btn btn-sm btn-primary edit-pet"
                data-pet='<?= htmlspecialchars(json_encode($pet), ENT_QUOTES) ?>'>
          <i class="fa fa-edit"></i> Edit
        </button>
        
        <button type="button" class="btn btn-sm btn-warning archive-pet"
                data-pet-id="<?= $pet['id'] ?>"
                data-pet-name="<?= htmlspecialchars($pet['pet_name'], ENT_QUOTES) ?>"
                title="Archive (hides but keeps records)">
          <i class="fa fa-archive"></i> Archive
        </button>
        
        <?php if ($userType === 'admin'): ?>
        <button type="button" class="btn btn-sm btn-danger permanent-delete"
                data-pet-id="<?= $pet['id'] ?>"
                data-pet-name="<?= htmlspecialchars($pet['pet_name'], ENT_QUOTES) ?>"
                title="Delete permanently with all records">
          <i class="fa fa-trash"></i> Delete
        </button>
        <?php endif; ?>
      <?php endif; ?>
      
    </div>
  </div>
</div>
<?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p class="text-muted" style="margin-top:10px;">
      <i class="fa fa-info-circle"></i> 
      Showing <strong><?= count($pets) ?></strong> <?= $showArchived ? 'archived' : 'active' ?> pet(s)
    </p>
  </div>
</div>

<!-- Add/Edit Pet Modal -->
<div class="modal fade" id="addPetModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="petForm">
        <input type="hidden" name="action" value="add" id="petAction">
        <input type="hidden" name="pet_id" id="pet_id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="petModalTitle"><i class="fa fa-plus"></i> Add New Pet</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Pet Name *</label>
              <input type="text" name="pet_name" id="pet_name" class="form-control" required>
            </div>
            <div class="col-md-6 form-group">
              <label>Owner *</label>
              <?php if ($userType === 'client'): ?>
                <input type="hidden" name="owner_id" value="<?= $userId ?>">
                <input type="text" class="form-control" value="<?= htmlspecialchars($currentUser['name']) ?>" readonly>
              <?php else: ?>
                <select name="owner_id" id="owner_id" class="form-control" required>
                  <option value="">— Select Owner —</option>
                  <?php if (empty($owners)): ?>
                    <option value="" disabled>No pet owners found — add users first</option>
                  <?php else: ?>
                    <?php foreach ($owners as $o): ?>
                      <option value="<?= $o['id'] ?>">
                        <?= htmlspecialchars($o['name']) ?>
                        <?php if (!empty($o['phone'])): ?>
                          — <?= htmlspecialchars($o['phone']) ?>
                        <?php endif; ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <small class="text-muted"><?= count($owners) ?> pet owner(s) available</small>
              <?php endif; ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Type *</label>
              <select name="pet_type" id="pet_type" class="form-control" required>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Bird">Bird</option>
                <option value="Rabbit">Rabbit</option>
                <option value="Hamster">Hamster</option>
                <option value="Fish">Fish</option>
                <option value="Reptile">Reptile</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>Breed</label>
              <input type="text" name="pet_breed" id="pet_breed" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Gender</label>
              <select name="pet_gender" id="pet_gender" class="form-control">
                <option value="">—</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Color</label>
              <input type="text" name="pet_color" id="pet_color" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Weight (kg)</label>
              <input type="number" step="0.01" name="pet_weight" id="pet_weight" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Birthdate</label>
              <input type="date" name="pet_birthdate" id="pet_birthdate" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label>Microchip ID</label>
            <input type="text" name="pet_microchip" id="pet_microchip" class="form-control">
          </div>
          <div class="form-group">
            <label>Allergies</label>
            <textarea name="allergies" id="allergies" class="form-control" rows="2"
                      placeholder="e.g., Chicken, Pollen, Specific medications"></textarea>
          </div>
          <div class="form-group">
            <label>Chronic Conditions</label>
            <textarea name="chronic_conditions" id="chronic_conditions" class="form-control" rows="2"
                      placeholder="e.g., Diabetes, Arthritis, Heart condition"></textarea>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="2"
                      placeholder="Any other important information"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Pet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Hidden Action Forms -->
<form id="archiveForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="pet_id" id="archive_pet_id">
</form>

<form id="restoreForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="restore">
  <input type="hidden" name="pet_id" id="restore_pet_id">
</form>

<?php if ($userType === 'admin'): ?>
<form id="permanentDeleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="permanent_delete">
  <input type="hidden" name="pet_id" id="permanent_delete_pet_id">
</form>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ════════════════════════════════════════════════════════
  // EDIT PET - Fill modal with data
  // ════════════════════════════════════════════════════════
  document.querySelectorAll('.edit-pet').forEach(function (btn) {
    btn.addEventListener('click', function () {
      try {
        const pet = JSON.parse(this.dataset.pet);
        document.getElementById('petAction').value = 'update';
        document.getElementById('pet_id').value = pet.id;
        document.getElementById('pet_name').value = pet.pet_name || '';
        
        const ownerSel = document.getElementById('owner_id');
        if (ownerSel) ownerSel.value = pet.owner_id;
        
        document.getElementById('pet_type').value = pet.pet_type || 'Dog';
        document.getElementById('pet_breed').value = pet.pet_breed || '';
        document.getElementById('pet_gender').value = pet.pet_gender || '';
        document.getElementById('pet_color').value = pet.pet_color || '';
        document.getElementById('pet_weight').value = pet.pet_weight || '';
        document.getElementById('pet_birthdate').value = pet.pet_birthdate || '';
        document.getElementById('pet_microchip').value = pet.pet_microchip || '';
        document.getElementById('allergies').value = pet.allergies || '';
        document.getElementById('chronic_conditions').value = pet.chronic_conditions || '';
        document.getElementById('notes').value = pet.notes || '';
        document.getElementById('petModalTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Pet';
        
        // Use jQuery if available, otherwise show manually
        if (typeof $ !== 'undefined' && $.fn.modal) {
          $('#addPetModal').modal('show');
        } else {
          document.getElementById('addPetModal').style.display = 'block';
        }
      } catch (e) {
        alert('Error loading pet data: ' + e.message);
      }
    });
  });

  // Reset modal on close (jQuery only)
  if (typeof $ !== 'undefined' && $.fn.modal) {
    $('#addPetModal').on('hidden.bs.modal', function () {
      document.getElementById('petForm').reset();
      document.getElementById('petAction').value = 'add';
      document.getElementById('pet_id').value = '';
      document.getElementById('petModalTitle').innerHTML = '<i class="fa fa-plus"></i> Add New Pet';
    });
  }

  // ════════════════════════════════════════════════════════
  // ARCHIVE PET (Soft Delete)
  // ════════════════════════════════════════════════════════
  document.querySelectorAll('.archive-pet').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const petId   = this.dataset.petId;
      const petName = this.dataset.petName;
      
      const confirmed = confirm(
        'Archive "' + petName + '"?\n\n' +
        '✓ Pet will be hidden from active list\n' +
        '✓ All medical records will be preserved\n' +
        '✓ Can be restored later\n\n' +
        'Continue?'
      );
      
      if (confirmed) {
        document.getElementById('archive_pet_id').value = petId;
        document.getElementById('archiveForm').submit();
      }
    });
  });

  // ════════════════════════════════════════════════════════
  // RESTORE PET
  // ════════════════════════════════════════════════════════
  document.querySelectorAll('.restore-pet').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const petId   = this.dataset.petId;
      const petName = this.dataset.petName;
      
      if (confirm('Restore "' + petName + '" to active pets?')) {
        document.getElementById('restore_pet_id').value = petId;
        document.getElementById('restoreForm').submit();
      }
    });
  });

  // ════════════════════════════════════════════════════════
  // PERMANENT DELETE (Admin Only)
  // ════════════════════════════════════════════════════════
  document.querySelectorAll('.permanent-delete').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const petId   = this.dataset.petId;
      const petName = this.dataset.petName;
      
      // First warning
      const firstConfirm = confirm(
        '⚠️ PERMANENT DELETE WARNING\n\n' +
        'You are about to permanently delete:\n' +
        '"' + petName + '"\n\n' +
        'This will REMOVE:\n' +
        '• Pet profile\n' +
        '• All medical history records\n' +
        '• All vaccination records\n\n' +
        '❌ This action CANNOT be undone!\n\n' +
        'Click OK to continue to the next confirmation.'
      );
      
      if (!firstConfirm) return;
      
      // Second confirmation - type pet's name
      const confirmName = prompt(
        'FINAL CONFIRMATION\n\n' +
        'To permanently delete this pet, type its name exactly:\n\n' +
        '"' + petName + '"\n\n' +
        '(Case insensitive)'
      );
      
      if (confirmName === null) return; // cancelled
      
      if (confirmName.trim().toLowerCase() !== petName.trim().toLowerCase()) {
        alert('❌ Pet name does not match.\n\nDeletion cancelled for safety.');
        return;
      }
      
      // Submit the form
      document.getElementById('permanent_delete_pet_id').value = petId;
      document.getElementById('permanentDeleteForm').submit();
    });
  });

  // ════════════════════════════════════════════════════════
  // SEARCH FILTER
  // ════════════════════════════════════════════════════════
  const search = document.getElementById('petSearch');
  if (search) {
    search.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      document.querySelectorAll('.pet-card').forEach(function (card) {
        const data = card.dataset.search || '';
        card.style.display = !term || data.includes(term) ? '' : 'none';
      });
    });
  }
  
  // Debug log
  console.log('[Pets] Initialized:', {
    edit:   document.querySelectorAll('.edit-pet').length,
    archive: document.querySelectorAll('.archive-pet').length,
    delete: document.querySelectorAll('.permanent-delete').length,
    restore: document.querySelectorAll('.restore-pet').length
  });
});
</script>
<?php endif; ?>