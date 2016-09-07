<?php
session_start();

include_once( "include/user.php" );
include_once( "include/settings.php" );
include_once( "include/html_frame.php" );

$alert_msg = '';
$alert_type = '';

if ( isset($_POST[ 'submit' ]) && !empty($_POST[ 'submit' ]) ) {
  if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $user = new User();
    $name = $_POST['username'];
    $pwd = $_POST['password'];
    if ($user->login($name,$pwd)) {
      if ($_SESSION[ 'user_data' ][ 'server_gen_pwd' ] === true) {
        header('Location: change_password.php');
        exit;
      } else {
        header('Location: overview.php');  
        exit;
      }  
    } else {
      $alert_msg = 'No user with given credentials exist.';
      $alert_type = 'danger';
    }
  } else {
    $alert_msg = 'Please fill out both fields before continuing.';
    $alert_type = 'warning';
  }
}


function userRegistration() {
  $settings = new Settings();
  $allsettings = $settings->getSettings('user_registration');
  foreach ( $allsettings as $setting ) {
    if ($setting[ 's_name' ] === 'allow_user_registration' && $setting[ 's_value' ] !== '') {
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

function alert($alert_msg, $alert_type) {
  if ( empty($alert_msg) && empty($alert_type) ) return '';
  return '
  <div class="form-group row">
    <div class="col-lg-12 alert alert-'.$alert_type.'">
      '.$alert_msg.'
    </div>
  </div>';
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
          <form class="form" id="login_form" name="login_form" action="index.php" method="post">
            <div class="form-group row">
              <div class="col-lg-12">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="on" autofocus>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-12">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
              </div>
            </div>'
            .alert($alert_msg, $alert_type).
            '<div class="form-group row">
              <div class="col-lg-12">
                <input type="submit" name="submit" value="LOG IN" class="btn btn-info btn-block">
              </div>
            </div>
          </form>
        </div>
      </div>';

userRegistration();

echo '
    </div>';

$frame->doc_end();

?>
