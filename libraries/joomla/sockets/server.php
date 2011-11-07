<?php
/**
 * @version		    $Id: 
 * @package		    Joomla.Platform
 * @subpackage	  JSockets
 * @copyright			Copyright (C) 1996 - 2011 Matware - All rights reserved.
 * @author				Matias Aguirre
 * @email   			maguirre@matware.com.ar
 * @link					http://www.matware.com.ar/
 * @license				GNU/GPL http://www.gnu.org/licenses/gpl-2.0-standalone.html
 */
defined('JPATH_PLATFORM') or die;

jimport('joomla.sockets.sockets');

/**
 * JSocketsServer Class
 *
 * @package     Joomla.Platform
 * @subpackage  JSockets
 * @since       11.1
 */
class JSocketsServer extends JSockets
{
	/**
	 * Creates a new Socket.
	 *
	 * @param array $args
	 * @param int $args[domain] AF_INET|AF_INET6|AF_UNIX
	 * @param int $args[type] SOCK_STREAM|SOCK_DGRAM|SOCK_SEQPACKET|SOCK_RAW|SOCK_UDM
	 * @param int $args[protocol] SOL_TCP|SOL_UDP
	 * @return Socket
	 */
	public function __construct(array $args = null) {
		// Parent construct
		parent::__construct();

		// Include Child
		include_once ( JPATH_PLATFORM . '/joomla/sockets/child.php' );
	}
	/**
	 * After calling this method, the Socket will start to listen on the port
	 * specified or the default port. 
	 * 
	 * @see Socket::$port
	 * @param string $host
	 * @param int $port 
	 */
	public function listen($host = "localhost", $port = 9999) {

	  if($this->link === null) {
	      throw new JException("No socket available, cannot listen");
	  }
	  
	  // Set a valid port to listen on
	  //if($port <= 1024) {
	  //    $port = 9999;
	  //}

	  socket_set_nonblock($this->link);

	  // Bind to the host/port
	  if(!socket_bind($this->link, $host, $port)) {
	      throw new JException("Cannot bind to $host:$port. PHP said, " . $this->getLastError($this->link));
	  }
	  // Try to listen
	  if(!socket_listen($this->link)) {
	      throw new JException("Cannot listen on $host:$port. PHP said, " . $this->getLastError($this->link));
	  }

	  echo "Listening on $host:$port\n";

	  $this->listening = true;

	  // Start main loop
	  while($this->listening) {
      // Accept new connections
      if(($thread = @socket_accept($this->link)) !== false) {
        $child = new JSocketsChild($thread);
        array_push($this->threads, $child);

        echo "Accepted child, " . $child->getInfo() . "\n";
      }

      // Loop through children, listen for read
      foreach($this->threads as $index => $child) {
        try {
          $msg = $child->read();
        } catch (JException $e) {
          // Child socket closed unexpectedly, remove from active
          // threads

          echo "Terminating child at $index\n";
          unset($this->threads[$index]);
          continue;
        }
        $msg = trim($msg);
        
        if($msg !== false && !empty($msg)) {
          $command = strtolower($msg);
          switch($command) {
              case "end":
                  $this->killAll();
                  die("Kill message received\n");
              break;
          }
          echo "Received message: $msg\n";

          $this->send($child, "You said: $msg\n");
        }
      }
	  }
	}
}
