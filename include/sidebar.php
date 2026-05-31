<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
  <ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>
    
    <?php 
    $type = $_SESSION['calendar_fd_user']['type'];
    
    if($type == 'client') {
        // Client/Pet Owner Menu
    ?>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=DB">
          <i class="fa fa-plus-circle"></i><span>Book Appointment</span>
        </a>
    </li>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=MYAPPOINTMENTS">
          <i class="fa fa-calendar-check-o"></i><span>My Appointments</span>
        </a>
    </li>
        <!-- Pets - visible to clients and staff -->
<li class="treeview"> 
    <a href="<?php echo WEB_ROOT; ?>/?v=PETS">
      <i class="fa fa-paw"></i>
      <span><?= $type === 'client' ? 'My Pets' : 'Pet Records' ?></span>
    </a>
</li>
    <?php 
    } else {
        // Staff and Admin Menu
    ?>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=DB">
          <i class="fa fa-calendar"></i><span>Appointment Calendar</span>
        </a>
    </li>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=LIST">
          <i class="fa fa-stethoscope"></i><span>Appointments</span>
        </a>
    </li>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=USERS">
          <i class="fa fa-users"></i><span>Pet Owners</span>
        </a>
    </li>
    <?php 
        if($type == 'admin') {
    ?>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=STAFF">
          <i class="fa fa-user-md"></i><span>Staff Management</span>
        </a>
    </li>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=HOLY">
          <i class="fa fa-times-circle"></i><span>Clinic Closed Days</span>
        </a>
    </li>
    <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=SETTINGS">
          <i class="fa fa-cogs"></i><span>System Settings</span>
        </a>
    </li>
    <!-- Pets - visible to clients and staff -->
<li class="treeview"> 
    <a href="<?php echo WEB_ROOT; ?>/?v=PETS">
      <i class="fa fa-paw"></i>
      <span><?= $type === 'client' ? 'My Pets' : 'Pet Records' ?></span>
    </a>
</li>

    <?php
        }
    }
    ?>
    
    <!-- ═══════════════════════════════════════ -->
    <!-- AI ASSISTANT SECTION (Visible to All)  -->
    <!-- ═══════════════════════════════════════ -->
     <li class="treeview"> 
    <a href="<?php echo WEB_ROOT; ?>views/?v=ANNOUNCEMENTS">
      <i class="fa fa-bullhorn"></i>
      <span><?= $type === 'client' ? 'News & Events' : 'Announcements' ?></span>
    </a>
</li>
    <li class="header">AI ASSISTANT</li>
    
    <li class="treeview">
        <a href="<?php echo WEB_ROOT; ?>views/?v=CHAT">
          <i class="fa fa-comments"></i><span>AI Chat</span>
          <small class="label pull-right bg-green">NEW</small>
        </a>
    </li>
    
    <?php if($type == 'admin') { ?>
    <li class="treeview">
        <a href="<?php echo WEB_ROOT; ?>/?v=FAQS">
          <i class="fa fa-question-circle"></i><span>FAQs Management</span>
        </a>
    </li>
    <?php } ?>
    
  </ul>
</section>
<!-- /.sidebar -->