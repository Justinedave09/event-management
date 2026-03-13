<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<!-- Horizontal Form -->
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">Clinic Closed Day Form</h3>
  </div>
  <!-- /.box-header -->
  <!-- form start -->
  <form class="form-horizontal" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=holiday" method="post">
    <div class="box-body">
      <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Closed Date</label>
        <div class="col-sm-8">
		<span id="sprytf_date">
          <input type="date" class="form-control input-sm" name="date" required min="<?php echo date('Y-m-d'); ?>">
		  <span class="textfieldRequiredMsg">Date is required.</span>
		</span>
        </div>
      </div>
	  
      <div class="form-group">
        <label for="inputPassword3" class="col-sm-4 control-label">Reason for Closure</label>
        <div class="col-sm-8">
		<span id="sprytf_reason">
          <input type="text" class="form-control input-sm" name="reason" placeholder="Reason for clinic closure (e.g., Holiday, Staff Training, Maintenance)">
		  <span class="textfieldRequiredMsg">Reason is required.</span>
		  <span class="textfieldMinCharsMsg">Reason must specify at least 8 characters.</span>
		</span>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
      <button type="submit" class="btn btn-info pull-right">Add Closed Day</button>
    </div>
    <!-- /.box-footer -->
  </form>
</div>
<!-- /.box -->
<script>
<!--
// Enhanced date validation for clinic closed days
var sprytf_reason = new Spry.Widget.ValidationTextField("sprytf_reason", "none", {minChars:8, maxChars: 100, validateOn:["blur", "change"]});

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date"]');
    
    if (dateInput) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        // Custom validation message
        dateInput.addEventListener('invalid', function() {
            if (this.validity.valueMissing) {
                this.setCustomValidity('Please select a date for clinic closure.');
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
});
//-->
</script>
