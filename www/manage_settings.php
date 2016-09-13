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
include_once('include/html_frame.php');
include_once('include/settings.php');

if ($_SESSION['user_data']['usr_privileges'] < 2) {
  header('Location: overview.php');
  exit;
}

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
  $settingsmax = $_POST[ 'settingsIdMax' ];
  $settingsmin = $_POST[ 'settingsIdMin' ];
  for ( $i = $settingsmin; $i <= $settingsmax; $i++ ) {
    $updateValue = $_POST[$i];
    $updateName = '';
    $updateType = '';
    $okayToUpdate = true;

    // TODO: getSetting by id instead
    foreach ($cur_settings as $row) {
      if ( $row[ 's_id' ] == $i ) {
          // special handling for logo uploads
          if ( $row[ 's_name' ] === 'logo' ) {
              if ( !empty( $_FILES[ $i ][ 'tmp_name' ] ) ) {
                  // Read in a binary file
                  $data = file_get_contents( $_FILES[ $i ][ 'tmp_name' ] );

                  // Escape the binary data
                  $updateValue = base64_encode( $data );
              } else {
                  $okayToUpdate = false;
              }
          }
          $updateName = $row[ 's_name' ];
          $updateType = $row[ 's_type' ];
      }
    }
    if ( $updateType === 'checkbox' ) {
      if ( $updateValue === 'on' ) {
        $updateValue = 'checked';
      } else {
        $updateValue = '';
      }
    }      
    if ( $okayToUpdate === true ) {
        $settings->changeSetting( $updateName, $updateValue );
    }
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
  $form_html = '<form enctype="multipart/form-data" class="form-horizontal" method="post" action="manage_settings.php?group='.$s_group.'">';
  $settingid = array();

  $minId = $allsettings[0][ 's_id' ];
  $maxId = $minId + sizeof($allsettings) - 1;

  foreach ( $allsettings as $cursetting ) {
    $form_html .= '<div class="form-group">
                    <label for="'.$cursetting[ 's_name' ].'" class="col-lg-6 control-label">'.$cursetting[ 's_fullname' ].'</label>
                    <div class="col-lg-6">';

    if ( $cursetting[ 's_type' ] === 'file' ) {
        $form_html .= '<img src="logo.php" alt="Odin logo">';
    }

    $form_html .= '<input type="'.$cursetting[ 's_type' ].'" class="form-control" id="'.$cursetting[ 's_name' ].'" name="'.$cursetting[ 's_id' ].'" ';
    if ($cursetting[ 's_type' ] === 'checkbox') {
      $form_html .= $cursetting[ 's_value' ];
    } else if ( $cursetting[ 's_type' ] === 'file' ) {
      $form_html .= 'value=""';
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
                    <input type="submit" class="btn btn-info pull-right" name="submit" value="Save changes"> 
                  </div>
                </div>
              </form>';
  return $form_html;
}

$frame = new HTMLframe();
$frame->doc_start("Configure Settings");
$frame->doc_nav('Settings', $_SESSION[ 'user_data' ][ 'usr_firstn' ]." ".$_SESSION[ 'user_data' ][ 'usr_lastn'] );


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
