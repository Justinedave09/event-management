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
      <table class="table table-bordered">
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
		  <th >Action</th>
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
		  <td><?php if($status == "PENDING") {?>
            <a href="javascript:approve('<?php echo $user_id ?>');">Approve</a>&nbsp;/
			&nbsp;<a href="javascript:decline('<?php echo $user_id ?>');">Deny</a>&nbsp;/
			&nbsp;<a href="javascript:deleteUser('<?php echo $user_id ?>');">Delete</a>
            <?php } else { ?>
			<a href="javascript:deleteUser('<?php echo $user_id ?>');">Delete</a>
			<?php }//else ?>
          </td>
		  <?php } ?>
        </tr>
        <?php } ?>
      </table>
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
	if(confirm('Are you sure you wants to Approve it ?')) {
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

</script>
