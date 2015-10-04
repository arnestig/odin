#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Data::Dumper;
use Net::Ping;
use POSIX qw(strftime);
use threads;

our $running = 1;
our $update_interval = 300; # 5 minutes

sub signal_handler
{
    # signal our main loop that it's time to quit
    print "Exiting!\n";
    $::running = 0;
}

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

# setup our signal handler
$SIG{ INT } = \&signal_handler;

# connect
my $dbh = DBI->connect("DBI:Pg:dbname=odin;host=localhost", "dbaodin", "gresen", {'RaiseError' => 1});

while ( $::running ) {
    my @hosts_to_scan;
    my %hostinfo;
    $dbh->{ AutoCommit } = 1;
    # select our stored procedure for hosts to scan
    my $sth = $dbh->prepare( 'BEGIN; SELECT * FROM get_hosts_to_scan();' );
    $sth->execute();

    # fetch our reference cursor
    my $cursor = $sth->fetch;
    $sth = $dbh->prepare( 'FETCH ALL IN "'.@{ $cursor }[ 0 ].'";' );
    $sth->execute();

    # get all objects to scan
    my $array_dump = $sth->fetchall_arrayref();
    foreach ( @{ $array_dump } ) {
        push( @hosts_to_scan, @{ $_ }[ 0 ] );
    }
    
    # cleanup database transaction
    $sth = $dbh->prepare( 'COMMIT;' );
    $sth->execute();
    $sth->finish();
    
    # iterate over all hosts to scan, 20 at a time
    while ( @hosts_to_scan ) {
        my @hosts_to_add;
        my @thread_pool;
        # only 20 at time, rest will be done next batch
        if ( $#hosts_to_scan >= 19 ) {
            @hosts_to_add = splice( @hosts_to_scan, -20 );
        } else {
            @hosts_to_add = splice( @hosts_to_scan, -$#hosts_to_scan-1 );
        }
        
        # start thread for each ping
        foreach my $target_ip ( @hosts_to_add ) {
            my $thread = threads->new(\&ping_host, $target_ip );
            push( @thread_pool, $thread );
        }

        # join the threads and collect result
        foreach ( @thread_pool ) {
            my %retval = %{ $_->join() };
            $hostinfo{ $retval{ ip_address } }{ online } = $retval{ online };
            $hostinfo{ $retval{ ip_address } }{ timestamp } = $retval{ timestamp };
        }
    }

    $dbh->{ AutoCommit } = 0;

    # update database with current status of the hosts
    foreach ( keys %hostinfo ) {
        my $sth = $dbh->prepare( 'SELECT update_host_status( ?, ?, ? )' );
        $sth->bind_param( 1, $_ );
        $sth->bind_param( 2, $hostinfo{ $_ }{ online } );
        $sth->bind_param( 3, $hostinfo{ $_ }{ timestamp } );
        $sth->execute();
        $sth->finish();
    }

    $dbh->commit();

    # sleep until next time we're checking the hosts
    sleep( $::update_interval );
}

# clean up
$dbh->disconnect();
