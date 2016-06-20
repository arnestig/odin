<?php

include_once( "include/user.php" );
include( "include/html_frame.php" );

?>

<?php
HTMLframe::doc_start("Log in"); 
?>

    <div class="container">
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4 login text-center">
          <img src="images/Kapsch-logo-big.png" alt="Kapsch logo">
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <h4 class="text-center">ODIN - IP-address Manager</h4><br>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <form>
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" autocomplete="on">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox">Remember me
              </label>
            </div>
            <a class="btn btn-default" href="networks.html" role="button">Log in</a>
            <!-- Real btn
            <button type="submit" class="btn btn-default">Log in</button>
            -->
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
          <p class="text-center">or...</p>
          <p class="text-center"><a href="new_user.html">Create new user</a></p>
        </div>
      </div>
    </div>

<?php;
HTMLframe::doc_end(); 
?>