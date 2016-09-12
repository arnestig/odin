<?php

session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');
include_once('include/user.php');
include_once('include/settings.php');

$settings = new Settings();
$user_reg = $settings->getSettingValue('allow_user_registration');
if ($user_reg != 'checked') {
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
          <a class="navbar-brand" href="index.php"><img src="logo.php?small" alt="Odin - Logo"></a>
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
                <input type="checkbox" name="terms_and_conditions" class="form-control" style="width: auto; height: auto;">
              </div>
              <div class="col-lg-11 form-group">
                <p>I have read, understood and agree to these <a href="#" data-toggle="modal" data-target="#termsAndConditionsModal">terms and conditions</a></p>
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

    <div class="modal fade" id="termsAndConditionsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h2>Terms and conditions</h2>
          </div>
          <div class="modal-body">

            <p>We own your data now</p>

            <ol>
              <li>Introduction
                <ol>
                  <li>These terms and conditions shall govern your use of our website.</li>
                  <li>By using our website, you accept these terms and conditions in full;
accordingly, if you disagree with these terms and conditions or any part of
these terms and conditions, you must not use our website.</li>
                  <li>If you [register with our website, submit any material to our website or use
any of our website services], we will ask you to expressly agree to these terms
and conditions.</li>
                  <li>You must be at least [18] years of age to use our website; by using our
website or agreeing to these terms and conditions, you warrant and represent
to us that you are at least [18] years of age.</li>
                  <li>Our website uses cookies; by using our website or agreeing to these terms
and conditions, you consent to our use of cookies in accordance with the
terms of our [privacy and cookies policy].</li>
                </ol>
              </li>
              <li>Credit
                <ol>
                  <li>This document was created using a template from SEQ Legal
(http://www.seqlegal.com).</li>
                </ol>
              </li>
              <li>Copyright notice
                <ol>
                  <li>Copyright (c) [year(s) of first publication] [full name].</li>
                  <li>Subject to the express provisions of these terms and conditions:
                    <ol>
                      <li>we, together with our licensors, own and control all the copyright and
other intellectual property rights in our website and the material on our
website; and</li>
                      <li>all the copyright and other intellectual property rights in our website
and the material on our website are reserved.</li>
                    </ol>
                  </li>
                </ol>
              </li>
              <li>Licence to use website
                <ol>
                  <li>You may:
                    <ol>
                      <li>view pages from our website in a web browser;</li>
                      <li>download pages from our website for caching in a web browser;</li>
                      <li>print pages from our website;</li>
                      <li>[stream audio and video files from our website]; and</li>
                      <li>[use [our website services] by means of a web browser],</li>
                    </ol>
                    subject to the other provisions of these terms and conditions.
                  </li>
                  <li>Except as expressly permitted by Section 4.1 or the other provisions of these
terms and conditions, you must not download any material from our website
or save any such material to your computer.</li>
                  <li>You may only use our website for [your own personal and business purposes],
and you must not use our website for any other purposes.</li>
                  <li>Except as expressly permitted by these terms and conditions, you must not
edit or otherwise modify any material on our website.</li>
                  <li>Unless you own or control the relevant rights in the material, you must not:
                    <ol>
                      <li></li>
                      <li></li>
                    </ol>
                  </li>
                </ol>
              </li>

            </ol>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>
