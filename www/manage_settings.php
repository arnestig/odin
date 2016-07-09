<?php

session_start();

include_once('include/html_frame.php');
include_once('include/settings.php');

$settings = new Settings();
$available_groups = $settings->getSettingGroups();
$cur_s_group = $available_groups[0];

if ( isset($_GET[ 'group' ]) && !empty($_GET[ 'group' ]) ) {
  foreach ( $available_groups as $group_row ) {
    if ( $group_row[ 'sg_name' ] === $_GET[ 'group' ] ) {
      $cur_s_group = $group_row;
    }
  }
}

$cur_settings = $settings->getSettings($cur_s_group['sg_name']);

if ( isset( $_POST[ 'submit' ] ) && $_POST[ 'submit' ] === 'Save changes' ) {
  print_r($_POST[ 'submit' ]);
  $settingsmax = $_POST[ 'settingsIdMax' ];
  $settingsmin = $_POST[ 'settingsIdMin' ];
  for ( $i = $settingsmin; $i <= $settingsmax; $i++ ) {
    $updateName = $_POST[ 'name'.$i ];
    $updateValue = $_POST[ 'value'.$i ];
    $updateType = $_POST[ 'type'.$i ];
    if ( $updateType === 'checkbox' ) {
      if ( $updateValue === 'checked' ) {
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
    $nav_html .= '><a href="manage_settings.php?group='.$group[ 'sg_name' ].'">'.$group[ 'sg_value' ].'</a></li>
    ';
  }
  $nav_html .= '</ul>';
  return $nav_html;
}

function displaySettings($s_group) {
  $settings = new Settings();
  $allsettings = $settings->getSettings($s_group);
  $form_html = '<form class="form-horizontal" method="post" action="manage_settings.php?group='.$s_group.'">';
  $settingid = array();

  $minId = $allsettings[0][ 's_id' ];
  $maxId = $minId + sizeof($allsettings) - 1;

  foreach ( $allsettings as $cursetting ) {
    $form_html .= '<div class="form-group">
                    <label for="'.$cursetting[ 's_name' ].'" class="col-lg-6 control-label">'.$cursetting[ 's_fullname' ].'</label>
                    <div class="col-lg-6">
                      <input type="'.$cursetting[ 's_type' ].'" class="form-control" id="'.$cursetting[ 's_name' ].'" name="'.$cursetting[ 's_id' ].'" ';
    if ($cursetting[ 's_type' ] === 'checkbox' && $cursetting[ 's_value' ] === '1') {
      $form_html .= 'checked';
    } else {
      $form_html .= 'value="'.$cursetting[ 's_value' ].'"';
    }
    $form_html.= '>
                    </div>
                  </div>';
  }

  $form_html .= '<div class="form_group">
                  <div class="col-lg-6 col-lg-offset-6">
                    <input type="hidden" name="settingsIdMin" value="'.$minId.'">
                    <input type="hidden" name="settingsIdMax" value="'.$maxId.'">
                    <button class="btn btn-default" href="manage_settings.php?group='.$s_group.'">Discard</button>
                    <input type="submit" class="btn btn-success pull-right" name="submit" value="Save changes"> 
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
          <div class="row spacer-row"></div>
          <div class="row">
            <div class="col-lg-12">
              <h4>Description</h4>
              <p>'.$cur_s_group[ 'sg_description' ].'</p>
            </div>
          </div>
          <div class="row spacer-row"></div>

          '.displaySettings($cur_s_group[ 'sg_name' ]).'

        </div>

      </div>
    </div>
';

$frame->doc_end();

?>