<?php
require_once './library/config.php';
require_once './library/functions.php';

$errorMessage = '&nbsp;';
if (isset($_POST['name']) && isset($_POST['pwd'])) {
	$result = doLogin();
	if ($result != '') {
		$errorMessage = $result;
	}
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo getSystemSetting('clinic_name', 'Veterinary Clinic'); ?> - Login</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?php echo WEB_ROOT;?>bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo WEB_ROOT;?>dist/css/AdminLTE.css">
    <!-- Modern Theme -->
    <link rel="stylesheet" href="<?php echo WEB_ROOT;?>dist/css/modern-theme.css">
    
	<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>
	
	<link href="<?php echo WEB_ROOT; ?>library/spry/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo WEB_ROOT; ?>library/spry/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>

    <style>
      /* Modern Login Page Styles - Reference Design */
      .login-page {
        background: var(--light-bg);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .login-box {
        width: 400px;
        margin: 0 auto;
      }
      
      .login-logo {
        text-align: center;
        margin-bottom: 30px;
      }
      
      .login-logo a {
        color: var(--sidebar-dark);
        font-size: 28px;
        font-weight: 700;
        text-decoration: none;
        letter-spacing: -0.5px;
      }
      
      .login-box-body {
        background: var(--lighter-bg);
        padding: 40px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        border: none;
      }
      
      .login-box-msg {
        text-align: center;
        margin-bottom: 30px;
        color: var(--text-muted);
        font-size: 16px;
        font-weight: 500;
      }
      
      .form-control {
        border: 1px solid var(--border-gray);
        border-radius: var(--border-radius-sm);
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: var(--lighter-bg);
      }
      
      .form-control:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        background: var(--lighter-bg);
      }
      
      .form-control-feedback {
        right: 20px;
        top: 15px;
        color: var(--text-muted);
      }
      
      .btn-primary {
        background: var(--sidebar-dark);
        border: 1px solid var(--sidebar-dark);
        border-radius: var(--border-radius-sm);
        padding: 12px 24px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
      }
      
      .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
      }
      
      .alert {
        border-radius: var(--border-radius-sm);
        border: none;
        padding: 15px 20px;
        margin-bottom: 20px;
      }
      
      .alert-danger {
        background: rgba(229, 62, 62, 0.1);
        color: var(--danger-color);
        border-left: 4px solid var(--danger-color);
      }
      
      .form-group {
        margin-bottom: 25px;
      }
      
      /* Validation Styles */
      .textfieldRequiredMsg,
      .textfieldMinCharsMsg,
      .passwordRequiredMsg,
      .passwordMinCharsMsg,
      .passwordMaxCharsMsg {
        color: var(--danger-color);
        font-size: 12px;
        margin-top: 5px;
        display: block;
      }
      
      /* Animation */
      .login-box-body {
        animation: fadeInUp 0.6s ease-out;
      }
      
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      /* Responsive */
      @media (max-width: 480px) {
        .login-box {
          width: 90%;
          margin: 20px auto;
        }
        
        .login-box-body {
          padding: 30px 20px;
        }
        
        .login-logo a {
          font-size: 24px;
        }
      }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="#"><i class="fa fa-heartbeat"></i> <?php echo getSystemSetting('clinic_name', 'Veterinary Clinic'); ?></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Sign in to access the appointment system</p>
		<?php if($errorMessage != "&nbsp;" ) {?>
		<div class="alert alert-danger">
        	<i class="fa fa-exclamation-triangle"></i> <?php echo $errorMessage; ?>
		</div>
		<?php } ?>
        <form action="" method="post">
          <div class="form-group has-feedback">
		  	<span id="sprytf_name"> 
            <input type="text" name="name" class="form-control" placeholder="Enter your username">
            <span class="fa fa-user form-control-feedback"></span>
			<span class="textfieldRequiredMsg">Username is required.</span>
			<span class="textfieldMinCharsMsg">Username must be at least 4 characters.</span>
			</span>
          </div>
          <div class="form-group has-feedback">
		  	<span id="sprytf_pwd"> 
            <input type="password" name="pwd" class="form-control" placeholder="Enter your password">
            <span class="fa fa-lock form-control-feedback"></span>
			<span class="passwordRequiredMsg">Password is required.</span>
			<span class="passwordMinCharsMsg">Password must be at least 4 characters.</span>
			<span class="passwordMaxCharsMsg">Password cannot exceed 12 characters.</span>
			</span>
          </div>
          <div class="row">
            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-sign-in"></i> Sign In
              </button>
            </div><!-- /.col -->
          </div>
        </form>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-gray);">
          <small style="color: var(--text-muted);">
            <i class="fa fa-shield"></i> Secure veterinary appointment management system
          </small>
        </div>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

  </body>
<script>
<!--
var sprytf_name = new Spry.Widget.ValidationTextField("sprytf_name", "none", {minChars:4, validateOn:["blur", "change"]});
var sprytf_pwd = new Spry.Widget.ValidationPassword("sprytf_pwd", {minChars:4, maxChars: 12, validateOn:["blur", "change"]});
//-->
</script>
</html>
