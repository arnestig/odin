<?php

include_once( "config.php" );

class NetworkManagement
{
    private $dbcon;
    public function __construct()
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
    }

    public function addNetwork( $network, $cidr )
    {
        $base = $this->findBaseInNetwork( $network, $cidr );
        
        /* add the base network first into networks */
        $sth = $this->dbcon->prepare( "INSERT INTO networks( nw_base, nw_cidr ) VALUES( ?, ? )" );
        $sth->execute( array( $network, $cidr ) ) or die ( "Error during execute\n" );

        /* get our network id for the insert just done */
        $sth = $this->dbcon->prepare( "SELECT nw_id FROM networks WHERE nw_base = ? AND nw_cidr = ?" );
        $sth->execute( array( $network, $cidr ) ) or die ("Error during execute\n" );
        $nw_id = $sth->fetch();

        /* then add all hosts based on this network */
        $sth = $this->dbcon->prepare( "INSERT INTO hosts( hostid, nw_id ) VALUES ( ?, ? )" );
        $base = ip2long( $base );
        for( $i = 0; $i < $this->nHostsInNetwork( $cidr ); $i++ ) {
            # we only want hosts, not base networks or broadcasts
            $last_octet = end( explode( ".", long2ip( $base ) ) );
            if ( $last_octet != 255 && $last_octet != 0 ) {
                # insert our host to the hosts table
                $sth->execute( array( $base, $nw_id[ 0 ] ) ) or die ("Error during execute\n") or die ( "Error during execute\n" );
            }
            $base++;
        }


    }

    public function removeNetwork()
    {

    }

    public function updateNetwork()
    {

    }

    public function getNetworks()
    {

    }

    private function nHostsInNetwork( $cidr )
    {
        return pow( 2, ( 32 - $cidr ) );
    }

    private function findBaseInNetwork( $network, $cidr )
    {
        $firsthost = ip2long( $network ) & ip2long( $this->cidr2mask( $cidr ) );
        return long2ip( $firsthost );
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
