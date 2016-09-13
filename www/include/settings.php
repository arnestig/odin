<?php

/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
                            Jonas Berglund <jonas.jberglund@gmail.com>
                            Martin Rydin <martin.rydin@gmail.com>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

*/

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

    public function getSettingValue( $settings_name )
    {
        $sth = $this->dbcon->prepare( "SELECT get_setting_value( ?, ? )" );
        $sth->execute( array( '', $settings_name) );
        $results = $sth->fetchAll( PDO::FETCH_COLUMN, 0 );
        unset($sth);

        return $results[ 0 ];
    }
}

?>
