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

  public function __construct($bind_address = 0, $bind_port = 0)
  {
		// Adapter base path, class prefix
		parent::__construct($bind_address, $bind_port);

		// Bind the socket
    if (!socket_bind($this->socket, $bind_address, $bind_port)) {
      throw new Exception("Could not bind socket to [$this->bind_address - $this->bind_port]: ".socket_strerror(socket_last_error($this->socket)));
    }

    if (!@socket_getsockname($this->socket, $this->local_addr, $this->local_port)) {
      throw new Exception("Could not retrieve local address & port: ".socket_strerror(socket_last_error($this->socket)));
    }
  }
}
