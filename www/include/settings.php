<?php

include_once( "config.php" );

class Settings
{
    private $dbcon;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
    }

    public function changeSetting( $name, $value )
    {
        $sth = $this->dbcon->prepare( "SELECT update_setting( ?, ?, ? )" );
        $sth->execute( array( '', $name, $value ) );
    }
    
    public function getSettings($settings_group_name)
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_settings( ?, ? )" );
        $sth->execute( array( '', $settings_group_name) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_settings'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC);
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getSettingGroups()
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_setting_groups( ? )" );
        $sth->execute( array( '') );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_setting_groups'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC);
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }
}

?>
