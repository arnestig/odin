<?php

session_start();

include_once('include/html_frame.php');

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
          <form>
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="inputEmail" placeholder="Email">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">First name</label>
              <input type="text" class="form-control" id="inputFirstName" placeholder="First name">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Last name</label>
              <input type="text" class="form-control" id="inputLastName" placeholder="Last name">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Username</label>
              <input type="text" class="form-control" id="inputUserName" placeholder="Your desired username">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input type="password" class="form-control" id="inputPassword" placeholder="Password">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Repeat Password</label>
              <input type="password" class="form-control" id="inputPasswordRepeat" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default">Register and log in</button>
          </form>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>