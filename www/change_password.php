<?php

/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2017  Tobias Eliasson <arnestig@gmail.com>
                            Jonas Berglund <jonas.jberglund@gmail.com>
                            Martin Rydin <martin.rydin@gmail.com>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

*/

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
      $userdata[ 'usr_email' ],
      $userdata[ 'usr_errmsg' ]
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
          <a class="navbar-brand" href="index.php"><img src="logo.php?small" alt="Odin - Logo" class="logo-small"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">About Odin</a></li>
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
            <input type="submit" name="change_password" value="Continue" class="btn btn-default"/>
          </form>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>
