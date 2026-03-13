<?php
// Check if user is admin - only admins can access system settings
$user_type = $_SESSION['calendar_fd_user']['type'];
if ($user_type !== 'admin') {
    echo "<div class='alert alert-danger'>Access denied. Only administrators can access system settings.</div>";
    return;
}

// Get current settings
$settings = getSystemSettings();
?>

<div class="row">
  <div class="col-md-8">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-cogs"></i> System Settings</h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <form class="form-horizontal" action="<?php echo WEB_ROOT; ?>views/process.php?cmd=updatesettings" method="post">
        <div class="box-body">
          
          <div class="form-group">
            <label for="clinic_name" class="col-sm-3 control-label">Clinic Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="clinic_name" id="clinic_name" 
                     value="<?php echo htmlspecialchars($settings['clinic_name']); ?>" 
                     placeholder="Enter veterinary clinic name" required>
              <p class="help-block">This name will appear in the system header and emails.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="clinic_address" class="col-sm-3 control-label">Clinic Address</label>
            <div class="col-sm-9">
              <textarea class="form-control" name="clinic_address" id="clinic_address" rows="3" 
                        placeholder="Enter clinic address"><?php echo htmlspecialchars($settings['clinic_address']); ?></textarea>
              <p class="help-block">Full address of the veterinary clinic.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="clinic_phone" class="col-sm-3 control-label">Clinic Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="clinic_phone" id="clinic_phone" 
                     value="<?php echo htmlspecialchars($settings['clinic_phone']); ?>" 
                     placeholder="Enter clinic phone number">
              <p class="help-block">Main contact phone number.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="clinic_email" class="col-sm-3 control-label">Clinic Email</label>
            <div class="col-sm-9">
              <input type="email" class="form-control" name="clinic_email" id="clinic_email" 
                     value="<?php echo htmlspecialchars($settings['clinic_email']); ?>" 
                     placeholder="Enter clinic email address">
              <p class="help-block">Email address used for system notifications.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="clinic_hours" class="col-sm-3 control-label">Operating Hours</label>
            <div class="col-sm-9">
              <textarea class="form-control" name="clinic_hours" id="clinic_hours" rows="3" 
                        placeholder="e.g., Mon-Fri: 8:00 AM - 6:00 PM, Sat: 9:00 AM - 4:00 PM"><?php echo htmlspecialchars($settings['clinic_hours']); ?></textarea>
              <p class="help-block">Clinic operating hours (displayed in emails and forms).</p>
            </div>
          </div>

          <div class="form-group">
            <label for="appointment_duration" class="col-sm-3 control-label">Default Appointment Duration</label>
            <div class="col-sm-9">
              <select class="form-control" name="appointment_duration" id="appointment_duration">
                <option value="15" <?php echo ($settings['appointment_duration'] == '15') ? 'selected' : ''; ?>>15 minutes</option>
                <option value="30" <?php echo ($settings['appointment_duration'] == '30') ? 'selected' : ''; ?>>30 minutes</option>
                <option value="45" <?php echo ($settings['appointment_duration'] == '45') ? 'selected' : ''; ?>>45 minutes</option>
                <option value="60" <?php echo ($settings['appointment_duration'] == '60') ? 'selected' : ''; ?>>60 minutes</option>
              </select>
              <p class="help-block">Default duration for appointments.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="booking_advance_days" class="col-sm-3 control-label">Advance Booking Limit</label>
            <div class="col-sm-9">
              <select class="form-control" name="booking_advance_days" id="booking_advance_days">
                <option value="30" <?php echo ($settings['booking_advance_days'] == '30') ? 'selected' : ''; ?>>30 days</option>
                <option value="60" <?php echo ($settings['booking_advance_days'] == '60') ? 'selected' : ''; ?>>60 days</option>
                <option value="90" <?php echo ($settings['booking_advance_days'] == '90') ? 'selected' : ''; ?>>90 days</option>
                <option value="180" <?php echo ($settings['booking_advance_days'] == '180') ? 'selected' : ''; ?>>180 days</option>
                <option value="365" <?php echo ($settings['booking_advance_days'] == '365') ? 'selected' : ''; ?>>1 year</option>
              </select>
              <p class="help-block">How far in advance clients can book appointments.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="email_notifications" class="col-sm-3 control-label">Email Notifications</label>
            <div class="col-sm-9">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="email_notifications" value="1" 
                         <?php echo ($settings['email_notifications'] == '1') ? 'checked' : ''; ?>>
                  Enable email notifications for appointments
                </label>
              </div>
              <p class="help-block">Send email confirmations and updates to clients.</p>
            </div>
          </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <button type="submit" class="btn btn-info pull-right">
            <i class="fa fa-save"></i> Save Settings
          </button>
        </div>
        <!-- /.box-footer -->
      </form>
    </div>
    <!-- /.box -->
  </div>

  <div class="col-md-4">
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-info-circle"></i> Settings Information</h3>
      </div>
      <div class="box-body">
        <h4>Clinic Name</h4>
        <p>This name will appear in:</p>
        <ul>
          <li>System header/logo area</li>
          <li>Email notifications</li>
          <li>Appointment confirmations</li>
          <li>Footer copyright</li>
        </ul>

        <h4>Contact Information</h4>
        <p>Used for:</p>
        <ul>
          <li>Email signatures</li>
          <li>Contact forms</li>
          <li>System notifications</li>
        </ul>

        <h4>Appointment Settings</h4>
        <p>Controls:</p>
        <ul>
          <li>Default appointment length</li>
          <li>How far ahead bookings are allowed</li>
          <li>Time slot intervals</li>
        </ul>
      </div>
    </div>

    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Important Notes</h3>
      </div>
      <div class="box-body">
        <ul>
          <li><strong>Backup:</strong> Settings are automatically backed up</li>
          <li><strong>Email:</strong> Changes to email settings require system restart</li>
          <li><strong>Access:</strong> Only administrators can modify these settings</li>
        </ul>
      </div>
    </div>
  </div>
</div>