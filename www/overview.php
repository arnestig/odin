<?php


include_once('include/nwmanagement.php');
include_once('include/html_frame.php');

//Default range-view
$cur_range = "192.168.0.0";
if ( isset( $_REQUEST[ 'nw_id' ] ) ) {
  $cur_range = $_REQUEST[ 'nw_id' ];
}

function network_ranges($view_range) {
  $networks = new NetworkManagement();
  $nw_array = $networks->getNetworks();
  foreach ($nw_array as $range) {
    echo '
                <li role="presentation"';
    if ($view_range === $range["nw_base"]) {
      echo ' class="active"';
    }
    echo '
                ><a href="overview.php?nw_id='.$range["nw_base"].'">'.$range["nw_base"].'/'.$range["nw_cidr"].'</a></li>
      ';
  }
}

//le grand master funktzione
function tbody_content($view_range) {
  $networks = new NetworkManagement();
  $hosts_array = $networks->getHosts(1);
  foreach ($hosts_array as $host_info) {
    //echo $host_info['host_ip'];
  }
}

$frame = new HTMLframe();
$frame->doc_start("Hosts");
$frame->doc_nav("Overview");




//Range selection (with desc.) and info/filter panel below
echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-1 col-lg-8">
          <div class="row">
            <div class="col-lg-12">
              <ul class="nav nav-tabs"> 
';
network_ranges($cur_range);
echo '
              </ul>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <h3>Description</h3>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <p>This IP-range is intended for use of the R&D-section of Kapsch. For other uses, please contact <a mailto="#">IPmasta</a> before taking water over your head.</p>
            </div>
          </div>
          <!-- START - Filter and color-info row -->
          <div class="row filter-row">
            <div class="col-lg-2">
              <div class="row">
                <div class="col-lg-12">
                  <div class="address-info free"></div>Free
                </div>
                <div class="col-lg-12">
                  <div class="address-info taken"></div>Taken
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="row">
                <div class="col-lg-12">
                  <div class="address-info free-but-seen"></div>Free - but seen
                </div>
                <div class="col-lg-12">
                  <div class="address-info taken-not-seen"></div>Taken - but not seen
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <form class="form">
                <div class="row">
                  <div class="form-group col-lg-12">
                    <input type="text" class="form-control input-sm" id="keywordSearch" placeholder="Enter keywords">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-lg-6">
                    <select class="form-control input-sm" id="sel1">
                      <option>Show all</option>
                      <option>Free</option>
                      <option>Taken</option>
                      <option>Free - but seen</option>
                      <option>Taken - but not seen</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-6">
                    <button class="btn btn-sm btn-default">Search</button>
                  </div>
                </div>  
              </form>
            </div>
          </div>
          <!-- END - Filter and color-info row -->
';

// Host row (layout-element) start snippet
echo '
          <div class="row">
            <div class="col-lg-12">
';
tbody_content($cur_range);
// Host table
// GENERATE
echo '
              <table class="table table-condensed nw-table">
                <thead>
                  <tr>
                    <th></th>
                    <th>Host IP</th>
                    <th>Host name</th>
                    <th colspan="2">Data description</th>
                    <th>Last seen</th>
                    <th>Reserve</th>
                  </tr>
                </thead>
                <tbody>


                  <tr class="warning">
                    <td data-toggle="collapse" data-target="#demo1" class="accordion-toggle" id="192.168.0.1"><i class="glyphicon glyphicon-triangle-right"></i></td>
                    <td>192.168.0.1</td>
                    <td>RdCam</td>
                    <td colspan="2">E4S. Part of cool...</td>
                    <td>20160530</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="12" class="hiddenRow">
                      <div class="hiddenNwDiv accordion-body collapse" id="demo1">
                        <div class="row">
                          <div class="col-lg-6">
                            <h5>Data description</h5>
                            E4S. Part of cool system that does very impressive stuff but needs to be researched and developed to even higher levels of impressiveness. ArticleID: 345-578. Hardware located in the best of spots in the Lab on floor 3.
                          </div>
                          <div class="col-lg-3">
                            <h5>Last notified</h5>
                            20160514 - 17:10
                            <div class="text-head-gutter"></div>
                            <h5>Lease expiry</h5>
                            20160814 - 10:04  
                          </div>
                          <div class="col-lg-3">
                            <h5>User ID</h5>
                            <a mailto="#"><i class="glyphicon glyphicon-envelope"></i>Arnold S</a>
                            <div class="text-head-gutter"></div>
                            <h5>Last scanned</h5>
                            20160606 - 15:45
                          </div>
                        </div>
                        <div class="row spacer-row"></div>
                      </div>
                    </td>
                  </tr>


                </tbody>
              </table>
';

// Host row (layout-element) end snippet
echo '
            </div>
          </div>
';

// Fixed right panel
echo '
        </div>

    <!-- FIXED RIGHT PANEL AND CHECKBOX FORM - START -->
        <form>
        <div class="col-lg-3">
          <div class="affix fixed-right" id="choosenAddrDiv">
            <div class="panel panel-default">
              <div class="panel-heading">
                <p>Choosen addresses</p>
              </div>
              <div class="panel-body" id="choosenAddr">
                <p></p>
              </div>
              <div class="bookAddrBtn">
                <a class="btn btn-success" href="book_address.html" role="button">Book addresses</a>
              </div>
            </div>
          </div>
        </div>
        </form>
    <!-- FIXED RIGHT PANEL AND FORM - END -->

      </div>
    </div>
';

HTMLframe::doc_end();

?>