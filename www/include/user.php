<?php
session_start();

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
            setcookie(CUKY_NAME, $result[ 'authenticate' ]);
            $this->setSessionDefaults();
            return true;
        } else {
            return false;
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

    //TODO: no hardcoding of nw ranges and other schtuff...
    private function setSessionDefaults() {
        $_SESSION['cur_network_range'] = "192.168.0.0";
        $_SESSION['show_all'] = true;
        $_SESSION['active_filter_tags'] = array();
        $_SESSION['filter_search'] = "";
        $_SESSION['max_pages'] = "";
        $_SESSION['current_page'] = "1";
    }
}

?>
