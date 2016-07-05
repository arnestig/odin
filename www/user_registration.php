<?php

session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');
include_once('include/user.php');

if (isset($_POST['Register'])) {
  //sanitize input...
  $user_man = new UserManagement();
  $user_man->addUser(
    $_POST[ 'reg_username' ],
    $_POST[ 'reg_password' ],
    0,
    $_POST[ 'reg_first_name' ],
    $_POST[ 'reg_last_name' ],
    $_POST[ 'reg_email' ]
  );
  $userHandler = new User();
  if ($userHandler->login($_POST[ 'reg_username' ],$_POST[ 'reg_password' ])) {
    header('Location: overview.php');
  }
  //Ändra below till nåt gick schnett...
  header('Location: index.php');
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
      <div class="row"><div class="col-lg-12"><br></div></div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <form class="form" action="user_registration.php" method="POST">
            <div class="form-group">
              <label for="inputEmail">Email address</label>
              <input type="email" name="reg_email" class="form-control" id="inputEmail" placeholder="Email">
            </div>
            <div class="form-group">
              <label for="inputFirstName">First name</label>
              <input type="text" name="reg_first_name" class="form-control" id="inputFirstName" placeholder="First name">
            </div>
            <div class="form-group">
              <label for="inputLastName">Last name</label>
              <input type="text" name="reg_last_name" class="form-control" id="inputLastName" placeholder="Last name">
            </div>
            <div class="form-group">
              <label for="inputUserName">Username</label>
              <input type="text" name="reg_username" class="form-control" id="inputUserName" placeholder="Your desired username">
            </div>
            <div class="form-group">
              <label for="inputPassword">Password</label>
              <input type="password" name="reg_password" class="form-control" id="inputPassword" placeholder="Password">
            </div>
            <div class="form-group">
              <label for="inputPasswordRepeat">Repeat Password</label>
              <input type="password" name="reg_password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password">
            </div>
            <input type="submit" name="Register" value="Register and log in" class="btn btn-default"/>
          </form>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>