<?php
define( '_JEXEC', 1 );

include_once ( '../libraries/import.php' );

jimport( 'joomla.application.cli' );
jimport( 'joomla.mail.helper' ); 
jimport( 'joomla.utilities.utility' );

class Mail extends JCli 
{

	public function execute ()
	{

		$from    = $this->create( 'from', 'From Email Address:' );		
		$sender  = $this->create( 'sender', 'Sender Name:' );
		$to      = $this->create( 'to', 'To Email Address:' );
		$subject = $this->create( 'subject', 'Subject:' );
		$body    = $this->create( 'message', 'Message:' );	
		
		//Lets do some house cleaning
		$from    = JMailHelper::cleanAddress( $from );
		$sender  = JMailHelper::cleanText( $sender );
		$to      = JMailHelper::cleanAddress( $to );		
		$subject = JMailHelper::cleanSubject( $subject );
		$body    = JMailHelper::cleanBody( $body );

		// Send the email
		if ( JUtility::sendMail( $from, $sender, $to, $subject, $body ) !== true )
		{
		 	$this->out( 'Email Not sent' );
			return $this->mailto();
		}
	}

	private function create ( $type, $message )
	{

		$this->out( $message );
		$return = $this->in( );
		
		return $return;
	}
}
JCli::getInstance( 'Mail' )->execute();