<?php

include_once( "config.php" );
include_once( "odin.php" );

class UserManagement extends Odin
{

    // Returns user id of added user
    public function addUser( $username, $password, $serverpwd, $firstname, $lastname, $email )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT add_user( ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $username, $password, $serverpwd, $firstname, $lastname, $email ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['add_user'] .'";');
        $results = $sth->fetch( PDO::FETCH_COLUMN, 0 );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results['usr_id'];
    }

    public function removeUser( $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT remove_user( ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id ) );
    }

    public function updateUser( $user_id, $username, $password, $serverpwd, $firstname, $lastname, $email )
    {
        $sth = $this->dbcon->prepare( "SELECT update_user( ?, ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id, $username, $password, $serverpwd, $firstname, $lastname, $email ) );
    }

    public function adminUpdateUser( $user_id, $username, $firstname, $lastname, $email, $privileges )
    {
        $sth = $this->dbcon->prepare( "SELECT admin_update_user( ?, ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $user_id, $username, $firstname, $lastname, $email, $privileges ) );
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
