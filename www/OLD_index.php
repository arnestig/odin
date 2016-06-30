<?php

include_once( "include/user.php" );
include_once( "include/nwmanagement.php" );
include_once( "include/usermanagement.php" );
$user = new User();
$user->login( "admin", "" );

$nwmanagement = new NetworkManagement();
$nwmanagement->addNetwork( "192.168.0.0", 29, "This IP-range is intended for use of the R&D-section of ACME. For other uses, please contact IPmasta before taking water over your head");
$nwmanagement->addNetwork( "192.30.192.0", 18, "Sumtimes its summer." );
$nwmanagement->addNetwork( "192.0.1.0", 24, "I stand on this rostrum with a sense of deep humility and great pride -- humility in the wake of those great American architects of our history who have stood here before me; pride in the reflection that this forum of legislative debate represents human liberty in the purest form yet devised. Here are centered the hopes and aspirations and faith of the entire human race. I do not stand here as advocate for any partisan cause, for the issues are fundamental and reach quite beyond the realm of partisan consideration. They must be resolved on the highest plane of national interest if our course is to prove sound and our future protected. I trust, therefore, that you will do me the justice of receiving that which I have to say as solely expressing the considered viewpoint of a fellow American." );
$nwmanagement->addNetwork( "10.10.1.16", 32, "Men since the beginning of time have sought peace. Various methods through the ages have been attempted to devise an international process to prevent or settle disputes between nations. From the very start workable methods were found in so far as individual citizens were concerned, but the mechanics of an instrumentality of larger international scope have never been successful. Military alliances, balances of power, Leagues of Nations, all in turn failed, leaving the only path to be by way of the crucible of war. The utter  destructiveness of war now blocks out this alternative. We have had our last chance. " );



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
