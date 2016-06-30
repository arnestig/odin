<?php

session_start();

include_once('include/html_frame.php');

$test = "no";

if (isset($_POST['book_address'])) {
  $index = 0;
  $test = "maybe";
  while (isset($_POST['ip' + $index])) {
    $test = "yes";
    $_SESSION['locked_ips'][] = $_POST['ip' + $index];
    $index++;
  }
  if (isset($_POST['check_ip_list'])) {
    $test = 'yesyseeys';
  }
} else {
  $test = 'post not set';
}

$frame = new HTMLframe();
$frame->doc_start("Book Address");
$frame->doc_nav('', $_SESSION[ 'username' ]);

function gen_address_form() {
  $form_body = '';
  foreach($_SESSION[ 'locked_ips' ] as $ip) {
    $form_body .= gen_address_form_row($ip);
  }
  return $form_body;
}  

function gen_address_form_row($ip) {
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
      '.print_r($_POST).'
      '.$test.'
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