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

include_once('settings.php');
include_once('nwmanagement.php');
include_once('usermanagement.php');

class MailHandler {
	private $dbcon;
    
    public function __construct() 
    {
        $this->dbcon = new PDO( "pgsql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";user=" . DB_USER . ";password=" . DB_PASSWORD . ";port=" . DB_PORT ) or die ("Could not connect to server\n"); 
        $this->dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Default return true. Add error handling to do other things
    public function sendMailToUser( $user_id, $subject, $message, $sender_id ) 
    {
        $sth = $this->dbcon->prepare( "SELECT notify_user( ?, ?, ?, ?, ? )" );
        $sth->execute( array( '', $user_id, $subject, $message, $sender_id ) );
        return true;
    }

    // Notify all active users in Odin
    public function notifyAllUsers($message, $sender_id) 
    {
    	$user_management = new UserManagement();
    	$all_users = $user_management->getUsers();
    	foreach ($all_users as $user) {
    		$this->sendMailToUser($user['usr_id'], 'About ODIN', $message, $sender_id);
    	}
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
			$this->sendMailToUser( $user, $subject, $message, $sender );
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
			$this->sendMailToUser( $user, $network.' has been deleted.', $message, $sender );
		}
		return true;	
	}

	public function userMadeAdmin( $user_id, $sender_id ) 
	{
		$this->sendMailToUser( $user_id, 'Admin in ODIN', 'You have now been made admin of ODIN. Log in to ODIN and see your new responsibilities under "Manage" in the menu.', $sender_id );
	}

	public function userEdited( $user_id, $message, $sender_id ) 
	{
		$this->sendMailToUser( $user_id, 'Changed user details ODIN', $message, $sender_id );
	}

	public function userPasswordChanged( $user_id, $password, $sender_id ) 
	{
		$message = 'Here is your new password which needs to be changed: ';
		$message .= $password;
		$this->sendMailToUser( $user_id, 'Changed password ODIN', $message, $sender_id );
	}	

	public function addUser( $user_id, $message, $sender_id ) 
	{
		$this->sendMailToUser( $user_id, 'Welcome to ODIN', $message, $sender_id );
	}

	// When user gets deleted. If message is left blank, default message is sent
	public function deleteUser( $user_id, $subject, $message, $sender ) 
	{
		if (empty($message)) {
			$message = 'Your profile and host-reservations has now been deleted from ODIN.';
		}
		$this->sendMailToUser( $user_id, 'Goodbye from ODIN', $message, $sender_id );
	}
}

?>
