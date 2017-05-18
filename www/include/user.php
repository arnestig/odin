<?php

/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2017  Tobias Eliasson <arnestig@gmail.com>
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
include_once( "usermanagement.php" );
include_once( "nwmanagement.php" );
include_once( "settings.php" );

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
        $settings = new Settings();
        $settings_group = $settings->getSettings('hosts');
        $steal_not_seen = false;
        foreach ($settings_group as $setting) {
            if ($setting['s_name'] == 'host_steal_not_seen' && $setting['s_value'] == 'checked') {
                $steal_not_seen = true;
            }
        }
        $all_users = $userManager->getUsers( );
        $user_data = array();
        foreach ($all_users as $user) {
            if ($user[ 'usr_usern' ] === $username) {
                $user_data = $user;
            }
        }
        $_SESSION[ 'user_data' ] = $user_data;
        $nw = $nwManager->getNetworks();
        $_SESSION[ 'cur_network_id' ] = $nw[0][ 'nw_id' ];
        $_SESSION[ 'show_all' ] = true;
        $_SESSION[ 'active_filter_tags' ] = array();
        $_SESSION[ 'host_rows' ] = '';
        $_SESSION[ 'filter_search' ] = '';
        $_SESSION[ 'max_pages' ] = 0;
        $_SESSION[ 'current_page' ] = 1;
        $_SESSION[ 'result_set' ] = null;
        $_SESSION[ 'networks' ] = null;
        $_SESSION[ 'steal_not_seen' ] = $steal_not_seen;
    }
}

?>
