<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Custom - Client Login</title>
<link href="<?php echo base_url(); ?>css/style.css" rel="stylesheet" type="text/css">
<link href='https://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css'>
<script src="<?php echo base_url(); ?>js/jquery.min.js"></script>
</head>

<body>
<main class="fixed-bg1">
  <div class="wrapper">
    <div class="login-p-logo"> <!--<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>image/logo.png" alt="logo" /></a>--> </div>
    <section class="login">
      <div class="login-form">
        <div class="whole-form">
          <div class="form-title">
            <h2>log in</h2>
            <div id="contact_results"></div>
          </div>
          <form id="loginform" action="<?php echo base_url(); ?>index.php/signin" method="post">
            <ul>
              <li>
                <input name="userName" id="userName" type="text" value="<?php echo set_value('userName'); ?>" placeholder="Username" required="true" />
                <?php echo form_error('userName'); ?>
              </li>
              <li>
                <input name="password" id="password" type="password" value="<?php echo set_value('password'); ?>" placeholder="Password" required="true" />
                <?php echo form_error('password'); ?>
              </li>
              <li>
                <input type="submit" id="submit_btn" value="log in" />
              </li>
              <!--<li><a href="#">forgot password? </a></li>-->
            </ul>
          </form>
        </div>
      </div>
    </section>
  </div>
</main>
</body>
</html>