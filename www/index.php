<?php

include_once( "include/user.php" );
include_once( "include/nwmanagement.php" );
include_once( "include/usermanagement.php" );
$user = new User();
$user->login( "admin", "" );

$nwmanagement = new NetworkManagement();
$nwmanagement->addNetwork( "192.168.0.2", 23 );


$usermanagement = new UserManagement();
$usermanagement->addUser( "testuser", "testpassword", "Testfirstname", "Testlastname", "email@test.com" );

$newuser = new User();
$user->login( "testuser", "testpassword" );

?>
