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

class JSocketsChild {
  /**
   * Stores a reference to the created socket
   * @var Resource
   */
  private $link = null;

  /**
   * Connection reset by peer error number
   * @var int
   */
  const PEER_RESET = 104;

  public function __construct($thread = null) {
    if($thread === null || !is_resource($thread)) {
        throw new JException("No socket available, cannot create Child");
    }
    $this->link = $thread;
  }
  /**
   * Sends a message to the socket
   * 
   * @param string $message
   * @return boolean
   */
  public function write($message) {
    if($this->link == null) {
        throw new JException("Socket not connected");
    }
    if(empty($message)) {
        return false;
    }
    $wrote = socket_write($this->link, $message, strlen($message));

    if($wrote === false) {
        throw new JException("Failed to write to socket.\n PHP said: " . $this->getLastError());
    }

    return (strlen($message) == $wrote);
  }
  /**
   * Reads from the Socket, returns false if there is nothing to read
   * 
   * @param int $bufferSize 
   * @return mixed
   */
  public function read($bufferSize = 1024) {
    if($this->link == null) {
        throw new JException("Socket not connected");
    }
    if(empty($bufferSize)) {
        $bufferSize = 1024;
    }

    $buffer = false;
    do {
      $in = "";
      $in = @socket_read($this->link, $bufferSize);

      // Connection reset
      if($this->getLastErrorNo() == self::PEER_RESET) {
          throw new JException("Connection reset by peer");
          break;
      }

      if(!empty($in)) {
          if($buffer === false) {
              $buffer = "";
          }
          $buffer .= $in;
      }
      // Socket error, close
      else if($in === '') {
          throw new JException("Socket closed unexpectedly");
          break;
      }
    } while(!empty($in));

    /**
     * Sometimes when the connection is closed unexpectedly, like
     * if someone presses the exit button in the window, a series of
     * question marks (?) are sent as binary characters to the server. 
     * Converting them to hex, returns 04 then to ASCII returns (?); So,
     * if the returned buffer is not false, and the resulting text is only
     * made up of ?'s then kill the buffer.
     */

    if($buffer !== false) {
        $tmp = bin2hex($buffer);
        if(($cnt = preg_match_all("/(04)/", $tmp, $matches)) > 0 && $cnt*2 == strlen($tmp)) {
            $buffer = false;
        }
    }
    
    return $buffer;
  }
  /**
   * At destruct, close the socket
   */
  public function __destruct() {
      @$this->close();
  }
  /**
   * Closes the socket
   * 
   * @return void
   */
  public function close() {
    // @see http://www.php.net/manual/en/function.socket-close.php#66810
    $socketOptions = array('l_onoff' => 1, 'l_linger' => 0);
    @socket_set_option($this->link, SOL_SOCKET, SO_LINGER, $socketOptions);
    @socket_close($this->link);
  }
  /**
   * Returns a string which contains the connection info
   *
   * @return string
   */
  public function getInfo() {
    $IP = "0.0.0.0";
    $port = 0;
    
    if($this->link == null) {
        throw new JException("Socket not connected");
    }
    
    socket_getsockname($this->link, $IP, $port);
    
    return "IP: $IP:$port";
  }
  /**
   * Returns the last error number
   * 
   * @return int
   */
  public function getLastErrorNo() {
    return socket_last_error($this->link);
  }
  /**
   * Returns the last error this socket has received
   *
   * @return string
   */
  public function getLastError() {
    return socket_strerror(socket_last_error($this->link));
  }
}
