<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><b>Book Veterinary Appointment</b></h3>
  </div>
  <!-- /.box-header -->
  <!-- form start -->
  <form role="form" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=book" method="post">
    <div class="box-body">
      <div class="form-group">
        <label for="exampleInputEmail1">Pet Owner Name</label>
		<input type="hidden" name="userId" value=""  id="userId"/>
        <span id="sprytf_name">
		<select name="name" class="form-control input-sm">
			<option>--select pet owner--</option>
			<?php
			$sql = "SELECT id, name FROM tbl_users WHERE type != 'admin'";
			$result = dbQuery($sql);
			while($row = dbFetchAssoc($result)) {
				extract($row);
			?>
			<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
			<?php 
			}
			?>
		</select>
		<span class="selectRequiredMsg">Pet owner name is required.</span>
		
		</span>
      </div>
	  
	  <div class="form-group">
        <label for="exampleInputEmail1">Address</label>
		<span id="sprytf_address">
        <textarea name="address" class="form-control input-sm" placeholder="Address" id="address"></textarea>
		<span class="textareaRequiredMsg">Address is required.</span>
		<span class="textareaMinCharsMsg">Address must specify at least 10 characters.</span>	
		</span>
      </div>
	  <div class="form-group">
        <label for="exampleInputEmail1">Phone</label>
		<span id="sprytf_phone">
        <input type="text" name="phone" class="form-control input-sm"  placeholder="Phone number" id="phone">
		<span class="textfieldRequiredMsg">Phone number is required.</span>
		</span>
      </div>
	  <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
		<span id="sprytf_email">
        <input type="text" name="email" class="form-control input-sm" placeholder="Enter email" id="email">
		<span class="textfieldRequiredMsg">Email ID is required.</span>
		<span class="textfieldInvalidFormatMsg">Please enter a valid email (user@domain.com).</span>
		</span>
      </div>

      <div class="form-group">
        <label for="pet_name">Pet Name</label>
		<span id="sprytf_pet_name">
        <input type="text" name="pet_name" class="form-control input-sm" placeholder="Pet's name" required>
		<span class="textfieldRequiredMsg">Pet name is required.</span>
		</span>
      </div>

      <div class="form-group">
        <label for="pet_type">Pet Type</label>
		<span id="sprytf_pet_type">
        <select name="pet_type" class="form-control input-sm" required>
			<option value="">--select pet type--</option>
			<option value="Dog">Dog</option>
			<option value="Cat">Cat</option>
			<option value="Bird">Bird</option>
			<option value="Rabbit">Rabbit</option>
			<option value="Hamster">Hamster</option>
			<option value="Fish">Fish</option>
			<option value="Reptile">Reptile</option>
			<option value="Other">Other</option>
		</select>
		<span class="selectRequiredMsg">Pet type is required.</span>
		</span>
      </div>

      <div class="form-group">
        <label for="pet_breed">Pet Breed (Optional)</label>
        <input type="text" name="pet_breed" class="form-control input-sm" placeholder="Pet's breed (optional)">
      </div>

      <div class="form-group">
        <label for="appointment_type">Appointment Type</label>
		<span id="sprytf_appointment_type">
        <select name="appointment_type" class="form-control input-sm" required>
			<option value="">--select appointment type--</option>
			<option value="General Checkup">General Checkup</option>
			<option value="Vaccination">Vaccination</option>
			<option value="Surgery">Surgery</option>
			<option value="Dental Care">Dental Care</option>
			<option value="Emergency">Emergency</option>
			<option value="Follow-up">Follow-up</option>
			<option value="Grooming">Grooming</option>
			<option value="Consultation">Consultation</option>
		</select>
		<span class="selectRequiredMsg">Appointment type is required.</span>
		</span>
      </div>
	  
      <div class="form-group">
      <div class="row">
      	<div class="col-xs-6">
			<label>Appointment Date</label>
			<span id="sprytf_rdate">
        	<input type="date" name="rdate" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
			<span class="textfieldRequiredMsg">Date is required.</span>
			<span class="textfieldInvalidFormatMsg">Please select a valid future date.</span>
			</span>
        </div>
        <div class="col-xs-6">
			<label>Appointment Time</label>
			<span id="sprytf_rtime">
            <input type="time" name="rtime" class="form-control" required min="08:00" max="18:00" step="900">
			<span class="textfieldRequiredMsg">Time is required.</span>
			<span class="textfieldInvalidFormatMsg">Please select a time between 8:00 AM and 6:00 PM.</span>
			</span>
       </div>
      </div>
	  </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
      <button type="submit" class="btn btn-primary">Book Appointment</button>
    </div>
  </form>
</div>
<!-- /.box -->
<script type="text/javascript">
<!--
var sprytf_name 	= new Spry.Widget.ValidationSelect("sprytf_name");
var sprytf_address 	= new Spry.Widget.ValidationTextarea("sprytf_address", {minChars:6, isRequired:true, validateOn:["blur", "change"]});
var sprytf_phone 	= new Spry.Widget.ValidationTextField("sprytf_phone", 'none', {validateOn:["blur", "change"]});
var sprytf_mail 	= new Spry.Widget.ValidationTextField("sprytf_email", 'email', {validateOn:["blur", "change"]});
var sprytf_pet_name = new Spry.Widget.ValidationTextField("sprytf_pet_name", 'none', {validateOn:["blur", "change"]});
var sprytf_pet_type = new Spry.Widget.ValidationSelect("sprytf_pet_type");
var sprytf_appointment_type = new Spry.Widget.ValidationSelect("sprytf_appointment_type");

// Enhanced date/time validation
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="rdate"]');
    const timeInput = document.querySelector('input[name="rtime"]');
    
    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        // Custom validation message
        dateInput.addEventListener('invalid', function() {
            if (this.validity.valueMissing) {
                this.setCustomValidity('Please select an appointment date.');
            } else if (this.validity.rangeUnderflow) {
                this.setCustomValidity('Please select a future date.');
            } else {
                this.setCustomValidity('');
            }
        });
        
        dateInput.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    }
    
    // Time input validation
    if (timeInput) {
        timeInput.addEventListener('invalid', function() {
            if (this.validity.valueMissing) {
                this.setCustomValidity('Please select an appointment time.');
            } else if (this.validity.rangeUnderflow) {
                this.setCustomValidity('Clinic opens at 8:00 AM.');
            } else if (this.validity.rangeOverflow) {
                this.setCustomValidity('Clinic closes at 6:00 PM.');
            } else {
                this.setCustomValidity('');
            }
        });
        
        timeInput.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    }
});
//-->
</script>

<script type="text/javascript">
$('select[name="name"]').on('change', function() {
	var id = this.value;
	if(id) {
		$.get('<?php echo WEB_ROOT. 'api/process.php?cmd=user&userId=' ?>'+id, function(data, status){
			var obj = $.parseJSON(data);
			$('#userId').val(obj.user_id);
			$('#email').val(obj.email);
			$('#address').val(obj.address);
			$('#phone').val(obj.phone_no);
		});
	}
})
</script>