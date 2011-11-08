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
abstract class JSocketsServer extends JSockets
{
	/**
	 * The parent object
	 * @var Resource
	 */
	public $parent = null;
	/**
	 * Stores a reference to the created socket
	 * @var Resource
	 */
	public $max_clients = 10;
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
	public function listen($host = "localhost", $port = 9999, $message = "") {

	  if($this->link === null) {
	      throw new JException("No socket available, cannot listen");
	  }
	  
	  // Bind to the host/port
	  if(!@socket_bind($this->link, $host, $port)) {
	      throw new JException("Cannot bind to $host:$port. PHP said, " . $this->getLastError($this->link));
	  }
	  // Try to listen
	  if(!@socket_listen($this->link)) {
	      throw new JException("Cannot listen on $host:$port. PHP said, " . $this->getLastError($this->link));
	  }

	  echo "Listening on $host:$port\n";

	  $this->listening = true;

    // create a list of all the clients that will be connected to us..
    // add the listening socket to this list
    $clients = array($this->link);
   
    while (true) {
      // create a copy, so $clients doesn't get modified by socket_select()
      $read = $clients;
     
      // get a list of all the clients that have data to be read from
      // if there are no clients with data, go to next iteration
      if (socket_select($read, $write = NULL, $except = NULL, 0) < 1)
        continue;
     
      // check if there is a client trying to connect
      if (in_array($this->link, $read)) {
				
        // accept the client, and add him to the $clients array
				$clients[] = $newsock = socket_accept($this->link);
				// Creating child object
        $child = new JSocketsChild($newsock);
        array_push($this->threads, $child);

        // send the client a welcome message
				if ($message != "") {
					$child->write($message);
				}

        socket_getpeername($newsock, $ip);
        echo "New client connected: {$ip}\n";
       
        // remove the listening socket from the clients-with-data array
        $key = array_search($this->link, $read);
        unset($read[$key]);
      }
     
      // loop through all the clients that have data to read from
      foreach ($read as $read_sock) {
        // read until newline or 1024 bytes
        // socket_read while show errors when the client is disconnected, so silence the error messages
        $data = @socket_read($read_sock, 1024, PHP_NORMAL_READ);
       
        // check if the client is disconnected
        if ($data === false) {
          // remove client for $clients array
          $key = array_search($read_sock, $clients);
          unset($clients[$key]);
          echo "client disconnected.\n";
          // continue to the next client to read from, if any
          continue;
        }
       
        // trim off the trailing/beginning white spaces
        $data = trim($data);
       
        // check if there is any data after trimming off the spaces
        if (!empty($data)) {

          $command = strtolower($data);

					// Execute __processCommand method from parent
					if (method_exists($this, '__processCommand')) {
						$this->__processCommand($child, $command);
					} else {
						throw new JException("__processCommand() method not found");
					}        
        } // end if
      } // end of reading foreach
    } // end while
	} // end method

	/**
	 * Process command sended by client, calling an abstract method
   * into descendent class
   * 
	 * @param string $command
	 */
	abstract protected function __processCommand($command = false);


} // end class
