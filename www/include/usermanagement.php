<?php

include_once( "config.php" );

class UserManagement
{
    private $dbcon;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
    }

    public function addUser( $username, $password, $firstname, $lastname, $email )
    {
        $sth = $this->dbcon->prepare( "SELECT add_user( ?, ?, ?, ?, ? )" );
        $sth->execute( array( $username, $password, $firstname, $lastname, $email ) );
    }

    public function removeUser( $user_id )
    {
    
    }

    public function updateUser( $user_id, $username, $password, $firstname, $lastname, $email )
    {

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
        $results = $sth->fetchAll();
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }
}

?>
