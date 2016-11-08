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

class NetworkManagement
{
    private $dbcon;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
        $this->dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function addNetwork( $network, $cidr_mask, $description, &$errmsg )
    {
        // check if we have gotten a CIDR or netmask
        if ( $this->isCIDR( $cidr_mask ) == false ) {
            if ( $this->isNetmask( $cidr_mask ) == false ) {
                // TODO: Exception here, we can't add this since it's not a valid CIDR or netmask
            } else {
                $cidr_mask = $this->mask2cidr( $cidr_mask );
            }
        }

        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT * FROM add_network( ?, ?, ?, ? )" );
        $sth->execute( array( '', $network, $cidr_mask, $description ) );
        $sth->bindColumn( 1, $result, PDO::PARAM_BOOL|PDO::PARAM_INPUT_OUTPUT );
        $sth->bindColumn( 2, $errmsg, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT );

        $sth->fetch( PDO::FETCH_BOUND );
        $this->dbcon->commit();

        unset($sth);
        return $result;
    }

    public function updateNetwork( $network_id, $network_description )
    {
        $sth = $this->dbcon->prepare( "SELECT update_network( ?, ?, ? )" );
        $sth->execute( array( '', $network_id, $network_description ) );
    }

    public function removeNetwork( $network_id )
    {
        $sth = $this->dbcon->prepare( "SELECT remove_network( ?, ? )" );
        $sth->execute( array( '', $network_id ) );
    }

    public function getNetworkInfo( $network_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_networks( ?, ? )" );
        $sth->execute( array( '', $network_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_networks'] .'";');
        $results = $sth->fetch( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getNetworks()
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_networks( ? )" );
        $sth->execute( array( '' ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_networks'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getNetworkUsers( $network_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_network_users( ?, ? )" );
        $sth->execute( array( '', $network_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_network_users'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getHostInfo()
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_hosts( ?, ? )" );
        $sth->execute( array( '', $user_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_hosts'] .'";');
        $results = $sth->fetch( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getHosts( $network_id, $page_offset = 0, $items_per_page = 100, $search_string = "", $filter_bit_mask )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_hosts( ?, ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $network_id, $page_offset, $items_per_page, $search_string, $filter_bit_mask ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_hosts'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function updateHost( $host_ip, $user_id, $host_name, $host_description )
    {
        $sth = $this->dbcon->prepare( "SELECT update_host( ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id, $host_name, $host_description ) );
        $result = $sth->fetch();
    }

    public function reserveHost( $host_ip, $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT reserve_host( ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id ) );
        $result = $sth->fetch();
        return $result[ 'reserve_host' ];
    }

    public function unreserveHost( $host_ip, $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT unreserve_host( ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id ) );
        $result = $sth->fetch();
    }

    public function getReserved( $user_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_reserved( ?, ? )" );
        $sth->execute( array( '', $user_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_reserved'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_COLUMN, 0 );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }

    public function getUserHosts( $user_id )
    {
        $this->dbcon->beginTransaction();
        $sth = $this->dbcon->prepare( "SELECT get_user_hosts( ?, ? )" );
        $sth->execute( array( '', $user_id ) );
        $cursors = $sth->fetch();
        $sth->closeCursor();

        // get each result set
        $results = array();
        $sth = $this->dbcon->query('FETCH ALL IN "'. $cursors['get_user_hosts'] .'";');
        $results = $sth->fetchAll( PDO::FETCH_ASSOC );
        $sth->closeCursor();
        $this->dbcon->commit();
        unset($sth);

        return $results;
    }    

    public function leaseHost( $host_ip, $user_id, $host_name, $host_desc )
    {
        $sth = $this->dbcon->prepare( "SELECT lease_host( ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id, $host_name, $host_desc ) );
        $result = $sth->fetch();
        return $result[ 'lease_host' ];
    }

    public function terminateLease( $host_ip, $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT terminate_lease( ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id ) );
        $result = $sth->fetch();
    }

    public function extendLease( $host_ip, $user_id )
    {
        $sth = $this->dbcon->prepare( "SELECT extend_lease( ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $user_id ) );
        $result = $sth->fetch();
    }

    public function transferLease( $host_ip, $cur_usr_id, $new_usr_id, $admin_usr_id )
    {
        $sth = $this->dbcon->prepare( "SELECT transfer_lease( ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $host_ip, $cur_usr_id, $new_usr_id, $admin_usr_id ) );
        $result = $sth->fetch();
    }

    private function isCIDR( $cidr )
    {
        if( is_numeric( $cidr ) == true ) {
            return ( $cidr < 32 && $cidr > 0 );
        }
        return false;
    }

    private function isNetmask( $mask )
    {
        return ip2long($mask);
    }

    private function mask2cidr( $mask )
    {
        $long = ip2long( $mask );
        $base = ip2long( "255.255.255.255" );
        return 32 - log( ( $long ^ $base ) + 1, 2 );
    }

    private function cidr2mask( $cidr )
    {
        $mask = long2ip( 0xffffffff << ( 32 - $cidr ) );
        return $mask;
    }
}

?>
