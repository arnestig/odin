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

$frame = new HTMLframe();
$frame->doc_start("Booking completed");
$frame->doc_nav('', $_SESSION[ 'user_data' ][ 'usr_firstn' ]." ".$_SESSION[ 'user_data' ][ 'usr_lastn'] );

?>

<div class="container">
	<div class="row">
		<div class="col-lg-offset-2 col-lg-10">
			<h1>Booking completed</h1>
			<h2>Thank you for using Odin</h2>
			<a href="overview.php" class="btn btn-default" role="button">Back to hosts</a>
		</div>
	</div>
</div>


<?php

$frame->doc_end();

?>









