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

if ( isset( $_SESSION[ 'user_data' ] ) ) {
    header('Location: overview.php');
}

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
        if ( isset( $_SESSION['redirecturl'] ) ) {
            $redirecturl = $_SESSION['redirecturl'];
            unset($_SESSION['redirecturl']);
            header('Location: '.$redirecturl);
        } else {
            header('Location: overview.php');  
        }
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
  $userreg_setting = $settings->getSettingValue('allow_user_registration');
  if ( $userreg_setting === 'checked' ) {
        echo '
        <div class="row">
          <div class="col-lg-offset-4 col-lg-4">
            <p class="text-center">or...</p>
            <p class="text-center"><a href="user_registration.php">Register here</a></p>
          </div>
        </div>';
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
          <img src="logo.php" alt="Odin logo" class="logo-big">
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <h4 class="text-center">Odin - IP-address Manager</h4><br>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <form class="form" id="login_form" name="login_form" action="index.php" method="post">
            <div class="form-group row">
              <div class="col-lg-12">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="on" autofocus required pattern="^(?!\s*$).+">
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-12">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required pattern="^(?!\s*$).+">
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
