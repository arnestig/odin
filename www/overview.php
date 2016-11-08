<?php

/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
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
include_once('include/nwmanagement.php');
include_once('include/usermanagement.php');
include_once('include/html_frame.php');

//Default range-view (TODO: delete init after implemented as user-default)
if ( isset( $_REQUEST[ 'nw_id' ] ) ) {
  $_SESSION[ 'cur_network_id' ] = $_REQUEST[ 'nw_id' ];
  $_SESSION[ 'current_page' ] = 1;
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
  $_SESSION[ 'current_page' ] = 1;
}

if ( isset( $_REQUEST[ 'filter_tag' ] ) ) {
  $req_tag = $_REQUEST[ 'filter_tag' ];
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
  $_SESSION[ 'current_page' ] = 1;
}

if ( isset( $_REQUEST[ 'filter_search' ]) ) {
  $_SESSION[ 'filter_search' ] = $_REQUEST[ 'filter_search' ];
  $_SESSION[ 'current_page' ] = 1;
}

//TODO: result pages req's. Both from text and buttons. Validate input.
if ( isset( $_REQUEST[ 'result_page' ] )) {
  $page = $_REQUEST[ 'result_page' ];
  if ($page >= 1 && $page <= $_SESSION[ 'max_pages' ] ) {
    $_SESSION[ 'current_page' ] = $_REQUEST[ 'result_page' ];
  }
}

// Handle POST from admin removing lease
if (!empty($_POST['admin-rm-lease']) && $_SESSION[ 'user_data' ][ 'usr_privileges' ] > 0) {
  if ($_POST['lease_holder'] != 0) {
    $ip = $_POST['admin-rm-lease'];
    $nwManager = new NetworkManagement();
    $nwManager->terminateLease($ip,$_POST['lease_holder']);
  }
}

if (!empty($_POST['admin-change-lease']) && $_SESSION[ 'user_data' ][ 'usr_privileges' ] > 0 ) {
    $newuser = $_POST[ 'changeLeaseUserId' ];
    $hostip = $_POST[ 'changeLeaseHostip' ];
    $currentuser = $_POST[ 'currentLeaseUserId' ];
    $nwManager = new NetworkManagement();
    $nwManager->transferLease( $hostip, $currentuser, $newuser, $_SESSION[ 'user_data' ][ 'usr_id' ] );
}

update_meta_data();

function calc_bit_mask() {
  $bitmask = 0;
  foreach ($_SESSION[ 'active_filter_tags' ] as $tag) {
      if ($tag == 'free') $bitmask |= 1;
      if ($tag == 'free_but_seen') $bitmask |= 2;
      if ($tag == 'taken') $bitmask |= 4;
      if ($tag == 'taken_not_seen') $bitmask |= 8;
  }
  return $bitmask;
}

//TODO: page per view from setting?
function update_meta_data() {
  $nwManager = new NetworkManagement();
  $result_set = $nwManager->getHosts($_SESSION[ 'cur_network_id' ], ($_SESSION[ 'current_page' ]-1), 100, $_SESSION[ 'filter_search' ], calc_bit_mask());
  $_SESSION[ 'networks' ] = $nwManager->getNetworks();
  $first_row = $result_set[0];
  $_SESSION[ 'result_set' ] = $result_set;
  $_SESSION[ 'host_rows' ] = $first_row['total_rows'];
  $_SESSION[ 'max_pages' ] = $first_row['total_pages'];

}

function user_select_list() {
    $userManager = new UserManagement();
    $ret_html = '';
    if ($_SESSION[ 'user_data' ][ 'usr_privileges' ] > 0) {
        $users = $userManager->getUsers();
        foreach ( $users as $user ) {
            $ret_html .= '<option value="'.$user[ 'usr_id'].'">'.$user[ 'usr_firstn' ].' '.$user[ 'usr_lastn'].'</option>';
        }
    }
    return $ret_html;
}

function network_ranges() {
  foreach ($_SESSION[ 'networks' ] as $range) {
    echo '      <li role="presentation"';
    if ($_SESSION[ 'cur_network_id' ] == $range['nw_id']) {
      echo ' class="active"';
    }
    echo '><a href="overview.php?nw_id='.$range["nw_id"].'">'.$range["nw_base"].'/'.$range["nw_cidr"].'</a></li>
      ';
  }
}

function network_description() {
  foreach ($_SESSION[ 'networks' ] as $range) {
    if ($_SESSION[ 'cur_network_id' ] == $range['nw_id']) {
      return $range['nw_description'];
    }
  }
}

//Controlling the toggling view of filters
function active_filter() {
  echo '
                <td><a class="filter-link" href="overview.php?show_all=true"><div class="toggle  '.compare_tags("show_all").'">Show all</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=free"><div class="toggle '.compare_tags("free").'"><div class="address-info free"></div>Free</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=free_but_seen"><div class="toggle '.compare_tags("free_but_seen").'"><div class="address-info free-but-seen"></div>Free (but seen)</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=taken"><div class="toggle '.compare_tags("taken").'"><div class="address-info taken"></div>Taken</div></a></td>
                <td><a class="filter-link" href="overview.php?filter_tag=taken_not_seen"><div class="toggle '.compare_tags("taken_not_seen").'"><div class="address-info taken-not-seen"></div>Taken (not seen)</div></a></td> 
  ';
}

//Helper function to active_filter()
function compare_tags($tag) {
  if ($tag === 'show_all' && $_SESSION['show_all']) {
    return 'active';
  }
  foreach ($_SESSION[ 'active_filter_tags' ] as $sesh_tag) {
    if ($sesh_tag === $tag) {
      return 'active';
    }
  }
}

function filter_search_placeholder() {
  if ($_SESSION[ 'filter_search' ] != null) {
    return $_SESSION[ 'filter_search' ];
  }
  return "Enter keywords";
}


//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------



function show_hosts() {
  $nw_manager = new NetworkManagement();
  $cur_reservations = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
  $generated_table = "";

  foreach ($_SESSION[ 'result_set' ] as $host_row) {
    $generated_table .= show_host_row_view($host_row, $cur_reservations);
  }

  return $generated_table;
}

function delete_option() {
  if ($_SESSION['user_data']['usr_privileges'] > 0) return '<th>Delete</th>';
  return '';
}

function show_host_row_view($row, $cur_reservations) {
  // ====================================
  // ALL POSSIBLE TAGS IN THE <INPUT> FOR CHECKBOX BELOW
  $ticked_box = '';

  //TODO: Fix if below?

  
  if ( in_array( $row[ 'host_ip' ], $cur_reservations ) ) $ticked_box = ' checked';


  // Set the disabled tag if other user owns lock
  $disabled = '';
  $td_tooltip = '';
  if ( $row[ 'reserved_by_usern' ] !== 'nobody' && $row[ 'reserved_by_usern' ] !== $_SESSION[ 'user_data' ][ 'usr_usern' ] ) $disabled = ' disabled';
 
  $checkbox = '<input type="checkbox" id="cb'.$row['host_ip'].'" name="Kbook_ip" value="'.$row['host_ip'].'"'.$ticked_box.' '.$disabled.'>';
  if ( $disabled === ' disabled' ) {
    $checkbox .= '<i class="glyphicon glyphicon-exclamation-sign"></i>';
    $td_tooltip = ' title="Another user reserved the host."';
  }
  if ($_SESSION[ 'user_data' ][ 'usr_id' ] == $row['usr_id']) {
    $checkbox = '';
  }

  $admin_rm_lease = '<td>&nbsp</td>';
  if ($_SESSION[ 'user_data' ][ 'usr_privileges' ] > 0 && 
    ($row['status'] == 4 || $row['status'] == 8)) {
    $admin_rm_lease = '<td>
                        <form class="rm-lease" method="POST" action="overview.php">
                          <button class="btn btn-small btn-danger" type="submit" name="admin-rm-lease" value="'.$row[ 'host_ip' ].'" style="padding:0px;">
                            <input type="hidden" name="lease_holder" value="'.$row[ 'usr_id' ].'"/>
                            <i class="glyphicon glyphicon-trash"></i>
                          </button>
                        </form>
                      </td>';    
  } else if ($_SESSION[ 'user_data' ][ 'usr_privileges' ] < 1) {
    $admin_rm_lease = '';
  }

  $user_email_html = '-';
  if (!empty($row['usr_email'])) {
    $user_email_html = '<a href="mailto:'.$row['usr_email'].'"><i class="glyphicon glyphicon-envelope"></i>'.$row['usr_firstn'].' '.$row['usr_lastn'].'</a>';
  }
  if (!empty($row['usr_id']) && $_SESSION[ 'user_data' ][ 'usr_privileges' ] > 0) {
      $user_email_html .= ' <br><a class="open-AdminChangeLeaseDialog"
                          data-hostip="'.$row[ 'host_ip' ].'"
                          data-curusrid="'.$row[ 'usr_id' ].'"
                          href="#adminChangeLeaseDialog" data-toggle="modal"
                          data-backdrop="static"><i class="glyphicon glyphicon glyphicon-wrench"></i>Transfer lease</a>';
  }


  // ====================================
  // COLORING AND CHECKBOX SETTING BELOW
  $bootstrap_color_tag = '';

  //Free (but seen)
  if ( $row[ 'status' ] == 2 ) {
    $bootstrap_color_tag = ' danger';
  }

  //Taken
  if ( $row[ 'status' ] == 4 ) {
    $bootstrap_color_tag = ' warning';
    $checkbox = '';
  }

  // Taken (not seen)
  if ( $row[ 'status' ] == 8) {
    $bootstrap_color_tag = ' info';
    if (!$_SESSION[ 'steal_not_seen' ]) $checkbox = '';
  }
  // ====================================

  return '
                  <tr class="'.$bootstrap_color_tag.'">
                    <td data-toggle="collapse" data-target="#acc'.str_replace('.', '', $row['host_ip']).'" class="accordion-toggle host-toggle" id="'.$row['host_ip'].'"><i class="glyphicon glyphicon-triangle-right"></i></td>
                    <td>'.$row['host_ip'].'</td>
                    <td>'.$row['host_name'].'</td>
                    <td colspan="2">'.substr($row['host_description'], 0, 30).' ...</td>
                    <td>'.substr($row['last_seen'], 0, 10).'</td>
                    <td class="check-reserve"'.$td_tooltip.'>'.$checkbox.'</td>
                    '.$admin_rm_lease.'
                  </tr>
                  <tr>
                    <td colspan="12" class="hiddenRow">
                      <div class="hiddenNwDiv accordion-body collapse" id="acc'.str_replace(".", "", $row['host_ip']).'">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="row">
                              <div class="col-lg-6">
                                <h5>Host description</h5>
                              </div>
                              <div class="col-lg-3">
                                <a id="log'.$row['host_ip'].'" class="history">
                                  <h6>Host log<span class="glyphicon glyphicon-list-alt"></span></h6>
                                </a>
                              </div>
                            </div>
                            '.$row['host_description'].'
                            <div class="text-head-gutter"></div>
                            <h5>User</h5>
                            '.$user_email_html.'
                          </div>
                          <div class="col-lg-3">
                            <h5>Last notified</h5>
                            '.$row['last_notified'].'
                            <div class="text-head-gutter"></div>
                            <h5>Lease expiry</h5>
                            '.$row['host_lease_expiry'].'  
                          </div>
                          <div class="col-lg-3">
                            <h5>Last seen</h5>
                            '.$row['last_seen'].'
                            <div class="text-head-gutter"></div>
                            <h5>Last scanned</h5>
                            '.$row['last_scanned'].'
                          </div>
                        </div>
                        <div class="row spacer-row"></div>
                      </div>
                    </td>
                  </tr>';
}

function basket() {
  $nw_manager = new NetworkManagement();
  $cur_reservations = $nw_manager->getReserved( $_SESSION[ 'user_data' ][ 'usr_id' ] );
  $content = '';
  $empty_basket = '';
  foreach ($cur_reservations as $ip) {
    //$content .= '<p class="basket-item" id="bi'.$ip.'">'.$ip.'</p>
    //';
    $content .= '<p id="bi'.$ip.'" class="cart-item">'.$ip.'<span id="rm'.$ip.'" class="glyphicon glyphicon-remove cart-remove pull-right"></span><p>
    ';
  }
  if (sizeof($cur_reservations) == 0) {
    $empty_basket = ' style="display:none;"';
    $content = '<p class="text-center">EMPTY</p>';
  }
  $basket = '<div class="affix fixed-right" id="choosenAddrDiv">
            <div class="panel panel-default">
              <div class="panel-heading">
                <p>Choosen addresses</p>
              </div>
              <div class="panel-body" id="choosenAddr">
                '.$content.'
              </div>
              <a href="book_address.php" role="button" class="btn btn-primary bookAddrBtn"'.$empty_basket.'>Book address(es)</a>
            </div>
          </div>
        </div>';
  return $basket;
}

//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------



$frame = new HTMLframe();
//Starts generating html
$frame->doc_start("Hosts");

echo '<!-- Modal CHANGE LEASE code start -->
    <div class="modal fade" id="adminChangeLeaseDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Transfer lease to user</h4>
          </div>
          <form class="form" method="POST" action="overview.php">
            <div class="modal-body">
              <div class="form-group">
                <label for="user">User</label>
                <select id="action" class="form-control" name="changeLeaseUserId">
                    '.user_select_list().'
                </select>
                <input type="hidden" value="" id="changeLeaseHostip" name="changeLeaseHostip"/>
                <input type="hidden" value="" id="currentLeaseUserId" name="currentLeaseUserId"/>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" name="admin-change-lease" value="Transfer"/>
            </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal CHANGE LEASE code end -->';

$frame->doc_nav("Overview", $_SESSION[ 'user_data' ][ 'usr_firstn' ]." ".$_SESSION[ 'user_data' ][ 'usr_lastn']);




//Range selection (with desc.) and info/filter panel below
echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-12">
              <ul class="nav nav-tabs"> 
';
network_ranges();
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
              <p>'.network_description().'</p>
            </div>
          </div>
          <!-- START - Filter and color-info row -->
          <table class="table filter small">
            <tbody>
              <tr>
';
active_filter();
echo '
                <td>&nbsp</td>
                <td>&nbsp</td>
              </tr>
              <tr>
                <td colspan="2" class="filter-bottom"><div class="filter-result"><em>'.$_SESSION[ 'host_rows' ].' address(es) in result</em></div></td>
                <td colspan="2" class="filter-bottom">
                  <form class="form" method="get">
                    <div class="input-group input-group-sm">
                      <input type="text" name="filter_search" class="form-control" placeholder="'.filter_search_placeholder().'">
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
                        <td><form><input type="text" name="result_page" class="form-control input-sm result-page-field" placeholder="'.$_SESSION[ 'current_page' ].'" style="width:85%;margin-top:2px;"></form></td>
                        <td><div class="filter-bottom page">of '.$_SESSION[ 'max_pages' ].'</div></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td class="filter-bottom">
                  <form>
                    <div class="input-group input-group-sm">
                      <div class="input-group-btn" style="padding-right:5px;">
                        <button class="btn btn-default" type="submit" name="result_page" value="'.($_SESSION[ 'current_page' ]-1).'"><i class="glyphicon glyphicon-chevron-left"></i></button>
                        <button class="btn btn-default" type="submit" name="result_page" value="'.($_SESSION[ 'current_page' ]+1).'"><i class="glyphicon glyphicon-chevron-right"></i></button>
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

//tbody_content($_SESSION['cur_network_id']);

echo '
              <table class="table table-condensed nw-table">
                <thead>
                  <tr>
                    <th></th>
                    <th>Host IP</th>
                    <th>Host name</th>
                    <th colspan="2">Host description</th>
                    <th>Last seen</th>
                    <th>Reserve</th>'
                    .delete_option().
                  '</tr>
                </thead>
                <tbody>

                  <!-- START - injection test -->
                  '.show_hosts().'
                  <!-- END - injection test -->
               
                </tbody>
              </table>
';

/*
                <form action="book_address.php" method="POST">

                  <!-- START - injection test -->
                  '.show_hosts().'
                  <!-- END - injection test -->

                  <input name="continue_reservation" value="SUBMIT" type="submit" id="submit-form" class="hidden" />

                </form>                  

*/

// Host row (layout-element) end snippet
echo '
            </div>
          </div>
';

// Fixed right panel
echo '
        </div>

    <!-- FIXED RIGHT PANEL AND CHECKBOX FORM-BUTTON - START -->
        <div class="col-lg-3">
          '.basket().'
    <!-- FIXED RIGHT PANEL AND FORM-BUTTON - END -->

      </div>
    </div>
';


/*            <div class="bookAddrBtn">
                <label for="submit-form" class="btn btn-primary">Book address(es)</label>
              </div>
*/
$frame->doc_end();
?>
