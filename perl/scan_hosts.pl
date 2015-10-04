#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Data::Dumper;
use Net::Ping;
use POSIX qw(strftime);
use threads;

our %hosts_to_scan;
our @hosts_to_scan;

sub ping_host
{
    my($ip_address) = @_;

    my $ph = Net::Ping->new();
    my %retval;
    $retval{ ip_address } = $ip_address;
    $retval{ online } = 0;
    $retval{ timestamp } = strftime( "%F %T", localtime ); # %Y-%m-%d %H:%M:%S
    if ( $ph->ping($ip_address) ) {
        $retval{ online } = 1;
    }
    return \%retval;
}

# connect
my $dbh = DBI->connect("DBI:Pg:dbname=odin;host=localhost", "dbaodin", "gresen", {'RaiseError' => 1});

# execute SELECT query
my $sth = $dbh->prepare( 'BEGIN;SELECT * FROM get_hosts_to_scan(); FETCH ALL IN "<unnamed portal 1>"');
$sth->execute();

# iterate through resultset
#my $hash_ref = $sth->fetchall_arrayref( 1 );
#foreach ( keys $hash_ref ) {
    #foreach my $key ( keys $hash_ref->{ $_ } ) {
        #if ( not defined $hash_ref->{ $_ }{ $key } ) {
            #$hash_ref->{ $_ }{ $key } = "";
        #}
        #print "$_ - $key: $hash_ref->{ $_ }{ $key }\n";
    #}
#}

my $array_dump = $sth->fetchall_arrayref( );
foreach ( @{ $array_dump } ) {
    push( @::hosts_to_scan, @{ $_ }[ 0 ] );
}
$sth->finish();

while ( @hosts_to_scan ) {
    my @hosts_to_add;
    my @thread_pool;
    if ( $#hosts_to_scan >= 19 ) {
        @hosts_to_add = splice( @hosts_to_scan, -20 );
    } else {
        @hosts_to_add = splice( @hosts_to_scan, -$#hosts_to_scan );
    }
    foreach my $target_ip ( @hosts_to_add ) {
        my $thread = threads->new(\&ping_host, $target_ip );
        push( @thread_pool, $thread );
    }

    foreach ( @thread_pool ) {
        my %retval = %{ $_->join() };
        $::hosts_to_scan{ $retval{ ip_address } }{ online } = $retval{ online };
        $::hosts_to_scan{ $retval{ ip_address } }{ timestamp } = $retval{ timestamp };
    }
}

#$dbh->{ AutoCommit } = 0; # TODO: disable autocommit

foreach ( keys %::hosts_to_scan ) {
    my $sth = $dbh->prepare( 'SELECT update_host_status( ?, ?, ? )' );
    $sth->bind_param( 1, $_ );
    $sth->bind_param( 2, $::hosts_to_scan{ $_ }{ online } );
    $sth->bind_param( 3, $::hosts_to_scan{ $_ }{ timestamp } );
    $sth->execute();
}

#$dbh->commit(); # TODO: disable autocommit

# clean up
$dbh->disconnect();
