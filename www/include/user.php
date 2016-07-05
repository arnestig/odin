<?php
session_start();

include_once( "config.php" );
include_once( "usermanagement.php" );
include_once( "nwmanagement.php" );

class User
{
    private $dbcon;

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
            $this->setSessionDefaults($username);
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
    private function setSessionDefaults($username) {
        $userManager = new UserManagement();
        $nwManager = new NetworkManagement();
        $all_users = $userManager->getUsers( );
        $user_data = array();
        foreach ($all_users as $user) {
            if ($user[ 'usr_usern' ] === $username) {
                $user_data = $user;
            }
        }
        //TODO: change username to fullname in navbar?
        $_SESSION[ 'user_data' ] = $user_data;
        $_SESSION[ 'cur_network_id' ] = '1';
        $_SESSION[ 'show_all' ] = true;
        $_SESSION[ 'active_filter_tags' ] = array();
        $_SESSION[ 'host_rows' ] = '';
        $_SESSION[ 'filter_search' ] = '';
        $_SESSION[ 'max_pages' ] = 0;
        $_SESSION[ 'current_page' ] = 1;
        $_SESSION[ 'result_set' ] = null;
        $_SESSION[ 'networks' ] = null;
        $_SESSION[ 'locked_ips' ] = array();
        $_SESSION[ 'steal_not_seen' ] = true;
    }
}

?>
