<?php
session_start();

if (isset($_POST[ 'checkbox' ])) {
	$ip_array = $_POST[ 'checkbox' ];
	if (sizeof($ip_array) > 0) {
		foreach ($ip_array as $ip) {
			if (!in_array($ip, $_SESSION[ 'locked_ips' ] )) {
				$_SESSION[ 'locked_ips' ][] = $ip;
			}
		}
	}
}

if (isset($_POST[ 'ip' ])) {
	$return['reply'] = 'YEYYYYEE';
    echo json_encode($return);
}

?>