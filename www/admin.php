<?php

include_once( "include/usermanagement.php" );

echo '<html>
        <head>
            <link rel="stylesheet" href="odin.css">
            <script type="text/javascript" src="include/sorttable.js"></script>
        </head>
        <table class="sortable">
            <tr>
                <th>user id</th>
                <th>user name</th>
                <th>name</th>
                <th>email</th>
            </tr>';

$usermanagement = new UserManagement();
$users = $usermanagement->getUsers();
foreach ( $users as $userdata ) {
    echo '<tr>
            <td>'.$userdata[ 'usr_id' ].'</td>
            <td>'.$userdata[ 'usr_usern' ].'</td>
            <td>'.$userdata[ 'usr_firstn' ].' '.$userdata[ 'usr_lastn' ].'</td>
            <td>'.$userdata[ 'usr_email' ].'</td>
          </tr>';
}

echo '</table></html>';

?>
