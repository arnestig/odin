<?php

session_start();

include_once('include/html_frame.php');
include_once('include/settings.php');

$settings = new Settings();
$cur_settings = $settings->getSettings();

$frame = new HTMLframe();
$frame->doc_start("Configure Settings");
$frame->doc_nav("Settings", $_SESSION[ 'user_data' ][ 'usr_usern' ] );

//TODO
function genSettings() {
  foreach ($cur_settings as $setting_row) {
    // Ta ut rubriker Och lägg i eǵen array
  }
  
  foreach ($variable as $key => $value) {
    # code...
  }
}

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-1 col-lg-5">

          <div class="row">
            <div class="col-lg-12">
              <h3>Settings <i class="glyphicon glyphicon-cog"></i></h3>
            </div>
          </div>
          

          <form class="form-horizontal">
            <div class="form-group">
              <label for="enableEmailNotifications" class="col-lg-6 control-label">Enable email notifications</label>
              <div class="col-lg-6">
                <input type="checkbox" class="form-control" id="enableEmailNotifications" '.function(){ if($cur_settings['email_notification'] !== 0) return 'checked'; }.'>
              </div>
            </div>
            <div class="form-group">
              <label for="mailServerType" class="col-lg-6 control-label">Mail server type</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="mailServerType" placeholder="Mail server type" value="'.$cur_settings[].'smtp">
              </div>
            </div>
            <div class="form-group">
              <label for="mailServerHostname" class="col-lg-6 control-label">Mail server hostname</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="mailServerHostname" placeholder="Mail server hostname" value="">
              </div>
            </div>
            <div class="form-group">
              <label for="mailServerPort" class="col-lg-6 control-label">Mail server port</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="mailServerPort" placeholder="Mail server port" value="25">
              </div>
            </div>
            <div class="form-group">
              <label for="senderEmailAddress" class="col-lg-6 control-label">Sender email address</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="senderEmailAddress" placeholder="Sender email address" value="no-reply@odin.valhalla">
              </div>
            </div>

            <div>
              <hr>
            </div>

            <div class="form-group">
              <label for="scanHostsInterval" class="col-lg-6 control-label">Scan hosts interval</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="scanHostsInterval" placeholder="Enter hours" value="">
              </div>
            </div>
            <div class="form-group">
              <label for="leaseTime" class="col-lg-6 control-label">Lease time</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="leaseTime" placeholder="Enter days" value="365">
              </div>
            </div>

            <div>
              <hr>
            </div>
            <div class="form-group">
              <label for="orgName" class="col-lg-6 control-label">Organization name</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="orgName" placeholder="Enter your organizations name" value="Odin">
              </div>
            </div>
            <div class="form-group">
              <label for="logoUploadFile" class="col-lg-6 control-label">Upload your logo</label>
              <div class="col-lg-6">
                <input type="file" id="logoUploadFile">
                <p class="help-block">Make the size 30px in height and 60 in width. Format should be .png.</p>
              </div>
            </div>

            <div>
              <hr>
            </div>
            
            <div class="form-group">
              <div class="col-lg-offset-8 col-lg-2">
                <button type="submit" class="btn btn-default">Cancel</button>
              </div>
              <div class="col-lg-2">
                <button type="submit" class="btn btn-default">Save</button>
              </div>
            </div>
          </form>

        </div>

      </div>
    </div>
';

$frame->doc_end();

?>