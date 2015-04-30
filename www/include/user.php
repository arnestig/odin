<?php

include_once( "config.php" );

class User
{
    private $dbcon;
    private $user_data;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
    }

    public function login( $username, $password )
    {
        $sth = $this->dbcon->prepare( "SELECT authenticate( ?, ? )" );
        $sth->execute( array( $username, $password ) );
        $result = $sth->fetch();
        if ( $result[ 'authenticate' ] == true ) {
            $_SESSION['active'] = true;
            echo "user logged in\n";
        } else {
            echo "login failed for $username\n";
        }
    }

    public function logout()
    {
        $_SESSION['active'] = false;
        session_destroy();
    }

    public function getSession()
    {
        return $_SESSION['active'];
    }
}

?>
