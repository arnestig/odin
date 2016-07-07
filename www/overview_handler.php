<?php
session_start();
/*
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(isset($_POST['Pid'])) {
        $return['reply'] = 'The input was '.$_POST['Pid'];
    }
    else {
        $return['reply'] = 'No input detected';
    }
    echo json_encode($return);
} else {
    die('direct access is forbidden');
}

*/

if (isset($_POST[ 'checkbox' ]) && !empty($_POST[ 'checkbox' ])) {
	
    $ip = $_POST[ 'checkbox' ];
    $nw_manager = new NetworkManagement();
    $cur_reservations = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );


    if (!in_array( $ip, $cur_reservations ) ) {
        echo $nw_manager->reserveHost( $ip, $_SESSION[ 'user_data' ][ 'usr_id' ] );
    } else {
        echo $nw_manager->unReserveHost( $ip, $_SESSION[ 'user_data' ][ 'usr_id' ] );
    }
}
if (isset($_POST[ 'getReserved' ]) && !empty($_POST[ 'getReserved' ])) {
    print json_encode( $_SESSION[ 'locked_ips' ] );
}

?>
