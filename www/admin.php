<?php

include_once( "include/usermanagement.php" );

function displayUsers() {
    echo '<table class="sortable">
        <tr>
        <th>user id</th>
        <th>user name</th>
        <th>name</th>
        <th>email</th>
        <th></th>
        <th></th>
        </tr>';

    $usermanagement = new UserManagement();
    $users = $usermanagement->getUsers();
    foreach ( $users as $userdata ) {
        echo '<tr>
            <td>'.$userdata[ 'usr_id' ].'</td>
            <td>'.$userdata[ 'usr_usern' ].'</td>
            <td>'.$userdata[ 'usr_firstn' ].' '.$userdata[ 'usr_lastn' ].'</td>
            <td>'.$userdata[ 'usr_email' ].'</td>
            <td><a href="admin.php?edit_user='.$userdata[ 'usr_id' ].'">edit</a></td>
            <td><a href="admin.php?remove_user='.$userdata[ 'usr_id' ].'">remove</a></td>
            </tr>';
    }

    echo '</table>';
}

function editUserPage( $user_id ) {
    $usermanagement = new UserManagement();
    $userdata = $usermanagement->getUserInfo( $user_id );
    echo '<FORM method="post" action="admin.php">
        <table>
            <INPUT type="hidden" name="eupUserID" value="'.$userdata[ 'usr_id' ].'">
        <tr><td>
            Username:</td><td><INPUT type="text" name="eupUsername" value="'.$userdata[ 'usr_usern' ].'">
        </td></tr>
        <tr><td>
            First name:</td><td><INPUT type="text" name="eupFirstname" value="'.$userdata[ 'usr_firstn' ].'">
        </td></tr>
        <tr><td>
            Last name:</td><td><INPUT type="text" name="eupLastname" value="'.$userdata[ 'usr_lastn' ].'">
        </td></tr>
        <tr><td>
            Email:</td><td><INPUT type="text" name="eupEmail" value="'.$userdata[ 'usr_email' ].'">
        </td></tr>
        <tr><td>
            Password:</td><td><INPUT type="password" name="eupPassword">
        </td></tr>
        <tr><td align="right" colspan=2>
            <BUTTON type="submit" name="eupSubmit" value="Save">Save</BUTTON>
            <BUTTON type="submit" name="eupSubmit" value="Cancel">Cancel</BUTTON>
        </td></tr>
        </table></FORM></HTML>';
        exit;
}

echo '<html>
        <head>
            <link rel="stylesheet" href="odin.css">
            <script type="text/javascript" src="include/sorttable.js"></script>
        </head>';

/* Handle our different GET's on this page */
if ($_SERVER[ 'REQUEST_METHOD' ] === 'GET') {
    if ( isset( $_REQUEST[ 'edit_user' ] ) ) {
        $user_id = $_REQUEST[ 'edit_user' ];
        editUserPage( $user_id );
    }
}

/* Handle our different POST's on this page */
if ( $_SERVER[ 'REQUEST_METHOD'] === 'POST' ) {
    /* Edit User Page form */
    if ( isset( $_POST[ 'eupSubmit' ] ) ) {
        if ( $_POST[ 'eupSubmit' ] === 'Save' ) {
            $usermanagement = new UserManagement();
            $usermanagement->updateUser(
                                        $_POST[ 'eupUserID' ],
                                        $_POST[ 'eupUsername' ],
                                        $_POST[ 'eupPassword' ],
                                        $_POST[ 'eupFirstname' ],
                                        $_POST[ 'eupLastname' ],
                                        $_POST[ 'eupEmail' ] );
        }
    }
}

/* Display a list of our users */
displayUsers();

echo '</html>';

?>
