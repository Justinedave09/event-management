<?php 
$records = getBookingRecords();
$utype = '';
$type = $_SESSION['calendar_fd_user']['type'];
if($type == 'admin') {
	$utype = 'on';
}
?>

<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Veterinary Appointment Details</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered appointment-list-table">
        <tr>
          <th style="width: 10px">#</th>
          <th>Owner Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Pet Name</th>
          <th>Pet Type</th>
          <th>Appointment Date</th>
          <th>Appointment Type</th>
          <th style="width: 100px">Status</th>
          <?php if($utype == 'on') { ?>
		  <th style="width: 200px">Action</th>
		  <?php } ?>
        </tr>
        <?php
	  $idx = 1;
	  foreach($records as $rec) {
	  	extract($rec);
		$stat = '';
		if($status == "PENDING") {$stat = 'warning';}
		else if ($status == "APPROVED") {$stat = 'success';}
		else if($status == "DENIED") {$stat = 'danger';}
		?>
        <tr>
          <td><?php echo $idx++; ?></td>
          <td><a href="<?php echo WEB_ROOT; ?>views/?v=USER&ID=<?php echo $user_id; ?>"><?php echo strtoupper($user_name); ?></a></td>
          <td><?php echo $user_email; ?></td>
          <td><?php echo $user_phone; ?></td>
          <td><?php echo $pet_name; ?></td>
          <td><?php echo $pet_type; ?></td>
          <td><?php echo $appointment_date; ?></td>
          <td><?php echo $appointment_type; ?></td>
          <td><span class="label label-<?php echo $stat; ?>"><?php echo $status; ?></span></td>
          <?php if($utype == 'on') { ?>
		  <td>
		    <div class="appointment-actions">
		      <?php if($status == "PENDING") {?>
              <a href="javascript:approve('<?php echo $user_id ?>');" class="btn btn-success btn-xs" title="Approve this appointment">
                <i class="fa fa-check"></i> Approve
              </a>
			  <a href="javascript:decline('<?php echo $user_id ?>');" class="btn btn-danger btn-xs" title="Decline this appointment">
                <i class="fa fa-times"></i> Deny
              </a>
              <?php } ?>
              
              <?php if($status == "APPROVED") { ?>
              <a href="javascript:sendReminder('<?php echo $user_id ?>');" class="btn btn-warning btn-xs btn-reminder" title="Send appointment reminder email to client">
                <i class="fa fa-bell"></i> Remind
              </a>
              <?php } ?>
              
              <a href="javascript:editAppointment('<?php echo $user_id ?>');" class="btn btn-info btn-xs btn-edit" title="Edit appointment details">
                <i class="fa fa-edit"></i> Edit
              </a>
              
			  <a href="javascript:deleteUser('<?php echo $user_id ?>');" class="btn btn-default btn-xs" title="Delete this appointment">
                <i class="fa fa-trash"></i> Delete
              </a>
            </div>
          </td>
		  <?php } ?>
        </tr>
        <?php } ?>
        </table>
      </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
      <!--
	<ul class="pagination pagination-sm no-margin pull-right">
      <li><a href="#">&laquo;</a></li>
      <li><a href="#">1</a></li>
      <li><a href="#">2</a></li>
      <li><a href="#">3</a></li>
      <li><a href="#">&raquo;</a></li>
    </ul>
	-->
      <?php echo generatePagination(); ?> </div>
  </div>
  <!-- /.box -->
</div>

<script language="javascript">
function approve(userId) {
	if(confirm('Are you sure you want to approve this appointment?')) {
		window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=regConfirm&action=approve&userId='+userId;
	}
}

function decline(userId) {
	if(confirm('Are you sure you want to decline this appointment?')) {
		window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=regConfirm&action=denide&userId='+userId;
	}
}

function deleteUser(userId) {
	if(confirm('Deleting this record will also remove the appointment from calendar.\n\nAre you sure you want to proceed?')) {
		window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=delete&userId='+userId;
	}
}

function sendReminder(userId) {
	if(confirm('Send appointment reminder email to this client?\n\nThis will send a detailed reminder about their upcoming appointment.')) {
		// Show loading state
		var reminderBtn = event.target.closest('a');
		var originalText = reminderBtn.innerHTML;
		reminderBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
		reminderBtn.style.pointerEvents = 'none';
		
		// Send reminder
		window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=sendReminder&userId='+userId;
	}
}

function editAppointment(userId) {
	// Redirect to edit form
	window.location.href = '<?php echo WEB_ROOT; ?>views/?v=EDIT&ID='+userId;
}
</script>
