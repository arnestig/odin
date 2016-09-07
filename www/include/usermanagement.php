<?php

include_once( "config.php" );
include_once( "odin.php" );

class UserManagement extends Odin
{

    // Returns user id of added user
    public function addUser( $username, $password, $serverpwd, $firstname, $lastname, $email, &$errmsg, &$new_usr_id )
    {
        $this->dbcon->beginTransaction();
        $result = false;
        $sth = $this->dbcon->prepare( "SELECT * FROM add_user( ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $username, $password, $serverpwd, $firstname, $lastname, $email ) );
        $sth->bindColumn( 1, $result, PDO::PARAM_BOOL|PDO::PARAM_INPUT_OUTPUT );
        $sth->bindColumn( 2, $errmsg, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT );
        $sth->bindColumn( 3, $new_usr_id, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT );

        $sth->fetch( PDO::FETCH_BOUND );
        $this->dbcon->commit();

        unset($sth);
        return $result;
    }

    public function removeUser( $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT remove_user( ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id ) );
    }

    public function updateUser( $user_id, $username, $password, $serverpwd, $firstname, $lastname, $email, &$errmsg )
    {
        $this->dbcon->beginTransaction();
        $result = false;
        $sth = $this->dbcon->prepare( "SELECT * FROM update_user( ?, ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id, $username, $password, $serverpwd, $firstname, $lastname, $email ) );
        $sth->bindColumn( 1, $result, PDO::PARAM_BOOL|PDO::PARAM_INPUT_OUTPUT );
        $sth->bindColumn( 2, $errmsg, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT );
        $sth->fetch( PDO::FETCH_BOUND );
        $this->dbcon->commit();

        unset($sth);
        return $result;
    }

    public function adminUpdateUser( $user_id, $username, $firstname, $lastname, $email, $privileges, &$errmsg )
    {
        $this->dbcon->beginTransaction();
        $result = false;
        $sth = $this->dbcon->prepare( "SELECT * FROM admin_update_user( ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id, $username, $firstname, $lastname, $email, $privileges ) );
        $sth->bindColumn( 1, $result, PDO::PARAM_BOOL|PDO::PARAM_INPUT_OUTPUT );
        $sth->bindColumn( 2, $errmsg, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT );
        $sth->fetch( PDO::FETCH_BOUND );
        $this->dbcon->commit();

        unset($sth);
        return $result;
    }
    
    public function getUserInfo( $user_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_users( ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_users'] .'";');
        $results = $sth->fetch( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }


    public function getUsers()
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_users( ? )" );
        $sth->execute( array( $this->getTicket() ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_users'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }
}
?>
