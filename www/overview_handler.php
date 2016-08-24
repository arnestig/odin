<?php
session_start();
include_once('include/nwmanagement.php');
include_once('include/logbookmanagement.php');
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

if (!empty($_GET[ 'host' ])) {
    $ip = $_GET[ 'host' ];
    $log_manager = new LogbookManagement();
    $hostEntries = $log_manager->getHostEntry( $ip );
    $html_res = '<table class="pop-table"><tr><th>User</th><th>Date</th><th>Change</th></tr>';
    foreach ($hostEntries as $entry) {
        $html_res .= '<tr>';
        foreach ($entry as $field) {
            $html_res .= '<td>'.$field.'</td>';
        }
        $html_res .= '</tr>';
    }
    $html_res .= '</table>';
    echo $html_res;
}

if (!empty($_POST[ 'ip' ]) && !empty($_POST[ 'action' ])) {
	
    $ip = $_POST[ 'ip' ];
    $action = $_POST[ 'action' ];
    $nw_manager = new NetworkManagement();
    $status = '';
    if ($action === 'true') {
        $status = $nw_manager->reserveHost( $ip, $_SESSION[ 'user_data' ][ 'usr_id' ] );
    } else {
        $nw_manager->unReserveHost( $ip, $_SESSION[ 'user_data' ][ 'usr_id' ] );
        $status = true;
    }
    //$return['reserved'][] = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
    //$return['reply'] = $status;
    //echo json_encode($return);
    echo json_encode( array('opStatus' => $status, 'ipList' => $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] )) );

}

if (isset($_POST[ 'getReserved' ]) && !empty($_POST[ 'getReserved' ])) {

    $nw_manager = new NetworkManagement();
    $cur_reservations = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );    

    print json_encode( $cur_reservations );
}

?>
