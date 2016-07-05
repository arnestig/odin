<?php
session_start();

include_once( "include/user.php" );
include_once( "include/settings.php" );
include_once( "include/html_frame.php" );

if (!empty($_POST['email']) && !empty($_POST['password'])) {
  $user = new User();
  $name = $_POST['email'];
  $pwd = $_POST['password'];
  if ($user->login($name,$pwd)) {
    if ($_SESSION[ 'user_data' ][ 'server_gen_pwd' ] === 1) {
      header('Location: change_password.php');
    } else {
      header('Location: overview.php');  
    }  
  }
}

function userRegistration() {
  $settings = new Settings();
  $allsettings = $settings->getSettings();
  foreach ( $allsettings as $setting ) {
    if ($setting[ 's_name' ] === 'allow_user_registration' && $setting[ 's_value' ] === '1') {
        echo '
        <div class="row">
          <div class="col-lg-offset-4 col-lg-4">
            <p class="text-center">or...</p>
            <p class="text-center"><a href="user_registration.php">Register here</a></p>
          </div>
        </div>';
      }
  }
}

$frame = new HTMLframe();
$frame->doc_start("Log in");

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4 login text-center">
          <img src="images/ODIN-big.png" alt="Odin logo">
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <h4 class="text-center">ODIN - IP-address Manager</h4><br>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <form id="login" name="login_form" action="" method="post">
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Email" autocomplete="on">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" id="login-chkbx" name="login-chkbx">Remember me
              </label>
            </div>
            <button type="submit" name="submit" value="Submit" class="btn btn-default" id="login-btn">Log in</button>
          </form>
        </div>
      </div>';

userRegistration();

echo '
    </div>';

$frame->doc_end();

?>