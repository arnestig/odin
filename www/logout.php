<?php
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

// Release all reserved hosts
require_once('include/nwmanagement.php');

$nw_manager = new NetworkManagement();
$cur_reservations = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );

foreach($cur_reservations as $ip) {
  $nw_manager->unReserveHost( $ip, $_SESSION[ 'user_data' ][ 'usr_id' ] );
}

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to index.php
header('Location: index.php');

?>
