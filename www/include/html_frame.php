<?php
/*
* class intended for scaffolding the sites main theme
*/

class HTMLframe {
  private $user_nav;
  private $admin_nav;

  public function __construct() {
    $this->admin_nav = [ ["manage_networks.php","glyphicon-signal","Networks"], ["manage_users.php","glyphicon-user","Users"], ["manage_settings.php","glyphicon-cog","Settings"] ];
    $this->user_nav = [ ["logout.php","glyphicon-off","Log out"],["userIPs.php","glyphicon-th-list","View your addresses"] ];
  }

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
    <link rel="stylesheet" href="css/odin.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>';
	}

  /* TODO: Implement current page highlight, restrict admin functionality, load company logo */
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
        <a class="navbar-brand" href="overview.php"><img src="images/ODIN.png" alt="Odin - Logo"></a>
          
        <ul class="nav navbar-nav">
          <li class="active">
            <a href="overview.php"><i class="glyphicon glyphicon-eye-open"></i>Overview</a>  
          </li>

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>Manage</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
            ';
    foreach ($this->admin_nav as $item) {
      echo '
              <li';
      if ($active === $item[2]) {
        echo ' class="active"';
      }
      echo '><a href="'.$item[0].'"><i class="glyphicon '.$item[1].'"></i>'.$item[2].'</a></li>
      ';
    }
    echo '
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><strong>'.$username.'</strong><span class="caret"></span></a>
            <ul class="dropdown-menu">
            ';
    foreach ($this->user_nav as $item) {
      echo '
              <li';
      if ($active === $item[2]) {
        echo ' class="active"';
      }
      echo '><a href="'.$item[0].'"><i class="glyphicon '.$item[1].'"></i>'.$item[2].'</a></li>
      ';
    }
    echo '
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
    <script src="js/scripts.js"></script>
  </body>
</html>';
	}
}

?>