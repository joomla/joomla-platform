<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Oauth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
jimport('joomla.environment.response');
jimport('joomla.environment.uri');

/**
 * Joomla Platform class for interacting with an OAuth 2.0 server.
 *
 * @package     Joomla.Platform
 * @subpackage  Oauth
 * @since       1234
 */
class JOauthOauth2client
{
	/**
	 * @var    JRegistry  Options for the OAuth2Client object.
	 * @since  1234
	 */
	protected $options;

	/**
	 * @var    JHttpTransport  The HTTP transport object to use in sending HTTP requests.
	 * @since  1234
	 */
	protected $client;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  1234
	 */
	protected $http;

	/**
	 * @var    JInput  The input object to use in retrieving GET/POST data.
	 * @since  1234
	 */
	protected $input;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry       $options  OAuth2Client options object
	 * @param   JHttpTransport  $client   The HTTP client object
	 * @param   JInput          $input    The input object
	 *
	 * @since   1234
	 */
	public function __construct(JRegistry $options = null, JHttpTransport $client = null, JInput $input = null)
	{
		$this->options = isset($options) ? $options : new JRegistry;
		$this->client  = isset($client) ? $client : JHttpFactory::getAvailableDriver($this->options);
		$this->http = new JHttp($this->options, $this->client);
		$this->input = isset($input) ? $input : JFactory::getApplication()->input;
	}

	/**
	 * Get the access token or redict to the authentication URL.
	 *
	 * @return  string  The access token
	 *
	 * @since   1234
	 */
	public function auth()
	{
		if ($data['code'] = $this->input->get('code', false, 'raw'))
		{
			$data['grant_type'] = 'authorization_code';
			$data['redirect_uri'] = $this->getOption('redirecturi');
			$data['client_id'] = $this->getOption('clientid');
			$data['client_secret'] = $this->getOption('clientsecret');
			$response = $this->http->post($this->getOption('tokenurl'), $data);

			if ($response->code >= 200 && $response->code < 300)
			{

				if ($response->headers['Content-Type'] == 'application/json')
				{
					$token = array_merge(json_decode($response->body, true), array('created' => time()));
				}
				else
				{
					parse_str($response->body, $token);
					$token = array_merge($token, array('created' => time()));
				}

				$this->setToken($token);
				return $token;
			}
			else
			{
				throw new RuntimeException('Error code ' . $response->code . ' received requesting access token: ' . $response->body . '.');
			}
		}

		if ($this->getOption('sendheaders'))
		{
			JResponse::setHeader('Location', $this->createUrl(), true);
			JResponse::sendHeaders();
		}
		return false;
	}

	/**
	 * Verify if the client has been authenticated
	 *
	 * @return  bool  Is authenticated
	 *
	 * @since   1234
	 */
	public function isAuth()
	{
		$token = $this->getToken();
		return !empty($token);
	}

	/**
	 * Create the URL for authentication.
	 *
	 * @return  JHttpResponse  The HTTP response
	 *
	 * @since   1234
	 */
	public function createUrl()
	{
		if (!$this->getOption('authurl') || !$this->getOption('clientid'))
		{
			throw new InvalidArgumentException('Authorization URL and client_id are required');
		}

		$url = $this->getOption('authurl');
		if (strpos($url, '?'))
		{
			$url .= '&';
		}
		else
		{
			$url .= '?';
		}

		$url .= 'response_type=code';
		$url .= '&client_id=' . urlencode($this->getOption('clientid'));

		if ($this->getOption('redirecturi'))
		{
			$url .= '&redirect_uri=' . urlencode($this->getOption('redirecturi'));
		}

		if ($this->getOption('scope'))
		{
			$scope = is_array($this->getOption('scope')) ? implode(' ', $this->getOption('scope')) : $this->getOption('scope');
			$url .= '&scope=' . urlencode($scope);
		}

		if ($this->getOption('state'))
		{
			$url .= '&state=' . urlencode($this->getOption('state'));
		}

		if (is_array($this->getOption('requestparams')))
		{
			foreach ($this->getOption('requestparams') as $key => $value)
			{
				$url .= '&' . $key . '=' . urlencode($value);
			}
		}

		return $url;
	}

	/**
	 * Send a signed Oauth request.
	 *
	 * @param   string  $url      The URL forf the request.
	 * @param   mixed   $data     The data to include in the request
	 * @param   array   $headers  The headers to send with the request
	 * @param   string  $method   The method with which to send the request
	 * @param   int     $timeout  The timeout for the request
	 *
	 * @return  string  The URL.
	 *
	 * @since   1234
	 */
	public function query($url, $data = null, $headers = array(), $method = 'get', $timeout = null)
	{
		$token = $this->getToken();
		if (array_key_exists('expires_in', $token) && $token['created'] + $token['expires_in'] < time() + 20)
		{
			if (!$this->getOption('userefresh'))
			{
				return false;
			}
			$token = $this->refreshToken($token['refresh_token']);
		}

		if (!$this->getOption('authmethod') || $this->getOption('authmethod') == 'bearer')
		{
			$headers['Authorization'] = 'Bearer ' . $token['access_token'];
		}
		elseif ($this->getOption('authmethod') == 'get')
		{
			if (strpos($url, '?'))
			{
				$url .= '&';
			}
			else
			{
				$url .= '?';
			}
			$url .= $this->getOption('getparam') ? $this->getOption('getparam') : 'access_token';
			$url .= '=' . $token['access_token'];
		}

		$response = $this->client->request($method, new JURI($url), $data, $headers, $timeout);

		if ($response->code < 200 || $response->code >= 300)
		{
			throw new RuntimeException('Error code ' . $response->code . ' received requesting data: ' . $response->body . '.');
		}
		return $response;
	}

	/**
	 * Get an option from the JOauth2client instance.
	 *
	 * @param   string  $key  The name of the option to get
	 *
	 * @return  mixed  The option value
	 *
	 * @since   1234
	 */
	public function getOption($key)
	{
		return $this->options->get($key);
	}

	/**
	 * Set an option for the JOauth2client instance.
	 *
	 * @param   string  $key    The name of the option to set
	 * @param   mixed   $value  The option value to set
	 *
	 * @return  JOauth2client  This object for method chaining
	 *
	 * @since   1234
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);
		return $this;
	}

	/**
	 * Get the access token from the JOauth2client instance.
	 *
	 * @return  array  The access token
	 *
	 * @since   1234
	 */
	public function getToken()
	{
		return $this->getOption('accesstoken');
	}

	/**
	 * Set an option for the JOauth2client instance.
	 *
	 * @param   array  $value  The access token
	 *
	 * @return  JOauth2client  This object for method chaining
	 *
	 * @since   1234
	 */
	public function setToken($value)
	{
		if (!array_key_exists('expires_in', $value) && array_key_exists('expires', $value))
		{
			$value['expires_in'] = $value['expires'];
			unset($value['expires']);
		}
		$this->setOption('accesstoken', $value);
		return $this;
	}

	/**
	 * Refresh the access token instance.
	 *
	 * @param   string  $token  The refresh token
	 *
	 * @return  array  The new access token
	 *
	 * @since   1234
	 */
	public function refreshToken($token = null)
	{
		if (!$this->getOption('userefresh'))
		{
			throw new RuntimeException('Refresh token is not supported for this OAuth instance.');
		}

		if (!$token)
		{
			$token = $this->getToken();

			if (!array_key_exists('refresh_token', $token))
			{
				throw new RuntimeException('No refresh token is available.');
			}
			$token = $token['refresh_token'];
		}
		$data['grant_type'] = 'refresh_token';
		$data['refresh_token'] = $token;
		$data['client_id'] = $this->getOption('clientid');
		$data['client_secret'] = $this->getOption('clientsecret');
		$response = $this->http->post($this->getOption('tokenurl'), $data);

		if ($response->code >= 200 || $response->code < 300)
		{
			if ($response->headers['Content-Type'] == 'application/json')
			{
				$token = array_merge(json_decode($response->body, true), array('created' => time()));
			}
			else
			{
				parse_str($response->body, $token);
				$token = array_merge($token, array('created' => time()));
			}

			$this->setToken($token);
			return $token;
		}
		else
		{
			throw new Exception('Error code ' . $response->code . ' received refreshing token: ' . $response->body . '.');
		}
	}
}
