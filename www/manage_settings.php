<?php

session_start();

include_once('include/html_frame.php');
include_once('include/settings.php');

$settings = new Settings();
$cur_settings = $settings->getSettings();
$available_groups = array();

print_r($cur_settings);

foreach ($cur_settings as $setting) {
  if (!in_array( $setting[ 's_group_name' ] , $available_groups )) {
    $available_groups[] = $setting;
  }
}

// TODO: set from GET-req 
$cur_s_group = $available_groups[0]['s_group_name'];

if ( isset( $_POST[ 'dsSubmit' ] ) ) {
  if ( $_POST[ 'dsSubmit' ] === 'Save' ) {
    $settings = new Settings();
    $settingsmax = $_POST[ 'dsSettingsIdMax' ];
    for ( $i = 0; $i < $settingsmax; $i++ ) {
      $updateName = $_POST[ 'dsName'.$i ];
      $updateValue = $_POST[ 'dsValue'.$i ];
      $updateType = $_POST[ 'dsType'.$i ];
      if ( $updateType === 'bool' ) {
        if ( $updateValue === 'on' ) {
          $updateValue = 1;
        } else {
          $updateValue = 0;
        }
      }      
      $settings->changeSetting( $updateName, $updateValue );
    }
  }
}

function genNavTabs($s_group,$all_groups) {
  $nav_html = '<ul class="nav nav-tabs">
                ';
  foreach ($all_groups as $group) {
    $nav_html .= '<li role="presentation"';
    if ($s_group_name == $group['s_group_name']) {
      $nav_html .= ' class="active"';
    }
    $nav_html .= '><a href="manage_settings?'.$group[ 's_group_name' ].'">'.$group[ 's_group_value' ].'</a></li>
    ';
  }
  $nav_html .= '</ul>';
  return $nav_html;
}

function displaySettings($s_group) {
  $settings = new Settings();
  $allsettings = $settings->getSettings();

  echo '<table><FORM method="post" action="admin.php?settings">';
  $settingid = 0;
  foreach ( $allsettings as $name => $settingsarray ) {
    echo '<tr><td colspan="2" align="center">--- '.$name.' ---</td></tr>';
    foreach ( $settingsarray as $cursetting ) {
      echo '<tr data-toggle="tooltip" title="'.$cursetting[ 's_description' ].'">
        <td>'.$cursetting[ 's_fullname' ].'</td>';
      if ( $cursetting[ 's_type' ] === 'text' ) {
        echo '<td><INPUT type="text" name="dsValue'.$settingid.'" value="'.$cursetting[ 's_value' ].'">';
      } elseif( $cursetting[ 's_type' ] === 'bool' ) {
        echo '<td><INPUT type="checkbox" name="dsValue'.$settingid.'"';
        if ( $cursetting[ 's_value' ] === '1' ) {
          echo ' checked';
        }
        echo '>';
      }
      echo '<INPUT type="hidden" name="dsName'.$settingid.'" value="'.$cursetting[ 's_name' ].'">';
      echo '<INPUT type="hidden" name="dsType'.$settingid.'" value="'.$cursetting[ 's_type' ].'"></td></tr>';
      $settingid++;
    }
  }
  echo '<tr><td align="right" colspan=2>
      <BUTTON type="submit" name="dsSubmit" value="Save">Save</BUTTON>
      <BUTTON type="submit" name="dsSubmit" value="Cancel">Cancel</BUTTON>
      <INPUT type="hidden" name="dsSettingsIdMax" value="'.$settingid.'">
      </table></FORM>';
}

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

function checked_box($cb_name) { 
  if($cur_settings[$cb_name] !== 0) {
    return 'checked';
  }
  return '';
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

          <div class="row">
            <div class="col-lg-12">'
              .genNavTabs($cur_s_group,$available_groups).'
            </div>
          </div>
          

          <form class="form-horizontal">
            <div class="form-group">
              <label for="enableEmailNotifications" class="col-lg-6 control-label">Enable email notifications</label>
              <div class="col-lg-6">
                <input type="checkbox" class="form-control" id="enableEmailNotifications" '.checked_box($cur_settings[ 'email_notification' ]).'>
              </div>
            </div>
            <div class="form-group">
              <label for="mailServerType" class="col-lg-6 control-label">Mail server type</label>
              <div class="col-lg-6">
                <input type="text" class="form-control" id="mailServerType" placeholder="Mail server type" name="mailServerType" value="'.$cur_settings['email_notification_type'].'">
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