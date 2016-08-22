<?php

session_start();

include_once('include/html_frame.php');

$frame = new HTMLframe();
$frame->doc_start("My addresses");

$frame->doc_nav('View your addresses', $_SESSION[ 'user_data' ][ 'usr_usern' ] );


echo '
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
                <label for="hostName">Host name</label>
                <input type="text" class="form-control" id="hostName" name="hostName" value="" disabled/>
                <input type="hidden" id="networkBase2" name="networkBase2" value=""/>
                <input type="hidden" id="networkId" name="networkId" value=""/>
              </div>
              <div class="form-group">
                <label for="networkCidr">CIDR</label>
                <input type="text" class="form-control" id="networkCidr" name="networkCidr" value="" disabled/>
                <input type="hidden" id="networkCidr2" name="networkCidr2" value=""/>
              </div>
              <div class="form-group">
                <label for="networkDescription">Network description</label>
                <textarea class="form-control" rows="3" id="networkDescription" name="networkDescription" value="" placeholder="Description of network"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" name="edit_description" value="Save changes"/>
            </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal EDIT HOST code end --> 

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
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Host IP</th>
                    <th>Host name</th>
                    <th>Data description</th>
                    <th>Lease expiry</th>
                    <th>Edit</th>
                    <th>Choose</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>192.168.5.150</td>
                    <td>LaserVD7</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td>
                      <a class="open-EditHostDialog" 
                          data-hostname="'.$row[ 'nw_id' ].'" 
                          data-networkbase="'.$row[ 'nw_base' ].'" 
                          data-networkcidr="'.$row[ 'nw_cidr' ].'" 
                          data-networkdescription="'.$row[ 'nw_description' ].'" 
                          href="#editHostDialog" 
                          data-toggle="modal" 
                          data-backdrop="static">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                    </td>
                    <td><input type="checkbox" class="check-terminate"></td>
                  </tr>
                  <tr>
                    <td>192.168.5.151</td>
                    <td>LaserVD5</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td><span class="glyphicon glyphicon-edit"></span></td>
                    <td><input type="checkbox" class="check-terminate"></td>
                  </tr>
                  <tr>
                    <td>192.168.5.152</td>
                    <td>LaserVD8</td>
                    <td>Placed above test track to ...</td>
                    <td>20160616</td>
                    <td><span class="glyphicon glyphicon-edit"></span></td>
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
                <h4>Choosen addresses</h4>
              </div>
              <div class="panel-body">
                <p>192.168.5.151</p>
                <p>192.168.5.152</p>
              </div>
              <div class="panel-footer">
                <div class="form-group">
                  <label class="control-label" for="action">Choose action</label>
                  <div>
                    <select id="action" class="form-control">
                      <option>Extend leases</option>
                      <option>Terminate leases</option>
                    </select> 
                  </div>
                </div>
                <div class="form-group">
                  <input role="button" class="btn btn-info" name="Execute" value="Execute">
                </div>
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