#!/usr/bin/perl

#  Odin - IP plan management and tracker
#  Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
#                           Jonas Berglund <jonas.jberglund@gmail.com>
#                           Martin Rydin <martin.rydin@gmail.com>
#
#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License along
#  with this program; if not, write to the Free Software Foundation, Inc.,
#  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

use strict;
use warnings;

use DBI;
use Data::Dumper;
use Net::Ping;
use Scalar::Util qw(looks_like_number);
use POSIX qw(strftime);
use threads;

our $running = 1;

sub signal_handler
{
    # signal our main loop that it's time to quit
    print "Exiting!\n";
    $::running = 0;
}

sub ping_host
{
    my($ip_address) = @_;

    my %retval;
    $retval{ ip_address } = $ip_address;
    $retval{ online } = 0;
    $retval{ timestamp } = strftime( "%F %T", localtime ); # %Y-%m-%d %H:%M:%S
    for my $proto ( qw{ tcp icmp } ) {
        if ( Net::Ping->new( $proto, 5 )->ping($ip_address) ) {
            $retval{ online } = 1;
            last;
        }
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
    
    $dbh->{ AutoCommit } = 0;

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
    }


    # sleep until next time we're checking the hosts
    sleep( 60 );
}

# clean up
$dbh->disconnect();
