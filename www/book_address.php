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

include_once('include/session.php'); # always include this file first
include_once('include/html_frame.php');
include_once('include/nwmanagement.php');

// Prohibits direct access to book address if no addresses are booked
$nwmanagement = new NetworkManagement();
$reserved = $nwmanagement->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
if (empty($reserved)) {
  header('Location: overview.php');
  exit;
}
// The Booking Controller
// TODO: check that posted IPs match those in sesh var
if (!empty($_POST[ 'book_addresses' ])) {
 

  $nbr_of_items = $_POST[ 'nbr_of_ips' ];
  $host_ip = '';
  $host_name = '';
  $host_desc = '';
  $_SESSION['booking_success'] = array();

  for ($i = 0; $i < $nbr_of_items; $i++) {
    $host_ip = $_POST[ 'hostIP'.$i ];
    $host_name = $_POST[ 'hostName'.$i ];
    $host_desc = $_POST[ 'hostDescription'.$i ];
    if ($host_ip != '') {
      if ( $nwmanagement->leaseHost( $host_ip, $_SESSION[ 'user_data' ][ 'usr_id' ], $host_name, $host_desc ) == true ) {
        $_SESSION['booking_success'][] = $host_ip;
      }
    }
  }
  header('Location: user_pages.php');
}

$frame = new HTMLframe();
$frame->doc_start("Book Address");
$frame->doc_nav('', $_SESSION[ 'user_data' ][ 'usr_firstn' ]." ".$_SESSION[ 'user_data' ][ 'usr_lastn'] );


function gen_address_form() {
  $form_body = '';
  $index = 0;
  $nwmanagement = new NetworkManagement();
  $reservedIPs = $nwmanagement->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
  //print_r($reservedIPs);
  
  foreach( $reservedIPs as $ip) {
    $form_body .= gen_address_form_row($ip, $index);
    $index++;
  }
  $form_body .= gen_form_footer($index);
  return $form_body;
}


function gen_address_form_row($ip, $index) {
  return '
        <div class="row book-address-container" id="book'.$ip.'">
          <div class="col-lg-offset-2 col-lg-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">'.$ip.'<span id="rmbtn'.$ip.'" class="glyphicon glyphicon-remove book-address-remove pull-right"></span></h3>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <label for="hostName'.$index.'">Host name</label>
                  <input type="text" class="form-control" name="hostName'.$index.'" id="hostName'.$index.'" placeholder="Host name" required pattern="(?=.*[a-zA-Z0-9]).{1,}">
                  <input type="hidden" name="hostIP'.$index.'" value="'.$ip.'">
                </div>
                <div class="form-group">
                  <label for="hostDescription'.$index.'">Host description</label>
                  <textarea class="form-control host-description" rows="3" name="hostDescription'.$index.'" id="hostDescription'.$index.'" placeholder="Host description" required pattern="(?=.*[a-zA-Z0-9]).{1,}"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>';
}

function gen_form_footer($index) {
  return '
        <div class="row">
          <div class="col-lg-offset-2 col-lg-6 pull-right">
            <a class="btn btn-default" href="overview.php" role="button">Cancel</a>
            <input type="hidden" name="nbr_of_ips" value="'.$index.'">
            <input type="submit" name="book_addresses" value="Book Addresses" class="btn btn-info">
          </div>
        </div>
  ';
}

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6">
          <h3>BOOK ADDRESS</h3>
          <p>Please provide following details for your choosen addresses:</p>
        </div>
      </div>
      <form class="form" method="POST" action="book_address.php" autocomplete="off">
        '.gen_address_form().'
      </form>
    </div>
';

$frame->doc_end();

?>
