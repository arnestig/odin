<?php

session_start();

include_once('include/html_frame.php');

$frame = new HTMLframe();
$frame->doc_start("My addresses");

$frame->doc_nav('View your addresses', $_SESSION[ 'user_data' ][ 'usr_usern' ] );


echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-1 col-lg-8">
          
          <div class="row">
            <div class="col-lg-12">
              <h3>My reservations <i class="glyphicon glyphicon-th-list"></i></h3>
              <p>Here you can view the status for your reserved addresses, and extend lapsing reservations or terminate your lease.</p>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>Host IP</th>
                    <th>Host name</th>
                    <th>Data description</th>
                    <th>Lease expiry</th>
                    <th>Extend<br>lease</th>
                    <th>Terminate<br>lease</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>192.168.5.150</td>
                    <td>LaserVD7</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td><input type="checkbox" class="check-extend"></td>
                    <td><input type="checkbox" class="check-terminate"></td>
                  </tr>
                  <tr>
                    <td>192.168.5.151</td>
                    <td>LaserVD5</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td><input type="checkbox" class="check-extend"></td>
                    <td><input type="checkbox" class="check-terminate"></td>
                  </tr>
                  <tr>
                    <td>192.168.5.152</td>
                    <td>LaserVD8</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td><input type="checkbox" class="check-extend"></td>
                    <td><input type="checkbox" class="check-terminate"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- FIXED RIGHT PANEL - START -->

        <div class="col-lg-3">
          <div class="affix fixed-right fixed-extend">
            <div class="panel panel-default">
              <div class="panel-heading">
                <p>Choosen addresses</p>
              </div>
              <div class="panel-body">
                <p>192.168.5.151</p>
                <p>192.168.5.152</p>
              </div>
              <div class="bookAddrBtn">
                <a class="btn btn-success" href="book_address.html" role="button">Extend leases</a>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <p>Choosen addresses</p>
              </div>
              <div class="panel-body">
                <p>192.168.5.151</p>
                <p>192.168.5.152</p>
              </div>
              <div class="bookAddrBtn">
                <a class="btn btn-danger" href="book_address.html" role="button">Terminate leases</a>
              </div>
            </div>


          </div>
        </div>

        <!-- FIXED RIGHT PANEL - END -->

      </div>  
    </div>
';

$frame->doc_end();

?>