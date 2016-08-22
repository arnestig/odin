<?php

session_start();

include_once('include/html_frame.php');

$frame = new HTMLframe();
$frame->doc_start("Booking completed");
$frame->doc_nav('', $_SESSION[ 'user_data' ][ 'usr_usern' ]);

?>

<div class="container">
	<div class="row">
		<div class="col-lg-offset-2 col-lg-10">
			<h1>Booking completed</h1>
			<h2>Thank you for using ODIN</h2>
			<a href="overview.php" class="btn btn-default" role="button">Back to hosts</a>
		</div>
	</div>
</div>


<?php

$frame->doc_end();

?>









