<?php

session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');
include_once('include/user.php');
include_once('include/settings.php');

$settings = new Settings();
$user_reg = $settings->getSettings('user_registration');
if ($user_reg[0]['s_value'] != 1) {
  header('Location: index.php');
}

$alert_msg = '';

if (!empty($_POST['register'])) {
  if (empty($_POST['terms_and_conditions'])) {
    $alert_msg = 'You have to agree to the terms and conditions before continuing.';
  } else if (!empty($_POST['reg_password']) && 
              !empty($_POST['reg_password_repeat']) && 
              $_POST['reg_password'] !== $_POST['reg_password_repeat']) {
    $alert_msg = 'Make sure you type the same password twice.';
    unset($_POST['reg_password']);
    unset($_POST['reg_password_repeat']);
  } else if ( empty($_POST['reg_username']) || 
              empty($_POST['reg_password']) ||
              empty($_POST['reg_password_repeat']) ||
              empty($_POST['reg_first_name']) ||
              empty($_POST['reg_last_name']) ||
              empty($_POST['reg_email']) ||
              empty($_POST['terms_and_conditions'])) {
    $alert_msg = 'You must fill out all the fields.';
  } else {
    $user_man = new UserManagement();
    $userHandler = new User();
    try {
      $user_man->addUser(
        $_POST[ 'reg_username' ],
        $_POST[ 'reg_password' ],
        0,
        $_POST[ 'reg_first_name' ],
        $_POST[ 'reg_last_name' ],
        $_POST[ 'reg_email' ]);
      if ($userHandler->login($_POST[ 'reg_username' ],$_POST[ 'reg_password' ])) {
        header('Location: overview.php');
      }
    } catch (PDOException $e) {
      $alert_msg = 'Your registration could not be completed because the desired username is already taken.';
    }
  }
}

function alert($alert_msg) {
  if ($alert_msg !== '') {
    return '<div class="row">
              <div class="col-lg-12 alert alert-danger">
                '.$alert_msg.'
              </div>
            </div>';
  }
}

$frame = new HTMLframe();
$frame->doc_start("Register User");

echo '
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="images/ODIN.png" alt="Odin - Logo"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">About ODIN</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-lg-12"><br></div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
          <form class="form" method="POST" action="user_registration.php">
            <div class="row">
              <div class="col-lg-12">
                <h2>Create an account</h2>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 bg-light">
                <h5>Login details</h5>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 form-group">
                <label for="inputUserName">Username</label>
                <input type="text" name="reg_username" class="form-control" id="inputUserName" placeholder="Your desired username" value="'.$_POST['reg_username'].'">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="inputPassword">Password</label>
                <input type="password" name="reg_password" class="form-control" id="inputPassword" placeholder="Password" value="'.$_POST['reg_password'].'">
              </div>
              <div class="col-lg-6 form-group">
                <label for="inputPasswordRepeat">Repeat Password</label>
                <input type="password" name="reg_password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password" value="'.$_POST['reg_password_repeat'].'">
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 bg-light">
                <h5>User information</h5>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 form-group">
                <label for="inputEmail">Email address</label>
                <input type="email" name="reg_email" class="form-control" id="inputEmail" placeholder="Email" value="'.$_POST['reg_email'].'">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="inputFirstName">First name</label>
                <input type="text" name="reg_first_name" class="form-control" id="inputFirstName" placeholder="First name" value="'.$_POST['reg_first_name'].'">
              </div>
              <div class="col-lg-6 form-group">
                <label for="inputLastName">Last name</label>
                <input type="text" name="reg_last_name" class="form-control" id="inputLastName" placeholder="Last name" value="'.$_POST['reg_last_name'].'">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-1 form-group">
                <input type="checkbox" name="terms_and_conditions" class="form-control">
              </div>
              <div class="col-lg-11 form-group">
                <p>I have read, understood and agree to these <a href="free-website-terms-and-conditions.pdf">terms and conditions</a></p>
              </div>
            </div>
            '.alert($alert_msg).'
            <div class="row">
              <div class="col-lg-12 form-group">
                <input type="submit" name="register" value="Register and log in" class="btn btn-info form-control"/>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>