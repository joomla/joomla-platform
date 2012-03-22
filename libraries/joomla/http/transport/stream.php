<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTTP
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * HTTP transport class for using PHP streams.
 *
 * @package     Joomla.Platform
 * @subpackage  HTTP
 * @since       11.3
 */
class JHttpTransportStream implements JHttpTransport
{
	/**
	 * @var    JRegistry  The client options.
	 * @since  11.3
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry  $options  Client options object.
	 *
	 * @since   11.3
	 * @throws  RuntimeException
	 */
	public function __construct(JRegistry $options)
	{
		// Verify that fopen() is available.
		if (!self::isSupported())
		{
			throw new RuntimeException('Cannot use a stream transport when fopen() is not available.');
		}

		// Verify that URLs can be used with fopen();
		if (!ini_get('allow_url_fopen'))
		{
			throw new RuntimeException('Cannot use a stream transport when "allow_url_fopen" is disabled.');
		}

		$this->options = $options;
	}

	/**
	 * Send a request to the server and return a JHttpResponse object with the response.
	 *
	 * @param   string   $method     The HTTP method for sending the request.
	 * @param   JUri     $uri        The URI to the resource to request.
	 * @param   mixed    $data       Either an associative array or a string to be sent with the request.
	 * @param   array    $headers    An array of request headers to send with the request.
	 * @param   integer  $timeout    Read timeout in seconds.
	 * @param   string   $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  JHttpResponse
	 *
	 * @since   11.3
	 */
	public function request($method, JUri $uri, $data = null, array $headers = null, $timeout = null, $userAgent = null)
	{
		// Create the stream context options array with the required method offset.
		$options = array('method' => strtoupper($method));

		// If data exists let's encode it and make sure our Content-type header is set.
		if (isset($data))
		{
			// If the data is a scalar value simply add it to the stream context options.
			if (is_scalar($data))
			{
				$options['content'] = $data;
			}
			// Otherwise we need to encode the value first.
			else
			{
				$options['content'] = http_build_query($data);
			}

			if (!isset($headers['Content-type']))
			{
				$headers['Content-type'] = 'application/x-www-form-urlencoded';
			}

			$headers['Content-length'] = strlen($options['content']);
		}

		// Build the headers string for the request.
		$headerString = null;
		if (isset($headers))
		{
			foreach ($headers as $key => $value)
			{
				$headerString .= $key . ': ' . $value . "\r\n";
			}

			// Add the headers string into the stream context options array.
			$options['header'] = trim($headerString, "\r\n");
		}

		// If an explicit timeout is given user it.
		if (isset($timeout))
		{
			$options['timeout'] = (int) $timeout;
		}

		// If an explicit user agent is given use it.
		if (isset($userAgent))
		{
			$options['user_agent'] = $userAgent;
		}

		// Ignore HTTP errors so that we can capture them.
		$options['ignore_errors'] = 1;

		// Create the stream context for the request.
		$context = stream_context_create(array('http' => $options));

		// Open the stream for reading.
		$stream = fopen((string) $uri, 'r', false, $context);

		// fopen($uri) returns false
		if ($stream === false)
		{
			throw new RuntimeException('HTTP request failed');
		}

		// Get the metadata for the stream, including response headers.
		$metadata = stream_get_meta_data($stream);

		// Get the contents from the stream.
		$content = stream_get_contents($stream);

		// Close the stream.
		fclose($stream);

		return $this->getResponse($metadata['wrapper_data'], $content);
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @param   array   $headers  The response headers as an array.
	 * @param   string  $body     The response body as a string.
	 *
	 * @return  JHttpResponse
	 *
	 * @since   11.3
	 * @throws  UnexpectedValueException
	 */
	protected function getResponse(array $headers, $body)
	{
		// Create the response object.
		$return = new JHttpResponse;

		// Set the body for the response.
		$return->body = $body;

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
			throw new UnexpectedValueException('No HTTP response code found.');
		}

		// Add the response headers to the response object.
		foreach ($headers as $header)
		{
			$pos = strpos($header, ':');
			$return->headers[trim(substr($header, 0, $pos))] = trim(substr($header, ($pos + 1)));
		}

		return $return;
	}

	/**
	 * method to check if http transport stream available for using
	 * 
	 * @return bool true if available else false
	 * 
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return function_exists('fopen') && is_callable('fopen');
	}
}
