<?php
// Get dashboard statistics
$totalAppointments = 0;
$totalClients = 0;
$totalStaff = 0;
$pendingAppointments = 0;

// Get total appointments
$sql = "SELECT COUNT(*) as total FROM tbl_appointments";
$result = dbQuery($sql);
if($result && dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $totalAppointments = $row['total'];
}

// Get total clients
$sql = "SELECT COUNT(*) as total FROM tbl_users WHERE type = 'client'";
$result = dbQuery($sql);
if($result && dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $totalClients = $row['total'];
}

// Get total staff
$sql = "SELECT COUNT(*) as total FROM tbl_users WHERE type IN ('admin', 'staff')";
$result = dbQuery($sql);
if($result && dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $totalStaff = $row['total'];
}

// Get pending appointments
$sql = "SELECT COUNT(*) as total FROM tbl_appointments WHERE status = 'pending'";
$result = dbQuery($sql);
if($result && dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $pendingAppointments = $row['total'];
}
?>

<!-- Dashboard Cards -->
<div class="dashboard-cards fade-in">
  <div class="dashboard-card appointments">
    <div class="card-icon">
      <i class="fa fa-calendar"></i>
    </div>
    <div class="card-title">Total Appointments</div>
    <div class="card-number"><?php echo $totalAppointments; ?></div>
    <div class="card-subtitle">All time appointments</div>
  </div>
  
  <div class="dashboard-card clients">
    <div class="card-icon">
      <i class="fa fa-users"></i>
    </div>
    <div class="card-title">Pet Owners</div>
    <div class="card-number"><?php echo $totalClients; ?></div>
    <div class="card-subtitle">Registered clients</div>
  </div>
  
  <div class="dashboard-card staff">
    <div class="card-icon">
      <i class="fa fa-user-md"></i>
    </div>
    <div class="card-title">Staff Members</div>
    <div class="card-number"><?php echo $totalStaff; ?></div>
    <div class="card-subtitle">Active staff</div>
  </div>
  
  <div class="dashboard-card pending">
    <div class="card-icon">
      <i class="fa fa-clock-o"></i>
    </div>
    <div class="card-title">Pending</div>
    <div class="card-number"><?php echo $pendingAppointments; ?></div>
    <div class="card-subtitle">Awaiting approval</div>
  </div>
</div>

<div class="row fade-in dashboard-container">
  <div class="col-md-8">
    <div class="box calendar-box">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-calendar"></i> Appointment Calendar</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <?php include('calendar.php'); ?>
      </div>
    </div>
  </div>
  <!-- /.col -->
  <div class="col-md-4">
    <?php 
    $type = $_SESSION['calendar_fd_user']['type'];
    if($type == 'admin' || $type == 'staff') {
      echo '<div class="slide-in form-box">';
      include('eventform.php');
      echo '</div>';
    }
    else {
      // Show welcome message for clients
      echo '<div class="box form-box">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user"></i> Welcome</h3>
              </div>
              <div class="box-body">
                <p>Welcome to the veterinary appointment system!</p>
                <p>You can view your appointments in the calendar on the left.</p>
                <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i> 
                  <strong>Need to book an appointment?</strong><br>
                  Please contact our staff to schedule your pet\'s appointment.
                </div>
              </div>
            </div>';
    }
    ?>	
  </div>
  <!-- /.col -->
</div>
