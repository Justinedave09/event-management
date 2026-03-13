<?php
// Get appointment details for editing
$appointmentId = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;

if ($appointmentId == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=LIST&err=' . urlencode('Invalid appointment ID'));
    exit();
}

// Get appointment and user details
$sql = "SELECT u.id as user_id, u.name, u.address, u.phone, u.email, 
               a.id as appointment_id, a.pet_name, a.pet_type, a.pet_breed, 
               a.appointment_date, a.appointment_type, a.status, a.comments
        FROM tbl_users u, tbl_appointments a 
        WHERE u.id = a.uid AND u.id = $appointmentId";
$result = dbQuery($sql);

if (dbNumRows($result) == 0) {
    header('Location: ' . WEB_ROOT . 'views/?v=LIST&err=' . urlencode('Appointment not found'));
    exit();
}

$appointment = dbFetchAssoc($result);
extract($appointment);

// Split appointment_date into date and time
$appointmentDateTime = new DateTime($appointment_date);
$appointmentDateOnly = $appointmentDateTime->format('Y-m-d');
$appointmentTimeOnly = $appointmentDateTime->format('H:i');

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
  <div class="box box-primary edit-appointment-form">
    <div class="box-header with-border">
      <h3 class="box-title">
        <i class="fa fa-edit"></i> Edit Appointment Details
      </h3>
      <div class="box-tools pull-right">
        <a href="<?php echo WEB_ROOT; ?>views/?v=LIST" class="btn btn-default btn-sm">
          <i class="fa fa-arrow-left"></i> Back to List
        </a>
      </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=updateAppointment" method="post">
      <input type="hidden" name="appointmentId" value="<?php echo $appointment_id; ?>">
      <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
      
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
        
        <!-- Client Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-user"></i> Client Information
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Pet Owner Name</label>
                <span id="sprytf_name">
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                <span class="textfieldRequiredMsg">Pet owner name is required.</span>
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
                <label for="address">Address</label>
                <span id="sprytf_address">
                <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
                <span class="textareaRequiredMsg">Address is required.</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Pet Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-paw"></i> Pet Information
          </h4>
          
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="pet_name">Pet Name</label>
                <span id="sprytf_pet_name">
                <input type="text" name="pet_name" class="form-control" value="<?php echo htmlspecialchars($pet_name); ?>" required>
                <span class="textfieldRequiredMsg">Pet name is required.</span>
                </span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="pet_type">Pet Type</label>
                <span id="sprytf_pet_type">
                <select name="pet_type" class="form-control" required>
                  <option value="">--select pet type--</option>
                  <option value="Dog" <?php echo ($pet_type == 'Dog') ? 'selected' : ''; ?>>Dog</option>
                  <option value="Cat" <?php echo ($pet_type == 'Cat') ? 'selected' : ''; ?>>Cat</option>
                  <option value="Bird" <?php echo ($pet_type == 'Bird') ? 'selected' : ''; ?>>Bird</option>
                  <option value="Rabbit" <?php echo ($pet_type == 'Rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                  <option value="Hamster" <?php echo ($pet_type == 'Hamster') ? 'selected' : ''; ?>>Hamster</option>
                  <option value="Fish" <?php echo ($pet_type == 'Fish') ? 'selected' : ''; ?>>Fish</option>
                  <option value="Reptile" <?php echo ($pet_type == 'Reptile') ? 'selected' : ''; ?>>Reptile</option>
                  <option value="Other" <?php echo ($pet_type == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <span class="selectRequiredMsg">Pet type is required.</span>
                </span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="pet_breed">Pet Breed (Optional)</label>
                <input type="text" name="pet_breed" class="form-control" value="<?php echo htmlspecialchars($pet_breed); ?>" placeholder="Pet's breed (optional)">
              </div>
            </div>
          </div>
        </div>

        <!-- Appointment Information Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-calendar"></i> Appointment Information
          </h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="appointment_type">Appointment Type</label>
                <span id="sprytf_appointment_type">
                <select name="appointment_type" class="form-control" required>
                  <option value="">--select appointment type--</option>
                  <option value="General Checkup" <?php echo ($appointment_type == 'General Checkup') ? 'selected' : ''; ?>>General Checkup</option>
                  <option value="Vaccination" <?php echo ($appointment_type == 'Vaccination') ? 'selected' : ''; ?>>Vaccination</option>
                  <option value="Surgery" <?php echo ($appointment_type == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                  <option value="Dental Care" <?php echo ($appointment_type == 'Dental Care') ? 'selected' : ''; ?>>Dental Care</option>
                  <option value="Emergency" <?php echo ($appointment_type == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                  <option value="Follow-up" <?php echo ($appointment_type == 'Follow-up') ? 'selected' : ''; ?>>Follow-up</option>
                  <option value="Grooming" <?php echo ($appointment_type == 'Grooming') ? 'selected' : ''; ?>>Grooming</option>
                  <option value="Consultation" <?php echo ($appointment_type == 'Consultation') ? 'selected' : ''; ?>>Consultation</option>
                </select>
                <span class="selectRequiredMsg">Appointment type is required.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Appointment Status</label>
                <select name="status" class="form-control" required>
                  <option value="PENDING" <?php echo ($status == 'PENDING') ? 'selected' : ''; ?>>Pending</option>
                  <option value="APPROVED" <?php echo ($status == 'APPROVED') ? 'selected' : ''; ?>>Approved</option>
                  <option value="DENIED" <?php echo ($status == 'DENIED') ? 'selected' : ''; ?>>Denied</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Appointment Date</label>
                <span id="sprytf_rdate">
                <input type="date" name="rdate" class="form-control" value="<?php echo $appointmentDateOnly; ?>" required min="<?php echo date('Y-m-d'); ?>">
                <span class="textfieldRequiredMsg">Date is required.</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Appointment Time</label>
                <span id="sprytf_rtime">
                <input type="time" name="rtime" class="form-control" value="<?php echo $appointmentTimeOnly; ?>" required min="08:00" max="18:00" step="900">
                <span class="textfieldRequiredMsg">Time is required.</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Comments Section -->
        <div class="form-section">
          <h4 class="section-title">
            <i class="fa fa-comment"></i> Additional Notes
          </h4>
          
          <div class="form-group">
            <label for="comments">Comments (Optional)</label>
            <textarea name="comments" class="form-control" rows="3" placeholder="Any additional notes or special instructions..."><?php echo htmlspecialchars($comments); ?></textarea>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
      
      <div class="box-footer">
        <div class="row">
          <div class="col-md-6">
            <a href="<?php echo WEB_ROOT; ?>views/?v=LIST" class="btn btn-default">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Update Appointment
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
var sprytf_name = new Spry.Widget.ValidationTextField("sprytf_name", 'none', {validateOn:["blur", "change"]});
var sprytf_phone = new Spry.Widget.ValidationTextField("sprytf_phone", 'none', {validateOn:["blur", "change"]});
var sprytf_email = new Spry.Widget.ValidationTextField("sprytf_email", 'email', {validateOn:["blur", "change"]});
var sprytf_address = new Spry.Widget.ValidationTextarea("sprytf_address", {isRequired:true, validateOn:["blur", "change"]});
var sprytf_pet_name = new Spry.Widget.ValidationTextField("sprytf_pet_name", 'none', {validateOn:["blur", "change"]});
var sprytf_pet_type = new Spry.Widget.ValidationSelect("sprytf_pet_type");
var sprytf_appointment_type = new Spry.Widget.ValidationSelect("sprytf_appointment_type");
var sprytf_rdate = new Spry.Widget.ValidationTextField("sprytf_rdate", 'none', {validateOn:["blur", "change"]});
var sprytf_rtime = new Spry.Widget.ValidationTextField("sprytf_rtime", 'none', {validateOn:["blur", "change"]});
//-->
</script>