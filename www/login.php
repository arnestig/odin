<?php

include_once( "include/user.php" );
include_once( "include/nwmanagement.php" );
include_once( "include/usermanagement.php" );

fmain();

function fmain () {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $usn = $_POST['usn'];
    $pwd = $_POST['pwd'];
    if ($user->login( $usn, $pwd )) {
      $result = "yes";
    } else {
      $result = "no";
    }
  }

?><!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" href="odin.css">
 </head>
 <body>
  <div id="logincontainer">
   <div id="welcome">
    <h1>Welcome to Valhalla!</h1>
    <p>Time to get them ip's in line</p>
   </div>
   <div id="login">
    <form method="post">
     <input id="usn" name="usn" type="text" placeholder="Username" autocomplete="on"></input>
     <table id="tbl-pwd">
      <tr>
       <td id="tbl-pwd-inp">
        <input id="pwd" name="pwd" type="password" placeholder="Password"></input></td>
       <td id="tbl-pwd-but"><button id="loginbutton" type="submit">Log in</button></td>
      </tr>
     </table>
    </form>
   </div>
   <div id="addme">
   <!-- TODO: add a "sign up-form" -->
   <?php echo $result ?>
   </div>
  </div>
 </body>
</html><?php

}
?>
