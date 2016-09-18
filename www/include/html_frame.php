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

/*
* class intended for scaffolding the sites main theme
*/

class HTMLframe {
  private $user_nav;
  private $admin_nav;

  public function __construct() {
    $this->admin_nav = [ ["manage_networks.php","glyphicon-signal","Networks"], ["manage_users.php","glyphicon-user","Users"], ["manage_settings.php","glyphicon-cog","Settings"] ];
    $this->user_nav = [ ["logout.php","glyphicon-log-out","Log out"],["userIPs.php","glyphicon-th-list","View your addresses"] ];
  }

	public function doc_start( $page_title ) {
		echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Odin - '.$page_title.'</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">    
    <link href="css/odin.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>';
	}

  /* Helper to doc nav */
  private function adminNav($active) {

    // Return empty string if user isn't admin
    if ($_SESSION[ 'user_data' ][ 'usr_privileges' ] < 1) return '';

    $admin_html = '';
    $is_active = '';

    // Quickfix - links for network and settings disabled for lvl1 admin.
    $disabled_links = array(' class="disabled"','',' class="disabled');
    if ($_SESSION['user_data']['usr_privileges'] > 1) $disabled_links = array('','','');

    //Check if menu-group is active
    foreach ($this->admin_nav as $li_row) {
      if (in_array($active, $li_row)) $is_active = ' active';
    }
    $admin_html .= '<li class="dropdown'.$is_active.'">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>Manage</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
            ';

    // Generating admin-links
    $i = 0;
    foreach ($this->admin_nav as $item) {
      $admin_html .= '<li';
      if ($active === $item[2]) {
        $admin_html .= ' class="active"';
      }
      $admin_html .= '><a href="'.$item[0].'"'.$disabled_links[$i].'><span class="glyphicon '.$item[1].'"></span>'.$item[2].'</a></li>
      ';
      $i++;
    }
    $admin_html .= '
            </ul>
          </li>';
    return $admin_html;
  }

  private function userNav($active, $username) {
    $user_html = '';
    $is_active = '';
    foreach ($this->user_nav as $li_row) {
      if (in_array($active, $li_row)) $is_active = ' active';
    }
    $user_html .= '<li class="dropdown'.$is_active.'">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>'.$username.'</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
            ';
    foreach ($this->user_nav as $item) {
      $user_html .= '<li';
      if ($active === $item[2]) {
        $user_html .= ' class="active"';
      }
      $user_html .= '><a href="'.$item[0].'"><i class="glyphicon '.$item[1].'"></i>'.$item[2].'</a></li>
      ';
    }
    $user_html .= '</ul>
          </li>';
    return $user_html;
  }

  /* TODO: restrict admin functionality, load company logo */
  public function doc_nav( $active, $username ) {
    echo '
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>  
        </div>
        <!-- a class="navbar-brand" href="overview.php"><img src="logo.php?small" alt="Odin - Logo"></a -->
        <a class="navbar-brand" href="overview.php"><img src="images/logo/ODIN.png" alt="Odin - Logo" class="logo-img"></a>
        <ul class="nav navbar-nav">
          <li';
    if ($active === 'Overview') echo ' class="active"'; 
    echo '>
            <a href="overview.php">HOSTS<span class="glyphicon glyphicon-th"></span></a>  
          </li>
          '.$this->adminNav($active).'
          '.$this->userNav($active, $username).'
        </ul>

        <!--
        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <form class="navbar-form" role="search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search" name="srch-term" id="srch-term">
              <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
              </div>
            </div>
          </form>
        </div> -->
      </div>
    </nav>
    ';
  }

	public function doc_end() {
		echo '    
    <!-- jQuery (necessary for Bootstraps JavaScript plugins) -->
    <script src="js/jquery-1.12.4.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/inputValidator.js"></script>
  </body>
</html>';
	}
}

?>
