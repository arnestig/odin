<?php


include_once('include/nwmanagement.php');
include_once('include/html_frame.php');

//Default range-view
$cur_range = "192.168.0.0";
if ( isset( $_REQUEST[ 'nw_id' ] ) ) {
  $cur_range = $_REQUEST[ 'nw_id' ];
}

// Tag defaults to just 'show_all' for initial page req AND if it's req'ed by client.
// If same tag exists twice in 'filter_tags' both are removed
$active_filter_tags = ['show_all'];
if ( isset( $_REQUEST[ 'filter_tags' ] ) ) {
  foreach ($_REQUEST[ 'filter_tags' ] as $set_tag) {
    if ($set_tag === 'show_all') {
      $_REQUEST['filter_tags'] = [];
    }
  }
  if (count($_REQUEST) > 0) {
    
    $active_filter_tags = $_REQUEST['filter_tags'];
  }
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

// TODO - Keeping track of active filtertags via get-reqs
function active_filter($filter_tags) {
  echo '
                <td><div class="toggle active">Show all</div></td>
                <td><div class="toggle"><div class="address-info free"></div>Free</div></td>
                <td><div class="toggle"><div class="address-info free-but-seen"></div>Free (but seen)</div></td>
                <td><div class="toggle"><div class="address-info taken"></div>Taken</div></td>
                <td><div class="toggle"><div class="address-info taken-not-seen"></div>Taken (not seen)</div></td> 
  ';
}

// TODO - le grand master funktzione
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
              <p>This IP-range is intended for use of the R&D-section of ACME. For other uses, please contact <a mailto="#">IPmasta</a> before taking water over your head.</p>
            </div>
          </div>
          <!-- START - Filter and color-info row -->
          <table class="table filter small">
            <tbody>
              <tr>
';
active_filter($active_filter_tags);
echo '
                <td>&nbsp</td>
                <td>&nbsp</td>
              </tr>
              <tr>
                <td colspan="2" class="filter-bottom"><div class="filter-result"><em>234 567 addresses in result</em></div></td>
                <td colspan="2" class="filter-bottom">
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Enter keywords">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Filter result</button>
                    </span>
                  </div>
                </td>
                <td class="filter-bottom" colspan="2">
                  <table class="table filter">
                    <tbody style="background-color:#eeeeee;">
                      <tr>
                        <td>&nbsp</td>
                        <td><div class="filter-bottom page">Page</div></td>
                        <td><input type="text" class="form-control input-sm" placeholder="1" style="width:85%;margin-top:2px;"></td>
                        <td><div class="filter-bottom page">of 254</div></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td class="filter-bottom">
                  <div class="input-group input-group-sm">
                    <div class="input-group-btn" style="padding-right:5px;">
                      <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-left"></i></button>
                      <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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