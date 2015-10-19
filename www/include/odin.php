<?php

include_once( "config.php" );

class Odin
{
    protected $dbcon;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
        $this->dbcon->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    protected function getTicket()
    {

        if (isset($_COOKIE[CUKY_NAME])) {
            $cuky = $_COOKIE[CUKY_NAME];
        } else {
            $cuky = '';
        }
        return $cuky;
    }
}

?>
