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
        $sth = $this->dbcon->prepare( "SELECT usr_id,usr_usern,usr_lastn,usr_firstn,usr_email FROM users WHERE usr_usern = ? AND usr_pwd = ?" );
        $sth->execute( array( $username, $password ) ) or die ("Error during execute\n");
        $result = $sth->fetch();
        if ( isset( $result[ 'usr_id' ] ) ) {
            $_SESSION['active'] = true;
            $this->user_data[ 'usr_id' ] = $result[ 'usr_id' ];
            $this->user_data[ 'usr_usern'] = $result[ 'usr_usern' ];
            $this->user_data[ 'usr_firstn'] = $result[ 'usr_firstn' ];
            $this->user_data[ 'usr_lastn'] = $result[ 'usr_lastn' ];
            $this->user_data[ 'usr_email'] = $result[ 'usr_email' ];
            echo "user logged in: " . $this->user_data[ 'usr_id' ] . "\n";
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
