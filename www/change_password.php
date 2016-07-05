<?php

session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');

if (isset($_POST['change_password'])) {
  // TODO: sanitize input...
  if ($_POST[ 'reg_password' ] !== $_POST[ 'reg_password_repeat' ]) {
    // TODO: Never go full retard
  } else {
    $user_man = new UserManagement();
    $userdata = $_SESSION[ 'user_data' ];
    $user_man->updateUser(
      $userdata[ 'usr_id' ],
      $userdata[ 'usr_usern' ],
      $_POST[ 'reg_password' ],
      0,
      $userdata[ 'usr_firstn' ],
      $userdata[ 'usr_lastn' ],
      $userdata[ 'usr_email' ]
    );
    header('Location: overview.php');
  }
}

$frame = new HTMLframe();
$frame->doc_start("Change password");

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
      <div class="row"><div class="col-lg-12"><br></div></div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <form action="change_password.php" method="POST">
            <h3>Change password</h3>
            <p>'.$_SESSION[ 'user_data' ][ 'usr_usern' ].', your password needs to be changed before continuing.</p>
            <div class="form-group">
              <label for="inputPassword">Password</label>
              <input type="password" name="reg_password" class="form-control" id="inputPassword" placeholder="Password">
            </div>
            <div class="form-group">
              <label for="inputPasswordRepeat">Repeat Password</label>
              <input type="password" name="reg_password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password">
            </div>
            <input type="submit" name="change_password" value="Register and log in" class="btn btn-default"/>
          </form>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>