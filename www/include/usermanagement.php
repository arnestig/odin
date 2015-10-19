<?php

include_once( "config.php" );
include_once( "odin.php" );

class UserManagement extends Odin
{

    public function addUser( $username, $password, $firstname, $lastname, $email )
    {
        $sth = $this->dbcon->prepare( "SELECT add_user( ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $this->getTicket(), $username, $password, $firstname, $lastname, $email ) );
    }

    public function removeUser( $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT remove_user( ? )" );
        $sth->execute( array( $user_id ) );
    }

    public function updateUser( $user_id, $username, $password, $firstname, $lastname, $email )
    {
        $sth = $this->dbcon->prepare( "SELECT update_user( ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( $user_id, $username, $password, $firstname, $lastname, $email ) );
    }
    
    public function getUserInfo( $user_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_users( ? )" );
        $sth->execute( array( $user_id ) );
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
        $sth = $this->dbcon->prepare( "SELECT get_users()" );
        $sth->execute();
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

#    private function getTicket()
#    {
#
#        if (isset($_COOKIE[CUKY_NAME])) {
#            $cuky = $_COOKIE[CUKY_NAME];
#        } else {
#            $cuky = '';
#        }
#        return $cuky;
#    }
}

?>
