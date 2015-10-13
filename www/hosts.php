<?php

include_once( "include/nwmanagement.php" );
include_once( "include/tablegenerator.php" );

function displayHosts() {
    $networkmanagement = new NetworkManagement();
    $hosts = $networkmanagement->getHosts();

    $tableGenerator = new TableGenerator(); 
    $tableGenerator->addColumn( 'host ip', '%s', array( 'host_ip' ) );
    $tableGenerator->addColumn( 'host name', '%s', array( 'host_name' ) );
    $tableGenerator->addColumn( 'data', '%s', array( 'host_data' ) );
    $tableGenerator->addColumn( 'description', '%s', array( 'host_description' ) );
    $tableGenerator->addColumn( 'expiry', '%s', array( 'host_lease_expiry' ) );
    $tableGenerator->addColumn( 'last seen', '%s', array( 'host_last_seen' ) );
    $tableGenerator->addColumn( 'last scanned', '%s', array( 'host_last_scanned' ) );
    $tableGenerator->addColumn( 'owner', '%d', array( 'usr_id' ) );
    $tableGenerator->setData( $hosts );
    echo $tableGenerator->generateHTML();
}

echo '<html>
        <head>
            <link rel="stylesheet" href="odin.css">
            <script type="text/javascript" src="include/sorttable.js"></script>
        </head>';

/* Handle our different GET's on this page */
if ($_SERVER[ 'REQUEST_METHOD' ] === 'GET') {
}

/* Handle our different POST's on this page */
if ( $_SERVER[ 'REQUEST_METHOD'] === 'POST' ) {
}

/* Display a list of our hosts */
displayHosts();

echo '</html>';

?>
