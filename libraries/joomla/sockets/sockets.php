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

/**
 * Sockets class.
 *
 * This class allows for simple but smart objects with get and set methods
 * and an internal error handler.
 *
 * Based on phpSocketDaemon 1.0
 * Copyright (C) 2006 Chris Chabot <chabotc@xs4all.nl>
 * See http://www.chabotc.nl/ for more information
 *
 * @package     Joomla.Platform
 * @subpackage  Base
 * @since       11.122
 */
class JSockets
{
  public $socket;
  public $bind_address;
  public $bind_port;
  public $domain;
  public $type;
  public $protocol;
	public $client;
  public $local_addr;
  public $local_port;
  public $read_buffer    = '';
  public $write_buffer   = '';

  public function __construct($bind_address = 0, $bind_port = 0, $domain = AF_INET, $type = SOCK_STREAM, $protocol = SOL_TCP)
  {
    $this->bind_address = $bind_address;
    $this->bind_port    = $bind_port;
    $this->domain       = $domain;
    $this->type         = $type;
    $this->protocol     = $protocol;

    if (($this->socket = @socket_create($domain, $type, $protocol)) === false) {
      throw new Exception("Could not create socket: ".socket_strerror(socket_last_error($this->socket)));
    }
  }

  public function __destruct()
  {
    if (is_resource($this->socket)) {
      $this->close();
    }
  }

  public function get_error()
  {
    $error = socket_strerror(socket_last_error($this->socket));
    socket_clear_error($this->socket);
    return $error;
  }

  public function close()
  {
    if (is_resource($this->socket)) {
      @socket_shutdown($this->socket, 2);
      @socket_close($this->socket);
    }
    $this->socket = (int)$this->socket;
  }

  public function write($socket, $buffer, $length = 4096)
  {
    if (!is_resource($socket)) {
		  if (!is_resource($this->socket)) {
		    throw new Exception("Invalid socket or resource");
				return false;
		  }else{
				$socket = $this->socket;
			}
    }

		if (($ret = socket_write($socket, $buffer, $length)) === false) {
      throw new Exception("Could not write to socket: ".$this->get_error()."\n");
    }
    return $ret;
  }

  public function read($socket = null, $length = 4096)
  {
    if (!is_resource($socket)) {
		  if (!is_resource($this->socket)) {
		    throw new Exception("Invalid socket or resource");
				return false;
		  }else{
				$socket = $this->socket;
			}
    }

		if (($ret = socket_read($socket, $length, PHP_NORMAL_READ)) == false) {
      throw new Exception("Could not read from socket: ".$this->get_error()."\n");
    }
    return $ret;
  }

  public function connect($remote_address, $remote_port)
  {
    $this->remote_address = $remote_address;
    $this->remote_port    = $remote_port;
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!socket_connect($this->socket, $remote_address, $remote_port)) {
      throw new Exception("Could not connect to {$remote_address} - {$remote_port}: ".$this->get_error()."\n");
    }
  }

  public function listen($backlog = 128)
  {
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!@socket_listen($this->socket, $backlog)) {
      throw new Exception("Could not listen to {$this->bind_address} - {$this->bind_port}: ".$this->get_error()."\n");
    }
  }

  public function accept()
  {
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (($client = socket_accept($this->socket)) === false) {
      throw new Exception("Could not accept connection to {$this->bind_address} - {$this->bind_port}: ".$this->get_error()."\n");
    }
    return $client;
  }

  public function set_non_block()
  {
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!@socket_set_nonblock($this->socket)) {
      throw new Exception("Could not set socket non_block: ".$this->get_error()."\n");
    }
  }

  public function set_block()
  {
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!@socket_set_block($this->socket)) {
      throw new Exception("Could not set socket non_block: ".$this->get_error()."\n");
    }
  }

  public function set_recieve_timeout($sec, $usec)
  {
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!@socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $sec, "usec" => $usec))) {
      throw new Exception("Could not set socket recieve timeout: ".$this->get_error()."\n");
    }
  }

  public function set_reuse_address($reuse = true)
  {
    $reuse = $reuse ? 1 : 0;
    if (!is_resource($this->socket)) {
      throw new Exception("Invalid socket or resource");
    } elseif (!@socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, $reuse)) {
      throw new Exception("Could not set SO_REUSEADDR to '$reuse': ".$this->get_error()."\n");
    }
  }
}
