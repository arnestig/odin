<?php

session_start();

include_once('include/html_frame.php');

$frame = new HTMLframe();
$frame->doc_start("Book Address");
$frame->doc_nav('', $_SESSION[ 'username' ]);

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6">
          <h3>BOOK ADDRESS</h3>
          <p>Please provide following details for your choosen addresses:</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">192.168.0.1</h3>
            </div>
            <div class="panel-body">
              <form>
                <div class="form-group">
                  <label for="hostName">Host name</label>
                  <input type="email" class="form-control" id="hostName1" placeholder="Host name">
                </div>
                <div class="form-group">
                  <label for="dataDescription">Data description</label>
                  <textarea class="form-control" rows="3" id="dataDescription1" placeholder="Data description"></textarea>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-offset-2 col-lg-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">192.168.0.2</h3>
            </div>
            <div class="panel-body">
              <form>
                <div class="form-group">
                  <label for="hostName">Host name</label>
                  <input type="email" class="form-control" id="hostName2" placeholder="Host name">
                </div>
                <div class="form-group">
                  <label for="dataDescription">Data description</label>
                  <textarea class="form-control" rows="3" id="dataDescription2" placeholder="Data description"></textarea>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
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