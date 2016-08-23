<?php

session_start();

include_once('include/html_frame.php');
include_once('include/nwmanagement.php');


$nw_manager = new NetworkManagement();

if (!empty($_POST[ 'edit_host' ])) {
  $nw_manager->updateHost( $_POST[ 'userHostIp2' ], 
                          $_SESSION[ 'user_data' ][ 'usr_id' ], 
                          $_POST[ 'userHostName' ], 
                          $_POST[ 'userHostDescription' ]);
}

if (!empty( $_POST[ 'mod_leases' ] )) {
  if ( $_POST[ 'lease_option' ] === 'extend' ) {
    foreach ($_POST[ 'ip_list' ] as $k => $v) {
      $nw_manager->extendLease($v, $_SESSION[ 'user_data' ][ 'usr_id' ]);
    }
  } else {
    foreach ($_POST[ 'ip_list' ] as $k => $v) {
      $nw_manager->terminateLease($v, $_SESSION[ 'user_data' ][ 'usr_id' ]);
    }
  }
}


$user_hosts = $nw_manager->getUserHosts( $_SESSION[ 'user_data' ][ 'usr_id' ] );

function gen_host_table($user_hosts) {
  $table = '';
  foreach ($user_hosts as $host) {
    $table .= '<tr>
                    <td>'.$host[ 'host_ip' ].'</td>
                    <td>'.$host[ 'host_name' ].'</td>
                    <td>'.substr($host[ 'host_description' ], 0, 30).' ...</td>
                    <td>'.substr($host[ 'host_lease_expiry' ], 0, 10).'</td>
                    <td>'.substr($host[ 'host_last_seen' ], 0, 10).'</td>
                    <td>
                      <a class="open-EditHostDialog" 
                          data-hostip="'.$host[ 'host_ip' ].'" 
                          data-hostname="'.$host[ 'host_name' ].'" 
                          data-hostdescription="'.$host[ 'host_description' ].'" 
                          href="#editHostDialog" 
                          data-toggle="modal" 
                          data-backdrop="static">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                    </td>
                    <td class="check-lease-opt"><input type="checkbox" 
                                                      id="userhost'.$host[ 'host_ip' ].'" 
                                                      name="ip_list[]"
                                                      value="'.$host[ 'host_ip' ].'"></td>
                  </tr>
                  ';
  }
  return $table;
}

$frame = new HTMLframe();
$frame->doc_start("My addresses");

$frame->doc_nav('View your addresses', $_SESSION[ 'user_data' ][ 'usr_usern' ] );


echo '
    <form method="POST" name="host_leases" action="userIPs.php">
    <div class="container">
      <div class="row">
        <div class="col-lg-9">
          
          <div class="row">
            <div class="col-lg-12">
              <h3>My reservations <i class="glyphicon glyphicon-th-list"></i></h3>
              <p>Here you can view the status for your reserved addresses, and extend lapsing reservations or terminate your lease.</p>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Host IP</th>
                    <th>Host name</th>
                    <th>Host description</th>
                    <th>Lease expiry</th>
                    <th>Last seen</th>
                    <th>Edit</th>
                    <th>Choose</th>
                  </tr>
                </thead>
                <tbody>
                  '.gen_host_table($user_hosts).'
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
                <h4>Choosen addresses</h4>
              </div>
              <div class="panel-body" id="leaseBasket">
              </div>
              <div class="panel-footer">
                <div class="form-group">
                  <label class="control-label" for="action">Choose action</label>
                  <div>
                    <select id="action" class="form-control" name="lease_option">
                      <option value="extend">Extend leases</option>
                      <option value="terminate">Terminate leases</option>
                    </select> 
                  </div>
                </div>
                <div class="form-group">
                  <input role="button" type="submit" class="btn btn-info" name="mod_leases" value="Execute">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FIXED RIGHT PANEL - END -->

      </div>  
    </div>
    </form>

    <!-- Modal EDIT HOST code start -->
    <div class="modal fade" id="editHostDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Edit Host</h4>
          </div>
          <form class="form" method="POST" action="userIPs.php">
            <div class="modal-body">
              <div class="form-group">
                <label for="userHostIp">Host IP</label>
                <input type="text" class="form-control" id="userHostIp" name="userHostIp" value="" disabled/>
                <input type="hidden" id="userHostIp2" name="userHostIp2" value=""/>
              </div>
              <div class="form-group">
                <label for="userHostName">Host name</label>
                <input type="text" class="form-control" id="userHostName" name="userHostName" value=""/>
              </div>
              <div class="form-group">
                <label for="userHostDescription">Host description</label>
                <textarea class="form-control" rows="3" id="userHostDescription" name="userHostDescription" value="" placeholder="Description of host"></textarea>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" name="edit_host" value="Save changes"/>
            </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal EDIT HOST code end --> 
';

$frame->doc_end();

?>
