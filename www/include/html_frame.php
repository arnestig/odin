<?php

class HTMLframe {
	public function doc_start( $page_title ) {
		echo '
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Odin - '.$page_title.'</title>

    <!-- Bootstrap -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/kapsch.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>';
	}

  public function doc_nav( $active ) {
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
        <a class="navbar-brand" href="admin_networks.html"><img src="images/Kapsch-logo.png" alt="Kapsch - Logo"></a>
          
        <ul class="nav navbar-nav">
          <li class="active">
            <a href="admin_networks.html"><i class="glyphicon glyphicon-eye-open"></i>Overview</a>  
          </li>

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>Manage</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="manage_networks.html"><i class="glyphicon glyphicon-signal"></i>Networks</a></li>
              <li><a href="manage_users.html"><i class="glyphicon glyphicon-user"></i>Users</a></li>
              <li><a href="settings.html"><i class="glyphicon glyphicon-cog"></i>Settings</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>Admin</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="index.html"><i class="glyphicon glyphicon-off"></i>Log out</a></li>
              <li><a href="userIPs.html"><i class="glyphicon glyphicon-th-list"></i>View Your Addresses</a></li>
            </ul>
          </li>
        </ul>

        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <form class="navbar-form" role="search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search" name="srch-term" id="srch-term">
              <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
              </div>
            </div>
          </form>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    ';
  }

	public function doc_end() {
		echo '    
    <!-- jQuery (necessary for Bootstraps JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>';
	}
}

?>