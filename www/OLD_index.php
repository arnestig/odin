<?php

include_once( "include/user.php" );
include_once( "include/nwmanagement.php" );
include_once( "include/usermanagement.php" );
$user = new User();
$user->login( "admin", "" );

$nwmanagement = new NetworkManagement();
$nwmanagement->addNetwork( "192.168.0.0", 29 );
$nwmanagement->addNetwork( "192.30.192.0", 18 );
$nwmanagement->addNetwork( "192.0.1.0", 24 );
$nwmanagement->addNetwork( "10.10.1.16", 32 );



$usermanagement = new UserManagement();
$usermanagement->addUser( "testuser", "testpassword", "Testfirstname", "Testlastname", "email@test.com" );
$usermanagement->addUser( "gresen", "goeettteeborg", "Jonas", "Berglund", "noff@sagresen.nu" );
$usermanagement->addUser( "root", "theking", "Tobias", "Eliasson", "gresensa@noff.nu" );
$usermanagement->addUser( "martin", "mrmrmr", "Martin", "Rydin", "martin@email.com" );
$users = $usermanagement->getUsers();
foreach ( $users as $userdata ) {
    echo $userdata[ 'usr_usern' ]."\n";
}

$newuser = new User();
$user->login( "testuser", "testpassword" );

?>
