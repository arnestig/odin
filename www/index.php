<?php

include_once( "include/user.php" );
include_once( "include/nwmanagement.php" );
$user = new User();
$user->login( "admin", "" );

$nwmanagement = new NetworkManagement();
$nwmanagement->addNetwork( "192.168.0.2", 23 );

?>
