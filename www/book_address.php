<?php

session_start();

include_once('include/html_frame.php');
include_once('include/nwmanagement.php');


// The Booking Controller
// TODO: check that posted IPs match those in sesh var
if (isset($_POST[ 'book_addresses' ])) {
  $nwmanagement = new NetworkManagement();

  $nbr_of_items = $_POST[ 'nbr_of_ips' ];
  $host_ip = '';
  $host_name = '';
  $host_desc = '';

  for ($i = 0; $i < $nbr_of_items; $i++) {

    $host_ip = $_POST[ 'hostIP'.$i ];
    $host_name = $_POST[ 'hostName'.$i ];
    $host_desc = $_POST[ 'dataDescription'.$i ];

    echo $host_ip.': '.$host_name.' &&&& '.$host_desc;

    if ( $nwmanagement->leaseHost( $host_ip, $_SESSION[ 'user_data' ][ 'usr_id' ], $host_name, $host_desc ) == true ) {
           echo 'ouafsdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddh';
    }
  }
} else {
  header('Location: overview.php');
}

$frame = new HTMLframe();
$frame->doc_start("Book Address");
$frame->doc_nav('', $_SESSION[ 'user_data' ][ 'usr_usern' ]);


function gen_address_form() {
  $form_body = '';
  foreach( array_keys($_SESSION[ 'locked_ips' ]) as $ip) {
    $form_body .= gen_address_form_row($ip);
  }
  return $form_body;
}

function gen_address_form() {
  $form_body = '';
  $index = 0;
  $nwmanagement = new NetworkManagement();
  $reservedIPs = $nwmanagement->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
  print_r($reservedIPs);
  
  foreach( array_values($reservedIPs) as $ip) {
    $form_body .= gen_address_form_row($ip, $index);
    $index++;
  }
  return $form_body;
}

function gen_address_form_row($ip, $index) {
  return '
        <div class="row">
          <div class="col-lg-offset-2 col-lg-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">'.$ip.'</h3>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <label for="hostName">Host name</label>
                  <input type="email" class="form-control" id="hostName1" placeholder="Host name">
                </div>
                <div class="form-group">
                  <label for="dataDescription">Data description</label>
                  <textarea class="form-control" rows="3" id="dataDescription1" placeholder="Data description"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>';
}

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6">
          <h3>BOOK ADDRESS</h3>
          <p>Please provide following details for your choosen addresses:</p>
        </div>
      </div>
      <form>
      '.gen_address_form().'
      </form>
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6 pull-right">
          <a class="btn btn-default" href="networks.html" role="button">Cancel</a>
          <a class="btn btn-success" href="networks.html" role="button" id="#bookBtn">Book</a>
            <!-- Real btn
            <button type="submit" class="btn btn-default">Log in</button>
            -->
        </div>
      </div> 
    </div>
';

$frame->doc_end();

?>
