<?php
include_once('settings.php');

//TODO: Most everything

class MailHandler {
	private $from;

	public function __construct() {
		// TODO: change to setting
		$from = '';
	}

	public function sendMail($subject, $message, $to = '') {
		$headers = "From: ".$from."" . "\r\n" ."CC: somebodyelse@example.com";


		mail($to,$subject,$tmessage,$headers);
	}
}

?>