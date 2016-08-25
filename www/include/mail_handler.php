<?php
include_once('settings.php');
include_once('nwmanagement.php');

class MailHandler {
	private $dbcon;
    
    public function __construct() 
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
        $this->dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Default return true. Add error handling to do other things
    private function sendMail( $user_id, $subject, $message, $sender ) 
    {
        $sth = $this->dbcon->prepare( "SELECT notify_user( ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $user_id, $subject, $message, $sender ) );
        return true;
    }

    // Notify all users of network for various reasons
	public function notifyNetworkUsers( $nw_id, $network, $message, $sender ) 
	{
		if ( empty($message) ) return false;
		$subject = 'Info about '.$network;
		$nw_management = new NetworkManagement();
		$nw_users = $nw_management->getNetworkUsers( $nw_id );

		foreach ($nw_users as $user) {
			$this->sendMail( $user, $subject, $message, $sender );
		}
		return true;
	}

	// Notify all users of deletion of network
	public function notifyNetworkUsersDelete( $nw_id, $network, $message, $sender ) 
	{
		if ( empty($message) ) {
			$message = $network.' has been deleted. Your devices should be moved to another network.';
		}
		$nw_management = new NetworkManagement();
		$nw_users = $nw_management->getNetworkUsers( $nw_id );

		foreach ($nw_users as $user) {
			$this->sendMail( $user, $network.' has been deleted.', $message, $sender );
		}
		return true;	
	}

	public function userMadeAdmin( $user_id, $sender_id ) 
	{
		$this->sendMail( $user_id, 'Admin in ODIN', 'You are now the baws of ODIN. With great power blabla.', $sender_id );
	}

	public function userEdited( $user_id, $message, $sender_id ) 
	{
		$this->sendMail( $user_id, 'Changed user details ODIN', $message, $sender_id );
	}

	public function userPasswordChanged( $user_id, $password, $sender_id ) 
	{
		$message = 'Here is your new password which needs to be changed: ';
		$message .= $password;
		$this->sendMail( $user_id, 'Changed password ODIN', $message, $sender_id );
	}	

	public function addUser( $user_id, $message, $sender_id ) 
	{
		$this->sendMail( $user_id, 'Welcome to ODIN', $message, $sender_id );
	}

	// When user gets deleted. If message is left blank, default message is sent
	public function deleteUser( $user_id, $subject, $message, $sender ) 
	{
		if (empty($message)) {
			$message = 'Your profile and host-reservations has now been deleted from ODIN.';
		}
		$this->sendMail( $user_id, 'Goodbye from ODIN', $message, $sender_id );
	}
}

?>