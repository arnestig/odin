<?php
session_start();

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



if (isset($_POST[ 'checkbox' ])) {
	$ip_array = $_POST[ 'checkbox' ];
    if ( isset( $_SESSION[ 'locked_ips' ][ $_POST[ 'checkbox'] ] ) ) {
        unset( $_SESSION[ 'locked_ips' ][ $_POST[ 'checkbox' ] ] );
    } else {
        $_SESSION[ 'locked_ips'][ $_POST[ 'checkbox' ] ] = 1;
    }
}

if (isset($_POST[ 'ip' ])) {
	$return['reply'] = 'YEYYYYEE';
    echo json_encode($return);
}

?>
