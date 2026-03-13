<?php
// Check if user is admin - only admins can edit staff accounts
$current_user_type = $_SESSION['calendar_fd_user']['type'];
if ($current_user_type !== 'admin') {
    header('Location: ' . WEB_ROOT . 'views/?v=STAFF&err=' . urlencode('Access denied. Only administrators can edit staff accounts.'));
    exit();
}

// Get staff details for editing
$userId = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;

if ($userId == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=STAFF&err=' . urlencode('Invalid staff ID'));
    exit();
}

// Get staff details (only staff and admin types)
$sql = "SELECT * FROM tbl_users WHERE id = $userId AND type IN ('staff', 'admin')";
$result = dbQuery($sql);

if (dbNumRows($result) == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=STAFF&err=' . urlencode('Staff member not found'));
    exit();
}

$staff = dbFetchAssoc($result);
extract($staff);

// Handle messages
$errorMessage = isset($_GET['err']) ? $_GET['err'] : '';
$successMessage = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="col-md-8 col-md-offset-2">
  <div class="box box-success edit-staff-form">
    <div class="box-header with-border">
      <h3 class="box-title">
        <i class="fa fa-user-md"></i> Edit Staff Member
      </h3>
      <div class="box-tools pull-right">
        <a href="<?php echo WEB_ROOT; ?>views/?v=STAFF" class="btn btn-default btn-sm">
          <i class="fa fa-arrow-left"></i> Back to Staff
        </a>
      </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" action="<?php echo WEB_ROOT; ?>views/process.php?cmd=updateStaff" method="post">
      <input type="hidden" name="userId" value="<?php echo $id; ?>">
      
      <div class="box-body">
        <?php if($errorMessage != '') { ?>
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> <?php echo $errorMessage; ?>
        </div>
        <?php } ?>
        
        <?php if($successMessage != '') { ?>
        <div class="alert alert-success">
          <i class="fa fa-check-circle"></i> <?php echo $successMessage; ?>
        </div>
        <?php } ?>
        
        <!-- Staff Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-user-md"></i> Staff Information
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Staff Name</label>
                <span id="sprytf_staff_name">
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                <span class="textfieldRequiredMsg">Name is required.</span>
                <span class="textfieldMinCharsMsg">Name must be at least 6 characters.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <span id="sprytf_staff_phone">
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                <span class="textfieldRequiredMsg">Phone number is required.</span>
                </span>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email Address</label>
                <span id="sprytf_staff_email">
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="textfieldRequiredMsg">Email address is required.</span>
                <span class="textfieldInvalidFormatMsg">Please enter a valid email address.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Account Status</label>
                <select name="status" class="form-control" required>
                  <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                  <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="address">Address</label>
            <span id="sprytf_staff_address">
            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
            <span class="textareaRequiredMsg">Address is required.</span>
            <span class="textareaMinCharsMsg">Address must be at least 10 characters.</span>
            </span>
          </div>
        </div>

        <!-- Role & Security Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-shield-alt"></i> Role & Security
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="type">Staff Role</label>
                <span id="sprytf_staff_type">
                <select name="type" class="form-control" required>
                  <option value="staff" <?php echo ($type == 'staff') ? 'selected' : ''; ?>>Veterinary Staff</option>
                  <option value="admin" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                </select>
                <span class="selectRequiredMsg">Please select staff role.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="pwd">New Password (Optional)</label>
                <input type="password" name="pwd" class="form-control" placeholder="Leave blank to keep current password">
                <small class="text-muted">Enter a new password only if you want to change it.</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-info-circle"></i> Account Information
          </h4>
          
          <div class="row">
            <div class="col-md-4">
              <div class="info-item">
                <strong>Staff ID:</strong>
                <span class="badge badge-primary">#<?php echo $id; ?></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-item">
                <strong>Account Created:</strong>
                <span><?php echo date('M j, Y', strtotime($bdate)); ?></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-item">
                <strong>Current Role:</strong>
                <span class="badge badge-<?php echo ($type == 'admin') ? 'danger' : 'info'; ?>">
                  <i class="fa <?php echo ($type == 'admin') ? 'fa-user-shield' : 'fa-user-md'; ?>"></i>
                  <?php echo ($type == 'admin') ? 'Administrator' : 'Veterinary Staff'; ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Permissions Notice -->
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i>
          <strong>Role Permissions:</strong>
          <ul class="mb-0 mt-2">
            <li><strong>Veterinary Staff:</strong> Can manage appointments, view client information, and send reminders</li>
            <li><strong>Administrator:</strong> Full system access including staff management and system settings</li>
          </ul>
        </div>
      </div>
      <!-- /.box-body -->
      
      <div class="box-footer">
        <div class="row">
          <div class="col-md-6">
            <a href="<?php echo WEB_ROOT; ?>views/?v=STAFF" class="btn btn-default">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-success">
              <i class="fa fa-save"></i> Update Staff Member
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- /.box -->
</div>

<script type="text/javascript">
<!--
var sprytf_staff_name = new Spry.Widget.ValidationTextField("sprytf_staff_name", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_staff_phone = new Spry.Widget.ValidationTextField("sprytf_staff_phone", 'none', {validateOn:["blur", "change"]});
var sprytf_staff_email = new Spry.Widget.ValidationTextField("sprytf_staff_email", 'email', {validateOn:["blur", "change"]});
var sprytf_staff_address = new Spry.Widget.ValidationTextarea("sprytf_staff_address", {minChars:10, isRequired:true, validateOn:["blur", "change"]});
var sprytf_staff_type = new Spry.Widget.ValidationSelect("sprytf_staff_type");
//-->
</script>