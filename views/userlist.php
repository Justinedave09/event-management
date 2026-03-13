<?php 
$records = getUserRecords();
$utype = '';
$type = $_SESSION['calendar_fd_user']['type'];
if($type == 'admin' || $type == 'staff') {
	$utype = 'on';
}
?>

<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Pet Owners</h3>
      <div class="box-tools pull-right">
        <?php if($type == 'admin' || $type == 'staff') { ?>
        <a href="<?php echo WEB_ROOT; ?>views/?v=CREATE" class="btn btn-primary btn-sm">
          <i class="fa fa-plus"></i> Add Pet Owner
        </a>
        <?php } ?>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered user-list-table">
        <tr>
          <th style="width: 10px">#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>User Role</th>
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
		if($status == "active") {$stat = 'success';}
		else if($status == "lock") {$stat = 'warning';}
		else if($status == "inactive") {$stat = 'warning';}
		else if($status == "delete") {$stat = 'danger';}
		?>
        <tr>
          <td><?php echo $idx++; ?></td>
          <td><a href="<?php echo WEB_ROOT; ?>views/?v=USER&ID=<?php echo $user_id; ?>"><?php echo strtoupper($user_name); ?></a></td>
          <td><?php echo $user_email; ?></td>
          <td><?php echo $user_phone; ?></td>
         
          <td>
		  <i class="fa <?php echo $type == 'staff' ? 'fa-user-md' : ($type == 'admin' ? 'fa-user-shield' : 'fa-user') ; ?>" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo strtoupper($type == 'client' ? 'PET OWNER' : $type); ?></td>
          <td><span class="label label-<?php echo $stat; ?>"><?php echo strtoupper($status); ?></span></td>
          <?php if($utype == 'on') { ?>
		  <td>
		    <div class="user-actions">
		      <a href="javascript:editUser('<?php echo $user_id ?>');" class="btn btn-info btn-xs btn-edit" title="Edit user details">
                <i class="fa fa-edit"></i> Edit
              </a>
              
		      <?php if($status == "active") {?>
              <a href="javascript:status('<?php echo $user_id ?>', 'inactive');" class="btn btn-warning btn-xs" title="Deactivate user">
                <i class="fa fa-pause"></i> Inactive
              </a>
			  <a href="javascript:status('<?php echo $user_id ?>', 'lock');" class="btn btn-danger btn-xs" title="Lock user account">
                <i class="fa fa-lock"></i> Lock
              </a>
			  <a href="javascript:status('<?php echo $user_id ?>', 'delete');" class="btn btn-default btn-xs" title="Delete user">
                <i class="fa fa-trash"></i> Delete
              </a>
              <?php } else { ?>
			  <a href="javascript:status('<?php echo $user_id ?>', 'active');" class="btn btn-success btn-xs" title="Activate user">
                <i class="fa fa-play"></i> Active
              </a>
			  <?php }//else ?>
            </div>
          </td>
		  <?php }?>
        </tr>
        <?php } ?>
        </table>
      </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
	
	<?php 
	$type = $_SESSION['calendar_fd_user']['type'];
	if($type == 'admin') {
	?>
	<!-- <button type="button" class="btn btn-info" onclick="javascript:createUserForm();"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;Create a new User</button> -->
	<?php 
	}
	?>
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
function createUserForm() {
	window.location.href = '<?php echo WEB_ROOT; ?>views/?v=CREATE';
}

function editUser(userId) {
	// Redirect to edit form
	window.location.href = '<?php echo WEB_ROOT; ?>views/?v=USEREDIT&ID='+userId;
}

function status(userId, status) {
	var actionText = '';
	switch(status) {
		case 'active': actionText = 'activate'; break;
		case 'inactive': actionText = 'deactivate'; break;
		case 'lock': actionText = 'lock'; break;
		case 'delete': actionText = 'delete'; break;
	}
	
	if(confirm('Are you sure you want to ' + actionText + ' this user?')) {
		window.location.href = '<?php echo WEB_ROOT; ?>views/process.php?cmd=change&action='+ status +'&userId='+userId;
	}
}
</script>
