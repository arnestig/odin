<?php

session_start();

include_once('include/html_frame.php');
include_once('include/settings.php');

$settings = new Settings();
$available_groups = $settings->getSettingGroups();
$cur_s_group = $available_groups[0];
print_r($cur_s_group);
$cur_settings = $settings->getSettings($cur_s_group['sg_name']);


// TODO: set from GET-req 


if ( isset( $_POST[ 'submit' ] ) ) {
  $settingsmax = $_POST[ 'settingsIdMax' ];
  $settingsmin = $_POST[ 'settingsIdMin' ];
  for ( $i = $settingsmin; $i <= $settingsmax; $i++ ) {
    $updateName = $_POST[ 'name'.$i ];
    $updateValue = $_POST[ 'value'.$i ];
    $updateType = $_POST[ 'type'.$i ];
    if ( $updateType === 'checkbox' ) {
      if ( $updateValue === 'on' ) {
        $updateValue = 1;
      } else {
        $updateValue = 0;
      }
    }      
    $settings->changeSetting( $updateName, $updateValue );
  }
}

function genNavTabs($s_group,$all_groups) {
  $nav_html = '<ul class="nav nav-tabs">
                ';
  foreach ($all_groups as $group) {
    $nav_html .= '<li role="presentation"';
    if ($s_group[ 'sg_name' ] === $group[ 'sg_name' ]) {
      $nav_html .= ' class="active"';
    }
    $nav_html .= '><a href="manage_settings?'.$group[ 'sg_name' ].'">'.$group[ 'sg_value' ].'</a></li>
    ';
  }
  $nav_html .= '</ul>';
  return $nav_html;
}

function displaySettings($s_group) {
  $settings = new Settings();
  $allsettings = $settings->getSettings($s_group);
  $form_html = '<form class="form-horizontal" method="post" action="manage_settings.php">';
  $settingid = array();
  foreach ( $settingsarray as $cursetting ) {
    $form_html .= '<div class="form-group">
                    <label for="'.$cursetting[ 's_name' ].'" class="col-lg-6 control-label">'.$cursetting[ 's_fullname' ].'</label>
                    <div class="col-lg-6">
                      <input type="'.$cursetting[ 's_type' ].'" class="form-control" id="'.$cursetting[ 's_name' ].'" ';
    if ($cursetting[ 's_type' ] === 'checkbox' && $cursetting[ 's_value' ] === '1') {
      $form_html .= 'checked';
    } else {
      $form_html .= 'value="'.$cursetting_value.'"';
    }
    $form_html.= '>
                    </div>
                  </div>';
  }
  $form_html .= '<div class="form_group">
                  <div="col-lg-12">
                    <input type="hidden" name="settingsIdMin" value="">
                    <input type="hidden" name="settingsIdMax" value="">
                  </div>
                </div>
              </form>';
  return $form_html;
}

$frame = new HTMLframe();
$frame->doc_start("Configure Settings");
$frame->doc_nav("Settings", $_SESSION[ 'user_data' ][ 'usr_usern' ] );


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
          
          '.displaySettings($cur_s_group).'

        </div>

      </div>
    </div>
';

$frame->doc_end();

?>