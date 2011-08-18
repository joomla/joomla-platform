<?php
define( '_JEXEC', 1 );

require_once ( '../libraries/import.php' );
jimport( 'joomla.application.cli' );

class TwitterFeed extends JCli
{

	function latest_tweet( $username, $count = 5 )
    {
		$url = "http://twitter.com/statuses/user_timeline/$username.xml?count=$count";
		$xml = simplexml_load_file( $url ) or die( "Please check the feed" );
      
		$text = '';
		foreach( $xml->status as $status )
		{
			$text .= $status->text . '

';
		}
		
		return $text;
	}

	public function execute()
	{

		$this->out( 'What is your twitter handle?' );
		$username = $this->in( );

		$this->out( 'How many tweets to view?' );
		$count = $this->in( );

		$tweet = $this->latest_tweet( $username, $count );
		$this->out( $tweet );
	}
}

JCli::getInstance( 'TwitterFeed' )->execute();