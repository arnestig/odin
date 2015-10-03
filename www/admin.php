<?php

include_once( "include/usermanagement.php" );
include_once( "include/tablegenerator.php" );

function displayUsers() {
    $usermanagement = new UserManagement();
    $users = $usermanagement->getUsers();

    $tableGenerator = new TableGenerator(); 
    $tableGenerator->addColumn( 'user id', '%d', array( 'usr_id' ) );
    $tableGenerator->addColumn( 'username', '%s', array( 'usr_usern' ) );
    $tableGenerator->addColumn( 'name', '%s %s', array( 'usr_firstn','usr_lastn' ) );
    $tableGenerator->addColumn( 'email', '%s', array( 'usr_email' ) );
    $tableGenerator->addColumn( '', '<a href="admin.php?edit_user=%s">edit</a>', array( 'usr_id' ) );
    $tableGenerator->addColumn( '', '<a href="admin.php?remove_user=%s">remove</a>', array( 'usr_id' ) );
    $tableGenerator->setData( $users );
    echo $tableGenerator->generateHTML();
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

    /* Edit User Page form */
    if ( isset( $_REQUEST[ 'edit_user' ] ) ) {
        $user_id = $_REQUEST[ 'edit_user' ];
        editUserPage( $user_id );
    }
}

/* Handle our different POST's on this page */
if ( $_SERVER[ 'REQUEST_METHOD'] === 'POST' ) {

    /* Submit received from edit user page */
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
