<?php
// Get user details for editing
$userId = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;

if ($userId == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=USERS&err=' . urlencode('Invalid user ID'));
    exit();
}

// Get user details
$sql = "SELECT * FROM tbl_users WHERE id = $userId";
$result = dbQuery($sql);

if (dbNumRows($result) == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=USERS&err=' . urlencode('User not found'));
    exit();
}

$user = dbFetchAssoc($result);
extract($user);

// Handle messages
$errorMessage = isset($_GET['err']) ? $_GET['err'] : '';
$successMessage = isset($_GET['msg']) ? $_GET['msg'] : '';

// Check if current user can edit this user
$current_user_type = $_SESSION['calendar_fd_user']['type'];
if ($current_user_type !== 'admin' && $current_user_type !== 'staff') {
    header('Location: ' . WEB_ROOT . 'views/?v=USERS&err=' . urlencode('Access denied'));
    exit();
}
?>

<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="col-md-8 col-md-offset-2">
  <div class="box box-primary edit-user-form">
    <div class="box-header with-border">
      <h3 class="box-title">
        <i class="fa fa-user-edit"></i> Edit User Details
      </h3>
      <div class="box-tools pull-right">
        <a href="<?php echo WEB_ROOT; ?>views/?v=USERS" class="btn btn-default btn-sm">
          <i class="fa fa-arrow-left"></i> Back to Users
        </a>
      </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" action="<?php echo WEB_ROOT; ?>views/process.php?cmd=updateUser" method="post">
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
        
        <!-- User Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-user"></i> User Information
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Full Name</label>
                <span id="sprytf_name">
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                <span class="textfieldRequiredMsg">Name is required.</span>
                <span class="textfieldMinCharsMsg">Name must be at least 6 characters.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <span id="sprytf_phone">
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
                <span id="sprytf_email">
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
                  <option value="lock" <?php echo ($status == 'lock') ? 'selected' : ''; ?>>Locked</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="address">Address</label>
            <span id="sprytf_address">
            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
            <span class="textareaRequiredMsg">Address is required.</span>
            <span class="textareaMinCharsMsg">Address must be at least 10 characters.</span>
            </span>
          </div>
        </div>

        <!-- Account Settings Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-cog"></i> Account Settings
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="type">User Type</label>
                <?php if ($current_user_type == 'admin') { ?>
                <select name="type" class="form-control" required>
                  <option value="client" <?php echo ($type == 'client') ? 'selected' : ''; ?>>Pet Owner (Client)</option>
                  <option value="staff" <?php echo ($type == 'staff') ? 'selected' : ''; ?>>Veterinary Staff</option>
                  <option value="admin" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                </select>
                <?php } else { ?>
                <input type="text" class="form-control" value="<?php echo ucfirst($type == 'client' ? 'Pet Owner' : $type); ?>" readonly>
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <small class="text-muted">Only administrators can change user types.</small>
                <?php } ?>
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

        <!-- Account Statistics Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-info-circle"></i> Account Information
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="info-item">
                <strong>Account Created:</strong>
                <span><?php echo date('F j, Y g:i A', strtotime($bdate)); ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-item">
                <strong>User ID:</strong>
                <span>#<?php echo $id; ?></span>
              </div>
            </div>
          </div>
          
          <?php if ($type == 'client') {
            // Get appointment count for clients
            $appointmentSql = "SELECT COUNT(*) as total FROM tbl_appointments WHERE uid = $id";
            $appointmentResult = dbQuery($appointmentSql);
            $appointmentCount = 0;
            if (dbNumRows($appointmentResult) > 0) {
              $appointmentRow = dbFetchAssoc($appointmentResult);
              $appointmentCount = $appointmentRow['total'];
            }
          ?>
          <div class="row">
            <div class="col-md-6">
              <div class="info-item">
                <strong>Total Appointments:</strong>
                <span class="badge badge-info"><?php echo $appointmentCount; ?></span>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
      <!-- /.box-body -->
      
      <div class="box-footer">
        <div class="row">
          <div class="col-md-6">
            <a href="<?php echo WEB_ROOT; ?>views/?v=USERS" class="btn btn-default">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Update User
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
var sprytf_name = new Spry.Widget.ValidationTextField("sprytf_name", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_phone = new Spry.Widget.ValidationTextField("sprytf_phone", 'none', {validateOn:["blur", "change"]});
var sprytf_email = new Spry.Widget.ValidationTextField("sprytf_email", 'email', {validateOn:["blur", "change"]});
var sprytf_address = new Spry.Widget.ValidationTextarea("sprytf_address", {minChars:10, isRequired:true, validateOn:["blur", "change"]});
//-->
</script>