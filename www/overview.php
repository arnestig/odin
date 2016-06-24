<?php
session_start();

include_once('include/nwmanagement.php');
include_once('include/html_frame.php');

//Default range-view (TODO: delete init after implemented as user-default)
if ( isset( $_REQUEST[ 'nw_id' ] ) ) {
  $_SESSION['cur_network_range'] = $_REQUEST[ 'nw_id' ];
}

// Tag defaults to just 'show_all' if it's req'ed by client.
if ( isset( $_REQUEST[ 'show_all' ] ) ) {
  $filter_tags_length = count($_SESSION[ 'active_filter_tags' ] );
  if ( !$_SESSION[ 'show_all' ] ) {
    $_SESSION[ 'show_all' ] = true;
    $_SESSION[ 'active_filter_tags' ] = array();
  } else if( $filter_tags_length > 0 ) {
    $_SESSION[ 'show_all' ] = false;
  }
}

if ( isset( $_REQUEST[ 'filter_tag' ] ) ) {
  $req_tag = $_REQUEST[ 'filter_tag' ];
  // Check if tag already is set in session
  $index = array_search($req_tag, $_SESSION[ 'active_filter_tags' ]);
  if ($index !== false) {
    unset($_SESSION[ 'active_filter_tags' ][$index]);
    $_SESSION[ 'active_filter_tags' ] = array_values($_SESSION[ 'active_filter_tags' ]);
  } else {
    $_SESSION[ 'active_filter_tags' ][] = $req_tag;
  }
  $filter_tags_length = count($_SESSION[ 'active_filter_tags' ] );
  if ($filter_tags_length === 0 || $filter_tags_length === 4) {
    $_SESSION[ 'active_filter_tags' ] = array();
    $_SESSION[ 'show_all' ] = true;
  } else {
    $_SESSION[ 'show_all' ] = false;
  }
}

//TODO: Search string from filter search. Validate input.
if ( isset( $_REQUEST[ 'filter_search' ])) {

}

//TODO: result pages req's. Both from text and buttons. Validate input.
if ( isset( $_REQUEST[ 'result_page' ] )) {
  $_SESSION[ 'result_page' ] = $_REQUEST[ 'result_page' ];
}

function network_ranges($view_range) {
  $networks = new NetworkManagement();
  $nw_array = $networks->getNetworks();
  foreach ($nw_array as $range) {
    echo '      <li role="presentation"';
    if ($view_range === $range["nw_base"]) {
      echo ' class="active"';
    }
    echo '
                ><a href="overview.php?nw_id='.$range["nw_base"].'">'.$range["nw_base"].'/'.$range["nw_cidr"].'</a></li>
      ';
  }
}

//Controlling the toggling view of filters
function active_filter($filter_tags) {
  echo '
                <td><a class="filter-link" href="overview.php?show_all=true"><div class="toggle '.compare_tags("show_all").'">Show all</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=free"><div class="toggle '.compare_tags("free").'"><div class="address-info free"></div>Free</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=free_but_seen"><div class="toggle '.compare_tags("free_but_seen").'"><div class="address-info free-but-seen"></div>Free (but seen)</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=taken"><div class="toggle '.compare_tags("taken").'"><div class="address-info taken"></div>Taken</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=taken_not_seen"><div class="toggle '.compare_tags("taken_not_seen").'"><div class="address-info taken-not-seen"></div>Taken (not seen)</div></a></td> 
  ';
}

function result_page() {
  echo '

  ';
}

function result_page_text() {
  return '1';
}

function result_page_max() {
  return '254';
}

function result_page_left() {
  return '1';
}

function result_page_right() {
  return '2';
}


//Helper function to active_filter()
function compare_tags($tag) {
  if ($tag === 'show_all' && $_SESSION['show_all']) {
    return 'active';
  }
  foreach ($_SESSION[ 'active_filter_tags' ] as $sesh_tag) {
    //echo 'active';
    if ($sesh_tag === $tag) {
      return 'active';
    }
  }
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
//Starts generating html
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
network_ranges($_SESSION['cur_network_range']);
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
active_filter($_SESSION[ 'active_filter_tags' ]);
echo '
                <td>&nbsp</td>
                <td>&nbsp</td>
              </tr>
              <tr>
                <td colspan="2" class="filter-bottom"><div class="filter-result"><em>234 567 addresses in result</em></div></td>
                <td colspan="2" class="filter-bottom">
                  <form class="form" method="get">
                    <div class="input-group input-group-sm">
                      <input type="text" name="filter_search" class="form-control" placeholder="Enter keywords">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="submit" value="Submit">Filter result</button>
                      </span>
                    </div>
                  </form>
                </td>
                <td class="filter-bottom" colspan="2">
                  <table class="table filter">
                    <tbody style="background-color:#eeeeee;">
                      <tr>
                        <td>&nbsp</td>
                        <td><div class="filter-bottom page">Page</div></td>
                        <td><form><input type="text" name="result_page" class="form-control input-sm result-page-field" placeholder="'.result_page_text().'" style="width:85%;margin-top:2px;"></form></td>
                        <td><div class="filter-bottom page">of '.result_page_max().'</div></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td class="filter-bottom">
                  <form>
                    <div class="input-group input-group-sm">
                      <div class="input-group-btn" style="padding-right:5px;">
                        <button class="btn btn-default" type="submit" name="result_page" value="'.result_page_left().'"><i class="glyphicon glyphicon-chevron-left"></i></button>
                        <button class="btn btn-default" type="submit" name="result_page" value="'.result_page_right().'"><i class="glyphicon glyphicon-chevron-right"></i></button>
                      </div>
                    </div>
                  </form>
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
tbody_content($_SESSION['cur_network_range']);
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
                  <!-- START - injection test -->
                  <tr class="'.bootstrap_color_tag().'">
                    <td data-toggle="collapse" data-target="#acc'.host_ip().'" class="accordion-toggle" id="'.host_ip().'"><i class="glyphicon glyphicon-triangle-right"></i></td>
                    <td>'.host_ip().'</td>
                    <td>'.host_name().'</td>
                    <td colspan="2">'.data_description_short().'</td>
                    <td>'.last_seen().'</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="12" class="hiddenRow">
                      <div class="hiddenNwDiv accordion-body collapse" id="acc'.host_ip().'">
                        <div class="row">
                          <div class="col-lg-6">
                            <h5>Data description</h5>
                            '.data_description().'
                          </div>
                          <div class="col-lg-3">
                            <h5>Last notified</h5>
                            '.last_notified().'
                            <div class="text-head-gutter"></div>
                            <h5>Lease expiry</h5>
                            '.lease_expiry().'  
                          </div>
                          <div class="col-lg-3">
                            <h5>User ID</h5>
                            <a mailto="'.user_mail().'"><i class="glyphicon glyphicon-envelope"></i>Arnold S</a>
                            <div class="text-head-gutter"></div>
                            <h5>Last scanned</h5>
                            '.last_scanned().'
                          </div>
                        </div>
                        <div class="row spacer-row"></div>
                      </div>
                    </td>
                  </tr>
                  <!-- END - injection test -->

                  <!-- START-Prototype -->
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
                  <!-- END-Prototype -->


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
$frame->doc_end();
?>