<?php
// Check if user is admin - only admins can create staff accounts
$user_type = $_SESSION['calendar_fd_user']['type'];
if ($user_type !== 'admin') {
    echo "<div class='alert alert-danger'>Access denied. Only administrators can create staff accounts.</div>";
    return;
}

// Get staff records
$staff_records = getStaffRecords();
?>

<div class="row">
  <!-- Staff List -->
  <div class="col-md-8">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Staff Members</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 10px">#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php
          $idx = 1;
          foreach($staff_records as $rec) {
            extract($rec);
            $stat = '';
            if($status == "active") {$stat = 'success';}
            else if ($status == "inactive") {$stat = 'danger';}
            
            $role_display = ($type == 'admin') ? 'Administrator' : 'Veterinary Staff';
            $role_icon = ($type == 'admin') ? 'fa-user-shield' : 'fa-user-md';
          ?>
          <tr>
            <td><?php echo $idx++; ?></td>
            <td>
              <i class="fa <?php echo $role_icon; ?>" aria-hidden="true"></i>&nbsp;&nbsp;
              <?php echo strtoupper($user_name); ?>
            </td>
            <td><?php echo $user_email; ?></td>
            <td><?php echo $user_phone; ?></td>
            <td><?php echo $role_display; ?></td>
            <td><span class="label label-<?php echo $stat; ?>"><?php echo strtoupper($status); ?></span></td>
            <td>
              <?php if($status == "active") { ?>
                <a href="javascript:changeStatus('<?php echo $user_id ?>', 'inactive');">Deactivate</a>
              <?php } else { ?>
                <a href="javascript:changeStatus('<?php echo $user_id ?>', 'active');">Activate</a>
              <?php } ?>
            </td>
          </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>

  <!-- Staff Registration Form -->
  <div class="col-md-4">
  
<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><b>Staff Registration</b></h3>
  </div>
  <!-- /.box-header -->
  <!-- form start -->
  <form role="form" action="<?php echo WEB_ROOT; ?>views/process.php?cmd=createstaff" method="post">
    <div class="box-body">
      <div class="form-group">
        <label for="staff_name">Staff Name</label>
        <span id="sprytf_staff_name">
		<input type="text" name="name" class="form-control input-sm" placeholder="Staff Member Name">
		<span class="textfieldRequiredMsg">Name is required.</span>
		<span class="textfieldMinCharsMsg">Name must specify at least 6 characters.</span>
		</span>
      </div>
	  
	  <div class="form-group">
        <label for="staff_address">Address</label>
		<span id="sprytf_staff_address">
        <textarea name="address" class="form-control input-sm" placeholder="Address"></textarea>
		<span class="textareaRequiredMsg">Address is required.</span>
		<span class="textareaMinCharsMsg">Address must specify at least 10 characters.</span>	
		</span>
      </div>
	  
	  <div class="form-group">
        <label for="staff_phone">Phone</label>
		<span id="sprytf_staff_phone">
        <input type="text" name="phone" class="form-control input-sm"  placeholder="Phone number">
		<span class="textfieldRequiredMsg">Phone number is required.</span>
		</span>
      </div>
	  
	  <div class="form-group">
        <label for="staff_email">Email address</label>
		<span id="sprytf_staff_email">
        <input type="email" name="email" class="form-control input-sm" placeholder="Enter email">
		<span class="textfieldRequiredMsg">Email ID is required.</span>
		<span class="textfieldInvalidFormatMsg">Please enter a valid email (user@domain.com).</span>
		</span>
      </div>

      <div class="form-group">
        <label for="staff_password">Password</label>
		<span id="sprytf_staff_password">
        <input type="password" name="pwd" class="form-control input-sm" placeholder="Enter password">
		<span class="textfieldRequiredMsg">Password is required.</span>
		<span class="textfieldMinCharsMsg">Password must be at least 6 characters.</span>
		</span>
      </div>

      <div class="form-group">
        <label for="staff_type">Staff Type</label>
		<span id="sprytf_staff_type">
        <select name="type" class="form-control input-sm">
			<option value=""> -- select staff type --</option>
			<option value="staff">Veterinary Staff</option>
			<option value="admin">Administrator</option>
		</select>
		<span class="selectRequiredMsg">Please select staff type.</span>
		</span>
      </div>
	  		  
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
      <button type="submit" class="btn btn-success">Create Staff Account</button>
      <a href="<?php echo WEB_ROOT; ?>views/?v=USERS" class="btn btn-default">Cancel</a>
    </div>
  </form>
</div>
<!-- /.box -->

<script type="text/javascript">
<!--
var sprytf_staff_name 	= new Spry.Widget.ValidationTextField("sprytf_staff_name", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_staff_address = new Spry.Widget.ValidationTextarea("sprytf_staff_address", {minChars:10, isRequired:true, validateOn:["blur", "change"]});
var sprytf_staff_phone 	= new Spry.Widget.ValidationTextField("sprytf_staff_phone", 'none', {validateOn:["blur", "change"]});
var sprytf_staff_email 	= new Spry.Widget.ValidationTextField("sprytf_staff_email", 'email', {validateOn:["blur", "change"]});
var sprytf_staff_password = new Spry.Widget.ValidationTextField("sprytf_staff_password", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_staff_type 	= new Spry.Widget.ValidationSelect("sprytf_staff_type");
//-->
</script>

<script language="javascript">
function changeStatus(userId, action) {
	var actionText = (action == 'active') ? 'activate' : 'deactivate';
	if(confirm('Are you sure you want to ' + actionText + ' this staff member?')) {
		window.location.href = '<?php echo WEB_ROOT; ?>views/process.php?cmd=change&action=' + action + '&userId=' + userId;
	}
}
</script>

</div>
</div>