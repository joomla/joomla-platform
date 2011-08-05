<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/** Error Codes:
 * - 30 : Unable to connect to host
 * - 31 : Not connected
 * - 32 : Unable to send command to server
 * - 33 : Bad username
 * - 34 : Bad password
 * - 35 : Bad response
 * - 36 : Passive mode failed
 * - 37 : Data transfer error
 * - 38 : Local filesystem error
 */

if (!defined('CRLF'))
{
	define('CRLF', "\r\n");
}
if (!defined("FTP_AUTOASCII"))
{
	define("FTP_AUTOASCII", -1);
}
if (!defined("FTP_BINARY"))
{
	define("FTP_BINARY", 1);
}
if (!defined("FTP_ASCII"))
{
	define("FTP_ASCII", 0);
}

// Is FTP extension loaded?  If not try to load it
if (!extension_loaded('ftp'))
{
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	{
		@ dl('php_ftp.dll');
	}
	else
	{
		@ dl('ftp.so');
	}
}
if (!defined('FTP_NATIVE'))
{
	define('FTP_NATIVE', (function_exists('ftp_connect')) ? 1 : 0);
}

/**
 * FTP client class
 *
 * @package     Joomla.Platform
 * @subpackage  Client
 * @since       11.1
 */
class JFTP extends JObject
{
	/**
	 * @var    resource  Socket resource
	 * @since  11.1
	 */
	var $_conn = null;

	/**
	 * @var    resource  Data port connection resource
	 * @since  11.1
	 */
	var $_dataconn = null;

	/**
	 * @var    array  Passive connection information
	 * @since  11.1
	 */
	var $_pasv = null;

	/**
	 * @var    string  Response Message
	 * @since  11.1
	 */
	var $_response = null;

	/**
	 * @var    integer  Timeout limit
	 * @since  11.1
	 */
	var $_timeout = 15;

	/**
	 * @var    integer  Transfer Type
	 * @since  11.1
	 */
	var $_type = null;

	/**
	 * @var    string  Native OS Type
	 * @since  11.1
	 */
	var $_OS = null;

	/**
	 * @var    array  Array to hold ascii format file extensions
	 * @since   11.1
	 */
	var $_autoAscii = array(
		"asp",
		"bat",
		"c",
		"cpp",
		"csv",
		"h",
		"htm",
		"html",
		"shtml",
		"ini",
		"inc",
		"log",
		"php",
		"php3",
		"pl",
		"perl",
		"sh",
		"sql",
		"txt",
		"xhtml",
		"xml");

	/**
	 * Array to hold native line ending characters
	 *
	 * @var    array
	 * @since  11.1
	 */
	var $_lineEndings = array('UNIX' => "\n", 'MAC' => "\r", 'WIN' => "\r\n");

	/**
	 * JFTP object constructor
	 *
	 * @param   array  $options  Associative array of options to set
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	function __construct($options = array())
	{

		// If default transfer type is not set, set it to autoascii detect
		if (!isset($options['type']))
		{
			$options['type'] = FTP_BINARY;
		}
		$this->setOptions($options);

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$this->_OS = 'WIN';
		}
		elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC')
		{
			$this->_OS = 'MAC';
		}
		else
		{
			$this->_OS = 'UNIX';
		}

		if (FTP_NATIVE)
		{
			// Import the generic buffer stream handler
			jimport('joomla.utilities.buffer');
			// Autoloading fails for JBuffer as the class is used as a stream handler
			JLoader::load('JBuffer');
		}
	}

	/**
	 * JFTP object destructor
	 *
	 * Closes an existing connection, if we have one
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	function __destruct()
	{
		if (is_resource($this->_conn))
		{
			$this->quit();
		}
	}

	/**
	 * Returns the global FTP connector object, only creating it
	 * if it doesn't already exist.
	 *
	 * You may optionally specify a username and password in the parameters. If you do so,
	 * you may not login() again with different credentials using the same object.
	 * If you do not use this option, you must quit() the current connection when you
	 * are done, to free it for use by others.
	 *
	 * @param   string  $host     Host to connect to
	 * @param   string  $port     Port to connect to
	 * @param   array   $options  Array with any of these options: type=>[FTP_AUTOASCII|FTP_ASCII|FTP_BINARY], timeout=>(int)
	 * @param   string  $user     Username to use for a connection
	 * @param   string  $pass     Password to use for a connection
	 *
	 * @return  JFTP    The FTP Client object.
	 *
	 * @since   11.1
	 */
	function getInstance($host = '127.0.0.1', $port = '21', $options = null, $user = null, $pass = null)
	{
		static $instances = array();

		$signature = $user . ':' . $pass . '@' . $host . ":" . $port;

		// Create a new instance, or set the options of an existing one
		if (!isset($instances[$signature]) || !is_object($instances[$signature]))
		{
			$instances[$signature] = new JFTP($options);
		}
		else
		{
			$instances[$signature]->setOptions($options);
		}

		// Connect to the server, and login, if requested
		if (!$instances[$signature]->isConnected())
		{
			$return = $instances[$signature]->connect($host, $port);
			if ($return && $user !== null && $pass !== null)
			{
				$instances[$signature]->login($user, $pass);
			}
		}

		return $instances[$signature];
	}

	/**
	 * Set client options
	 *
	 * @param   array  $options  Associative array of options to set
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function setOptions($options)
	{

		if (isset($options['type']))
		{
			$this->_type = $options['type'];
		}
		if (isset($options['timeout']))
		{
			$this->_timeout = $options['timeout'];
		}
		return true;
	}

	/**
	 * Method to connect to a FTP server
	 *
	 * @param   string  $host  Host to connect to [Default: 127.0.0.1]
	 * @param   string  $port  Port to connect on [Default: port 21]
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function connect($host = '127.0.0.1', $port = 21)
	{

		// Initialise variables.
		$errno = null;
		$err = null;

		// If already connected, return
		if (is_resource($this->_conn))
		{
			return true;
		}

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			$this->_conn = @ftp_connect($host, $port, $this->_timeout);
			if ($this->_conn === false)
			{
				JError::raiseWarning('30', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_NO_CONNECT', $host, $port));
				return false;
			}
			// Set the timeout for this connection
			ftp_set_option($this->_conn, FTP_TIMEOUT_SEC, $this->_timeout);
			return true;
		}

		// Connect to the FTP server.
		$this->_conn = @ fsockopen($host, $port, $errno, $err, $this->_timeout);
		if (!$this->_conn)
		{
			JError::raiseWarning('30', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_NO_CONNECT_SOCKET', $host, $port, $errno, $err));
			return false;
		}

		// Set the timeout for this connection
		socket_set_timeout($this->_conn, $this->_timeout);

		// Check for welcome response code
		if (!$this->_verifyResponse(220))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_BAD_RESPONSE', $this->_response));
			return false;
		}

		return true;
	}

	/**
	 * Method to determine if the object is connected to an FTP server
	 *
	 * @return  boolean  True if connected
	 *
	 * @since   11.1
	 */
	function isConnected()
	{
		return is_resource($this->_conn);
	}

	/**
	 * Method to login to a server once connected
	 *
	 * @param   string  $user  Username to login to the server
	 * @param   string  $pass  Password to login to the server
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function login($user = 'anonymous', $pass = 'jftp@joomla.org')
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_login($this->_conn, $user, $pass) === false)
			{
				JError::raiseWarning('30', 'JFTP::login: Unable to login');
				return false;
			}
			return true;
		}

		// Send the username
		if (!$this->_putCmd('USER ' . $user, array(331, 503)))
		{
			JError::raiseWarning('33', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_BAD_USERNAME', $this->_response, $user));
			return false;
		}

		// If we are already logged in, continue :)
		if ($this->_responseCode == 503)
		{
			return true;
		}

		// Send the password
		if (!$this->_putCmd('PASS ' . $pass, 230))
		{
			JError::raiseWarning('34', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_BAD_PASSWORD', $this->_response, str_repeat('*', strlen($pass))));
			return false;
		}

		return true;
	}

	/**
	 * Method to quit and close the connection
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function quit()
	{

		// If native FTP support is enabled lets use it...
		if (FTP_NATIVE)
		{
			@ftp_close($this->_conn);
			return true;
		}

		// Logout and close connection
		@fwrite($this->_conn, "QUIT\r\n");
		@fclose($this->_conn);

		return true;
	}

	/**
	 * Method to retrieve the current working directory on the FTP server
	 *
	 * @return  string   Current working directory
	 *
	 * @since   11.1
	 */
	function pwd()
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (($ret = @ftp_pwd($this->_conn)) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_PWD_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return $ret;
		}

		// Initialise variables.
		$match = array(null);

		// Send print working directory command and verify success
		if (!$this->_putCmd('PWD', 257))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PWD_BAD_RESPONSE', $this->_response));
			return false;
		}

		// Match just the path
		preg_match('/"[^"\r\n]*"/', $this->_response, $match);

		// Return the cleaned path
		return preg_replace("/\"/", "", $match[0]);
	}

	/**
	 * Method to system string from the FTP server
	 *
	 * @return  string   System identifier string
	 *
	 * @since   11.1
	 */
	function syst()
	{

		// If native FTP support is enabled lets use it...
		if (FTP_NATIVE)
		{
			if (($ret = @ftp_systype($this->_conn)) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_SYS_BAD_RESPONSE_NATIVE'));
				return false;
			}
		}
		else
		{
			// Send print working directory command and verify success
			if (!$this->_putCmd('SYST', 215))
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_SYST_BAD_RESPONSE', $this->_response));
				return false;
			}
			$ret = $this->_response;
		}

		// Match the system string to an OS
		if (strpos(strtoupper($ret), 'MAC') !== false)
		{
			$ret = 'MAC';
		}
		elseif (strpos(strtoupper($ret), 'WIN') !== false)
		{
			$ret = 'WIN';
		}
		else
		{
			$ret = 'UNIX';
		}

		// Return the os type
		return $ret;
	}

	/**
	 * Method to change the current working directory on the FTP server
	 *
	 * @param   string  $path  Path to change into on the server
	 *
	 * @return  boolean True if successful
	 *
	 * @since   11.1
	 */
	function chdir($path)
	{

		// If native FTP support is enabled lets use it...
		if (FTP_NATIVE)
		{
			if (@ftp_chdir($this->_conn, $path) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_CHDIR_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return true;
		}

		// Send change directory command and verify success
		if (!$this->_putCmd('CWD ' . $path, 250))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_CHDIR_BAD_RESPONSE', $this->_response, $path));
			return false;
		}

		return true;
	}

	/**
	 * Method to reinitialise the server, ie. need to login again
	 *
	 * NOTE: This command not available on all servers
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function reinit()
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_site($this->_conn, 'REIN') === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_REINIT_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return true;
		}

		// Send reinitialise command to the server
		if (!$this->_putCmd('REIN', 220))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_REINIT_BAD_RESPONSE', $this->_response));
			return false;
		}

		return true;
	}

	/**
	 * Method to rename a file/folder on the FTP server
	 *
	 * @param   string  $from  Path to change file/folder from
	 * @param   string  $to    Path to change file/folder to
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function rename($from, $to)
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_rename($this->_conn, $from, $to) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_RENAME_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return true;
		}

		// Send rename from command to the server
		if (!$this->_putCmd('RNFR ' . $from, 350))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_RENAME_BAD_RESPONSE_FROM', $this->_response, $from));
			return false;
		}

		// Send rename to command to the server
		if (!$this->_putCmd('RNTO ' . $to, 250))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_RENAME_BAD_RESPONSE_TO', $this->_response, $to));
			return false;
		}

		return true;
	}

	/**
	 * Method to change mode for a path on the FTP server
	 *
	 * @param   string  $path  Path to change mode on
	 * @param   mixed   $mode  Octal value to change mode to, e.g. '0777', 0777 or 511 (string or integer)
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function chmod($path, $mode)
	{

		// If no filename is given, we assume the current directory is the target
		if ($path == '')
		{
			$path = '.';
		}

		// Convert the mode to a string
		if (is_int($mode))
		{
			$mode = decoct($mode);
		}

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_site($this->_conn, 'CHMOD ' . $mode . ' ' . $path) === false)
			{
				if ($this->_OS != 'WIN')
				{
					JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_CHMOD_BAD_RESPONSE_NATIVE'));
				}
				return false;
			}
			return true;
		}

		// Send change mode command and verify success [must convert mode from octal]
		if (!$this->_putCmd('SITE CHMOD ' . $mode . ' ' . $path, array(200, 250)))
		{
			if ($this->_OS != 'WIN')
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_CHMOD_BAD_RESPONSE', $this->_response, $path, $mode));
			}
			return false;
		}
		return true;
	}

	/**
	 * Method to delete a path [file/folder] on the FTP server
	 *
	 * @param   string  $path  Path to delete
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function delete($path)
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_delete($this->_conn, $path) === false)
			{
				if (@ftp_rmdir($this->_conn, $path) === false)
				{
					JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_DELETE_BAD_RESPONSE_NATIVE'));
					return false;
				}
			}
			return true;
		}

		// Send delete file command and if that doesn't work, try to remove a directory
		if (!$this->_putCmd('DELE ' . $path, 250))
		{
			if (!$this->_putCmd('RMD ' . $path, 250))
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_DELETE_BAD_RESPONSE', $this->_response, $path));
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to create a directory on the FTP server
	 *
	 * @param   string  $path  Directory to create
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function mkdir($path)
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_mkdir($this->_conn, $path) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_MKDIR_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return true;
		}

		// Send change directory command and verify success
		if (!$this->_putCmd('MKD ' . $path, 257))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_MKDIR_BAD_RESPONSE', $this->_response, $path));
			return false;
		}
		return true;
	}

	/**
	 * Method to restart data transfer at a given byte
	 *
	 * @param   integer  $point  Byte to restart transfer at
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function restart($point)
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			if (@ftp_site($this->_conn, 'REST ' . $point) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_RESTART_BAD_RESPONSE_NATIVE'));
				return false;
			}
			return true;
		}

		// Send restart command and verify success
		if (!$this->_putCmd('REST ' . $point, 350))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_RESTART_BAD_RESPONSE', $this->_response, $point));
			return false;
		}

		return true;
	}

	/**
	 * Method to create an empty file on the FTP server
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function create($path)
	{

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_CREATE_BAD_RESPONSE_PASSIVE'));
				return false;
			}

			$buffer = fopen('buffer://tmp', 'r');
			if (@ftp_fput($this->_conn, $path, $buffer, FTP_ASCII) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_CREATE_BAD_RESPONSE_BUFFER'));
				fclose($buffer);
				return false;
			}
			fclose($buffer);
			return true;
		}

		// Start passive mode
		if (!$this->_passive())
		{
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_CREATE_BAD_RESPONSE_PASSIVE'));
			return false;
		}

		if (!$this->_putCmd('STOR ' . $path, array(150, 125)))
		{
			@ fclose($this->_dataconn);
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_CREATE_BAD_RESPONSE', $this->_response, $path));
			return false;
		}

		// To create a zero byte upload close the data port connection
		fclose($this->_dataconn);

		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_CREATE_BAD_RESPONSE_TRANSFER', $this->_response, $path));
			return false;
		}

		return true;
	}

	/**
	 * Method to read a file from the FTP server's contents into a buffer
	 *
	 * @param   string  $remote   Path to remote file to read on the FTP server
	 * @param   string  &$buffer  Buffer variable to read file contents into
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function read($remote, &$buffer)
	{

		// Determine file type
		$mode = $this->_findMode($remote);

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_READ_BAD_RESPONSE_PASSIVE'));
				return false;
			}

			$tmp = fopen('buffer://tmp', 'br+');
			if (@ftp_fget($this->_conn, $tmp, $remote, $mode) === false)
			{
				fclose($tmp);
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_READ_BAD_RESPONSE_BUFFER'));
				return false;
			}
			// Read tmp buffer contents
			rewind($tmp);
			$buffer = '';
			while (!feof($tmp))
			{
				$buffer .= fread($tmp, 8192);
			}
			fclose($tmp);
			return true;
		}

		$this->_mode($mode);

		// Start passive mode
		if (!$this->_passive())
		{
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_READ_BAD_RESPONSE_PASSIVE'));
			return false;
		}

		if (!$this->_putCmd('RETR ' . $remote, array(150, 125)))
		{
			@ fclose($this->_dataconn);
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_READ_BAD_RESPONSE', $this->_response, $remote));
			return false;
		}

		// Read data from data port connection and add to the buffer
		$buffer = '';
		while (!feof($this->_dataconn))
		{
			$buffer .= fread($this->_dataconn, 4096);
		}

		// Close the data port connection
		fclose($this->_dataconn);

		// Let's try to cleanup some line endings if it is ascii
		if ($mode == FTP_ASCII)
		{
			$buffer = preg_replace("/" . CRLF . "/", $this->_lineEndings[$this->_OS], $buffer);
		}

		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_READ_BAD_RESPONSE_TRANSFER', $this->_response, $remote));
			return false;
		}

		return true;
	}

	/**
	 * Method to get a file from the FTP server and save it to a local file
	 *
	 * @param   string  $local   Local path to save remote file to
	 * @param   string  $remote  Path to remote file to get on the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function get($local, $remote)
	{

		// Determine file type
		$mode = $this->_findMode($remote);

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_GET_PASSIVE'));
				return false;
			}

			if (@ftp_get($this->_conn, $local, $remote, $mode) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_GET_BAD_RESPONSE'));
				return false;
			}
			return true;
		}

		$this->_mode($mode);

		// Check to see if the local file can be opened for writing
		$fp = fopen($local, "wb");
		if (!$fp)
		{
			JError::raiseWarning('38', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_GET_WRITING_LOCAL', $local));
			return false;
		}

		// Start passive mode
		if (!$this->_passive())
		{
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_GET_PASSIVE'));
			return false;
		}

		if (!$this->_putCmd('RETR ' . $remote, array(150, 125)))
		{
			@ fclose($this->_dataconn);
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_GET_BAD_RESPONSE_RETR', $this->_response, $remote));
			return false;
		}

		// Read data from data port connection and add to the buffer
		while (!feof($this->_dataconn))
		{
			$buffer = fread($this->_dataconn, 4096);
			$ret = fwrite($fp, $buffer, 4096);
		}

		// Close the data port connection and file pointer
		fclose($this->_dataconn);
		fclose($fp);

		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_GET_BAD_RESPONSE_TRANSFER', $this->_response, $remote));
			return false;
		}

		return true;
	}

	/**
	 * Method to store a file to the FTP server
	 *
	 * @param   string  $local   Path to local file to store on the FTP server
	 * @param   string  $remote  FTP path to file to create
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function store($local, $remote = null)
	{

		// If remote file is not given, use the filename of the local file in the current
		// working directory.
		if ($remote == null)
		{
			$remote = basename($local);
		}

		// Determine file type
		$mode = $this->_findMode($remote);

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_STORE_PASSIVE'));
				return false;
			}

			if (@ftp_put($this->_conn, $remote, $local, $mode) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_STORE_BAD_RESPONSE'));
				return false;
			}
			return true;
		}

		$this->_mode($mode);

		// Check to see if the local file exists and if so open it for reading
		if (@ file_exists($local))
		{
			$fp = fopen($local, "rb");
			if (!$fp)
			{
				JError::raiseWarning('38', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_STORE_READING_LOCAL', $local));
				return false;
			}
		}
		else
		{
			JError::raiseWarning('38', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_STORE_FIND_LOCAL', $local));
			return false;
		}

		// Start passive mode
		if (!$this->_passive())
		{
			@ fclose($fp);
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_STORE_PASSIVE'));
			return false;
		}

		// Send store command to the FTP server
		if (!$this->_putCmd('STOR ' . $remote, array(150, 125)))
		{
			@ fclose($fp);
			@ fclose($this->_dataconn);
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_STORE_BAD_RESPONSE_STOR', $this->_response, $remote));
			return false;
		}

		// Do actual file transfer, read local file and write to data port connection
		while (!feof($fp))
		{
			$line = fread($fp, 4096);
			do
			{
				if (($result = @ fwrite($this->_dataconn, $line)) === false)
				{
					JError::raiseWarning('37', JText::_('JLIB_CLIENT_ERROR_JFTP_STORE_DATA_PORT'));
					return false;
				}
				$line = substr($line, $result);
			}
			while ($line != "");
		}

		fclose($fp);
		fclose($this->_dataconn);

		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_STORE_BAD_RESPONSE_TRANSFER', $this->_response, $remote));
			return false;
		}

		return true;
	}

	/**
	 * Method to write a string to the FTP server
	 *
	 * @param   string  $remote  FTP path to file to write to
	 * @param   string  $buffer  Contents to write to the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function write($remote, $buffer)
	{

		// Determine file type
		$mode = $this->_findMode($remote);

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_WRITE_PASSIVE'));
				return false;
			}

			$tmp = fopen('buffer://tmp', 'br+');
			fwrite($tmp, $buffer);
			rewind($tmp);
			if (@ftp_fput($this->_conn, $remote, $tmp, $mode) === false)
			{
				fclose($tmp);
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_WRITE_BAD_RESPONSE'));
				return false;
			}
			fclose($tmp);
			return true;
		}

		// First we need to set the transfer mode
		$this->_mode($mode);

		// Start passive mode
		if (!$this->_passive())
		{
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_WRITE_PASSIVE'));
			return false;
		}

		// Send store command to the FTP server
		if (!$this->_putCmd('STOR ' . $remote, array(150, 125)))
		{
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_WRITE_BAD_RESPONSE_STOR', $this->_response, $remote));
			@ fclose($this->_dataconn);
			return false;
		}

		// Write buffer to the data connection port
		do
		{
			if (($result = @ fwrite($this->_dataconn, $buffer)) === false)
			{
				JError::raiseWarning('37', JText::_('JLIB_CLIENT_ERROR_JFTP_WRITE_DATA_PORT'));
				return false;
			}
			$buffer = substr($buffer, $result);
		}
		while ($buffer != "");

		// Close the data connection port [Data transfer complete]
		fclose($this->_dataconn);

		// Verify that the server recieved the transfer
		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_WRITE_BAD_RESPONSE_TRANSFER', $this->_response, $remote));
			return false;
		}

		return true;
	}

	/**
	 * Method to list the filenames of the contents of a directory on the FTP server
	 *
	 * Note: Some servers also return folder names. However, to be sure to list folders on all
	 * servers, you should use listDetails() instead if you also need to deal with folders
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  string  Directory listing
	 *
	 * @since   11.1
	 */
	function listNames($path = null)
	{

		// Initialise variables.
		$data = null;

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTNAMES_PASSIVE'));
				return false;
			}

			if (($list = @ftp_nlist($this->_conn, $path)) === false)
			{
				// Workaround for empty directories on some servers
				if ($this->listDetails($path, 'files') === array())
				{
					return array();
				}
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTNAMES_BAD_RESPONSE'));
				return false;
			}
			$list = preg_replace('#^' . preg_quote($path, '#') . '[/\\\\]?#', '', $list);
			if ($keys = array_merge(array_keys($list, '.'), array_keys($list, '..')))
			{
				foreach ($keys as $key)
				{
					unset($list[$key]);
				}
			}
			return $list;
		}

		/*
		 * If a path exists, prepend a space
		 */
		if ($path != null)
		{
			$path = ' ' . $path;
		}

		// Start passive mode
		if (!$this->_passive())
		{
			JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTNAMES_PASSIVE'));
			return false;
		}

		if (!$this->_putCmd('NLST' . $path, array(150, 125)))
		{
			@ fclose($this->_dataconn);
			// Workaround for empty directories on some servers
			if ($this->listDetails($path, 'files') === array())
			{
				return array();
			}
			JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_LISTNAMES_BAD_RESPONSE_NLST', $this->_response, $path));
			return false;
		}

		// Read in the file listing.
		while (!feof($this->_dataconn))
		{
			$data .= fread($this->_dataconn, 4096);
		}
		fclose($this->_dataconn);

		// Everything go okay?
		if (!$this->_verifyResponse(226))
		{
			JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_LISTNAMES_BAD_RESPONSE_TRANSFER', $this->_response, $path));
			return false;
		}

		$data = preg_split("/[" . CRLF . "]+/", $data, -1, PREG_SPLIT_NO_EMPTY);
		$data = preg_replace('#^' . preg_quote(substr($path, 1), '#') . '[/\\\\]?#', '', $data);
		if ($keys = array_merge(array_keys($data, '.'), array_keys($data, '..')))
		{
			foreach ($keys as $key)
			{
				unset($data[$key]);
			}
		}
		return $data;
	}

	/**
	 * Method to list the contents of a directory on the FTP server
	 *
	 * @param   string  $path  Path to the local file to be stored on the FTP server
	 * @param   string  $type  Return type [raw|all|folders|files]
	 *
	 * @return  mixed  If $type is raw: string Directory listing, otherwise array of string with file-names
	 */
	function listDetails($path = null, $type = 'all')
	{

		// Initialise variables.
		$dir_list = array();
		$data = null;
		$regs = null;
		// TODO: Deal with recurse -- nightmare
		// For now we will just set it to false
		$recurse = false;

		// If native FTP support is enabled let's use it...
		if (FTP_NATIVE)
		{
			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_PASSIVE'));
				return false;
			}

			if (($contents = @ftp_rawlist($this->_conn, $path)) === false)
			{
				JError::raiseWarning('35', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_BAD_RESPONSE'));
				return false;
			}
		}
		else
		{
			// Non Native mode


			// Start passive mode
			if (!$this->_passive())
			{
				JError::raiseWarning('36', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_PASSIVE'));
				return false;
			}

			// If a path exists, prepend a space
			if ($path != null)
			{
				$path = ' ' . $path;
			}

			// Request the file listing
			if (!$this->_putCmd(($recurse == true) ? 'LIST -R' : 'LIST' . $path, array(150, 125)))
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_BAD_RESPONSE_LIST', $this->_response, $path));
				@ fclose($this->_dataconn);
				return false;
			}

			// Read in the file listing.
			while (!feof($this->_dataconn))
			{
				$data .= fread($this->_dataconn, 4096);
			}
			fclose($this->_dataconn);

			// Everything go okay?
			if (!$this->_verifyResponse(226))
			{
				JError::raiseWarning('37', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_BAD_RESPONSE_TRANSFER', $this->_response, $path));
				return false;
			}

			$contents = explode(CRLF, $data);
		}

		// If only raw output is requested we are done
		if ($type == 'raw')
		{
			return $data;
		}

		// If we received the listing of an empty directory, we are done as well
		if (empty($contents[0]))
		{
			return $dir_list;
		}

		// If the server returned the number of results in the first response, let's dump it
		if (strtolower(substr($contents[0], 0, 6)) == 'total ')
		{
			array_shift($contents);
			if (!isset($contents[0]) || empty($contents[0]))
			{
				return $dir_list;
			}
		}

		// Regular expressions for the directory listing parsing.
		$regexps = array(
			'UNIX' => '#([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{1,2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'MAC' => '#([-dl][rwxstST-]+).* ?([0-9 ]*)?([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'WIN' => '#([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)#'
		);

		// Find out the format of the directory listing by matching one of the regexps
		$osType = null;
		foreach ($regexps as $k => $v)
		{
			if (@preg_match($v, $contents[0]))
			{
				$osType = $k;
				$regexp = $v;
				break;
			}
		}
		if (!$osType)
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_CLIENT_ERROR_JFTP_LISTDETAILS_UNRECOGNISED'));
			return false;
		}

		/*
		 * Here is where it is going to get dirty....
		 */
		if ($osType == 'UNIX')
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) strpos("-dl", $regs[1]{0});
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = @date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}
		elseif ($osType == 'MAC')
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) strpos("-dl", $regs[1]{0});
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}
		else
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) ($regs[7] == '<DIR>');
					$timestamp = strtotime("$regs[3]-$regs[1]-$regs[2] $regs[4]:$regs[5]$regs[6]");
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = '';
					//$tmp_array['number'] = 0;
					$tmp_array['user'] = '';
					$tmp_array['group'] = '';
					$tmp_array['size'] = (int) $regs[7];
					$tmp_array['date'] = date('m-d', $timestamp);
					$tmp_array['time'] = date('H:i', $timestamp);
					$tmp_array['name'] = $regs[8];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}

		return $dir_list;
	}

	/**
	 * Send command to the FTP server and validate an expected response code
	 *
	 * @param   string  $cmd               Command to send to the FTP server
	 * @param   mixed   $expectedResponse  Integer response code or array of integer response codes
	 *
	 * @return  boolean  True if command executed successfully
	 *
	 * @since   11.1
	 */
	function _putCmd($cmd, $expectedResponse)
	{

		// Make sure we have a connection to the server
		if (!is_resource($this->_conn))
		{
			JError::raiseWarning('31', JText::_('JLIB_CLIENT_ERROR_JFTP_PUTCMD_UNCONNECTED'));
			return false;
		}

		// Send the command to the server
		if (!fwrite($this->_conn, $cmd . "\r\n"))
		{
			JError::raiseWarning('32', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PUTCMD_SEND', $cmd));
		}

		return $this->_verifyResponse($expectedResponse);
	}

	/**
	 * Verify the response code from the server and log response if flag is set
	 *
	 * @param   mixed  $expected  Integer response code or array of integer response codes
	 *
	 * @return  boolean  True if response code from the server is expected
	 *
	 * @since   11.1
	 */
	function _verifyResponse($expected)
	{

		// Initialise variables.
		$parts = null;

		// Wait for a response from the server, but timeout after the set time limit
		$endTime = time() + $this->_timeout;
		$this->_response = '';
		do
		{
			$this->_response .= fgets($this->_conn, 4096);
		}
		while (!preg_match("/^([0-9]{3})(-(.*" . CRLF . ")+\\1)? [^" . CRLF . "]+" . CRLF . "$/", $this->_response, $parts) && time() < $endTime);

		// Catch a timeout or bad response
		if (!isset($parts[1]))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_VERIFYRESPONSE', $this->_response));
			return false;
		}

		// Separate the code from the message
		$this->_responseCode = $parts[1];
		$this->_responseMsg = $parts[0];

		// Did the server respond with the code we wanted?
		if (is_array($expected))
		{
			if (in_array($this->_responseCode, $expected))
			{
				$retval = true;
			}
			else
			{
				$retval = false;
			}
		}
		else
		{
			if ($this->_responseCode == $expected)
			{
				$retval = true;
			}
			else
			{
				$retval = false;
			}
		}
		return $retval;
	}

	/**
	 * Set server to passive mode and open a data port connection
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function _passive()
	{

		// Initialize variables.
		$match = array();
		$parts = array();
		$errno = null;
		$err = null;

		// Make sure we have a connection to the server
		if (!is_resource($this->_conn))
		{
			JError::raiseWarning('31', JText::_('JLIB_CLIENT_ERROR_JFTP_PASSIVE_CONNECT_PORT'));
			return false;
		}

		// Request a passive connection - this means, we'll talk to you, you don't talk to us.
		@ fwrite($this->_conn, "PASV\r\n");

		// Wait for a response from the server, but timeout after the set time limit
		$endTime = time() + $this->_timeout;
		$this->_response = '';
		do
		{
			$this->_response .= fgets($this->_conn, 4096);
		}
		while (!preg_match("/^([0-9]{3})(-(.*" . CRLF . ")+\\1)? [^" . CRLF . "]+" . CRLF . "$/", $this->_response, $parts) && time() < $endTime);

		// Catch a timeout or bad response
		if (!isset($parts[1]))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PASSIVE_RESPONSE', $this->_response));
			return false;
		}

		// Separate the code from the message
		$this->_responseCode = $parts[1];
		$this->_responseMsg = $parts[0];

		// If it's not 227, we weren't given an IP and port, which means it failed.
		if ($this->_responseCode != '227')
		{
			JError::raiseWarning('36', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PASSIVE_IP_OBTAIN', $this->_responseMsg));
			return false;
		}

		// Snatch the IP and port information, or die horribly trying...
		if (preg_match('~\((\d+),\s*(\d+),\s*(\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))\)~', $this->_responseMsg, $match) == 0)
		{
			JError::raiseWarning('36', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PASSIVE_IP_VALID', $this->_responseMsg));
			return false;
		}

		// This is pretty simple - store it for later use ;).
		$this->_pasv = array('ip' => $match[1] . '.' . $match[2] . '.' . $match[3] . '.' . $match[4], 'port' => $match[5] * 256 + $match[6]);

		// Connect, assuming we've got a connection.
		$this->_dataconn = @fsockopen($this->_pasv['ip'], $this->_pasv['port'], $errno, $err, $this->_timeout);
		if (!$this->_dataconn)
		{
			JError::raiseWarning('30', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_PASSIVE_CONNECT', $this->_pasv['ip'], $this->_pasv['port'], $errno, $err));
			return false;
		}

		// Set the timeout for this connection
		socket_set_timeout($this->_conn, $this->_timeout);

		return true;
	}

	/**
	 * Method to find out the correct transfer mode for a specific file
	 *
	 * @param   string  $fileName  Name of the file
	 *
	 * @return  integer Transfer-mode for this filetype [FTP_ASCII|FTP_BINARY]
	 *
	 * @since   11.1
	 */
	function _findMode($fileName)
	{
		if ($this->_type == FTP_AUTOASCII)
		{
			$dot = strrpos($fileName, '.') + 1;
			$ext = substr($fileName, $dot);

			if (in_array($ext, $this->_autoAscii))
			{
				$mode = FTP_ASCII;
			}
			else
			{
				$mode = FTP_BINARY;
			}
		}
		elseif ($this->_type == FTP_ASCII)
		{
			$mode = FTP_ASCII;
		}
		else
		{
			$mode = FTP_BINARY;
		}
		return $mode;
	}

	/**
	 * Set transfer mode
	 *
	 * @param   integer  $mode  Integer representation of data transfer mode [1:Binary|0:Ascii]
	 * Defined constants can also be used [FTP_BINARY|FTP_ASCII]
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	function _mode($mode)
	{
		if ($mode == FTP_BINARY)
		{
			if (!$this->_putCmd("TYPE I", 200))
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_MODE_BINARY', $this->_response));
				return false;
			}
		}
		else
		{
			if (!$this->_putCmd("TYPE A", 200))
			{
				JError::raiseWarning('35', JText::sprintf('JLIB_CLIENT_ERROR_JFTP_MODE_ASCII', $this->_response));
				return false;
			}
		}
		return true;
	}
}
