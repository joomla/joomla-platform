<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.environment.uri');

/**
 * HTTP client class.
 *
 * @package     Joomla.Platform
 * @subpackage  Client
 * @since       11.1
 */
class JHttp
{
	/**
	 * Server connection resources array.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $connections = array();

	/**
	 * Timeout limit in seconds for the server connection.
	 *
	 * @var    int
	 * @since  11.1
	 */
	protected $timeout = 5;

	/**
	 * Server response string.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  Array of configuration options for the client.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		// If a connection timeout is set, use it.
		if (isset($options['timeout']))
		{
			$this->timeout = $options['timeout'];
		}
	}

	/**
	 * Destructor.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function __destruct()
	{
		// Close all the connections.
		foreach ($this->connections as $connection)
		{
			fclose($connection);
		}
	}

	/**
	 * Method to send the HEAD command to the server.
	 *
	 * @param   string  $url      Path to the resource.
	 * @param   array   $headers  An array of name-value pairs to include in the header of the request.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  Exception
	 */
	public function head($url, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try
		{
			$connection = $this->connect($uri);
		}
		catch (Exception $e)
		{
			return false;
		}

		// Send the command to the server.
		if (!$this->sendRequest($connection, 'HEAD', $uri, null, $headers))
		{
			return false;
		}

		return $this->getResponseObject();
	}

	/**
	 * Method to send the GET command to the server.
	 *
	 * @param   string  $url      Path to the resource.
	 * @param   array   $headers  An array of name-value pairs to include in the header of the request.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  Exception
	 */
	public function get($url, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try
		{
			$connection = $this->connect($uri);
		}
		catch (Exception $e)
		{
			return false;
		}

		// Send the command to the server.
		if (!$this->sendRequest($connection, 'GET', $uri, null, $headers))
		{
			return false;
		}

		return $this->getResponseObject();
	}

	/**
	 * Method to send the POST command to the server.
	 *
	 * @param   string  $url      Path to the resource.
	 * @param   array   $data     Associative array of key/value pairs to send as post values.
	 * @param   array   $headers  An array of name-value pairs to include in the header of the request.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  Exception
	 */
	public function post($url, $data, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try
		{
			$connection = $this->connect($uri);
		}
		catch (Exception $e)
		{
			return false;
		}

		// Send the command to the server.
		if (!$this->sendRequest($connection, 'POST', $uri, $data, $headers))
		{
			return false;
		}

		return $this->getResponseObject();
	}

	/**
	 * Send a command to the server and validate an expected response.
	 *
	 * @param   resource  $connection  The HTTP connection resource.
	 * @param   string    $method      The HTTP method for sending the request.
	 * @param   string    $uri         The URI to the resource to request.
	 * @param   array     $data        An array of key => value pairs to send with the request.
	 * @param   array     $headers     An array of request headers to send with the request.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  Exception
	 */
	protected function sendRequest($connection, $method, JUri $uri, $data = null, $headers = null)
	{
		// Make sure the connection is a valid resource.
		if (is_resource($connection))
		{
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($connection);
			if ($meta['timed_out'])
			{
				throw new Exception('Server connection timed out.', 0);
			}
		}
		else
		{
			throw new Exception('Not connected to server.', 0);
		}

		// Get the request path from the URI object.
		$path = $uri->toString(array('path', 'query'));

		// Build the request payload.
		$request = array();
		$request[] = strtoupper($method) . ' ' . ((empty($path)) ? '/' : $path) . ' HTTP/1.0';
		$request[] = 'Host: ' . $uri->getHost();

		// If no user agent is set use the base one.
		if (empty($headers) || !isset($headers['User-Agent']))
		{
			$request[] = 'User-Agent: JHttp | JoomlaPlatform/11.3';
		}

		// If there are custom headers to send add them to the request payload.
		if (is_array($headers))
		{
			foreach ($headers as $k => $v)
			{
				$request[] = $k . ': ' . $v;
			}
		}

		// If we have data to send add it to the request payload.
		if (!empty($data))
		{
			// If the data is an array, build the request query string.
			if (is_array($data))
			{
				$data = http_build_query($data);
			}

			$request[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
			$request[] = 'Content-Length: ' . strlen($data);
			$request[] = null;
			$request[] = $data;
		}

		// Send the request to the server.
		fwrite($connection, implode("\r\n", $request) . "\r\n\r\n");

		// Get the response data from the server.
		$this->response = null;
		while (!feof($connection))
		{
			$this->response .= fgets($connection, 4096);
		}

		return true;
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @return  JHttpResponse
	 *
	 * @since   11.1
	 * @throws  Exception
	 */
	protected function getResponseObject()
	{
		// Create the response object.
		$return = new JHttpResponse;

		// Split the response into headers and body.
		$response = explode("\r\n\r\n", $this->response, 2);

		// Get the response headers as an array.
		$headers = explode("\r\n", $response[0]);

		// Get the response code from the first offset of the response headers.
		preg_match('/[0-9]{3}/', array_shift($headers), $matches);
		$code = $matches[0];
		if (is_numeric($code))
		{
			$return->code = (int) $code;
		}
		// No valid response code was detected.
		else
		{
			throw new Exception('Invalid server response.', 0);
		}

		// Add the response headers to the response object.
		foreach ($headers as $header)
		{
			$pos = strpos($header, ':');
			$return->headers[trim(substr($header, 0, $pos))] = trim(substr($header, ($pos + 1)));
		}

		// Set the response body if it exists.
		if (!empty($response[1]))
		{
			$return->body = $response[1];
		}

		return $return;
	}

	/**
	 * Method to connect to a server and get the resource.
	 *
	 * @param   JUri  $uri  The URI to connect with.
	 *
	 * @return  mixed  Connection resource on success or boolean false on failure.
	 *
	 * @since   11.1
	 */
	protected function connect(JUri $uri)
	{
		// Initialize variables.
		$errno = null;
		$err = null;

		// Get the host from the uri.
		$host = ($uri->isSSL()) ? 'ssl://' . $uri->getHost() : $uri->getHost();

		// If the port is not explicitly set in the URI detect it.
		if (!$uri->getPort())
		{
			$port = ($uri->getScheme() == 'https') ? 443 : 80;
		}
		// Use the set port.
		else
		{
			$port = $uri->getPort();
		}

		// Build the connection key for resource memory caching.
		$key = md5($host . $port);

		// If the connection already exists, use it.
		if (!empty($this->connections[$key]) && is_resource($this->connections[$key]))
		{
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($this->connections[$key]);
			if (!$meta['timed_out'])
			{
				return $this->connections[$key];
			}
		}

		// Attempt to connect to the server.
		$this->connections[$key] = fsockopen($host, $port, $errno, $err, $this->timeout);
		if ($this->connections[$key])
		{
			stream_set_timeout($this->connections[$key], $this->timeout);
		}

		return $this->connections[$key];
	}
}

/**
 * HTTP response data object class.
 *
 * @package     Joomla.Platform
 * @subpackage  Client
 * @since       11.1
 */
class JHttpResponse
{
	/**
	 * @var    int  The server response code.
	 * @since  11.1
	 */
	public $code;

	/**
	 * @var    array  Response headers.
	 * @since  11.1
	 */
	public $headers = array();

	/**
	 * @var    string  Server response body.
	 * @since  11.1
	 */
	public $body;
}
