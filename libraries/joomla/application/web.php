<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base class for a Joomla! Web application.
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.4
 */
class JApplicationWeb extends JApplicationBase
{
	/**
	 * @var    string  Character encoding string.
	 * @since  11.3
	 */
	public $charSet = 'utf-8';

	/**
	 * @var    string  Response mime type.
	 * @since  11.3
	 */
	public $mimeType = 'text/html';

	/**
	 * @var    JDate  The body modified date for response headers.
	 * @since  11.3
	 */
	public $modifiedDate;

	/**
	 * @var    JApplicationWebClient  The application client object.
	 * @since  11.3
	 */
	public $client;

	/**
	 * @var    JRegistry  The application configuration object.
	 * @since  11.3
	 */
	protected $config;

	/**
	 * @var    JDocument  The application document object.
	 * @since  11.3
	 */
	protected $document;

	/**
	 * @var    JLanguage  The application language object.
	 * @since  11.3
	 */
	protected $language;

	/**
	 * @var    JSession  The application session object.
	 * @since  11.3
	 */
	protected $session;

	/**
	 * @var    object  The application response object.
	 * @since  11.3
	 */
	protected $response;

	/**
	 * Class constructor.
	 *
	 * @param   mixed  $input   An optional argument to provide dependency injection for the application's
	 *                          input object.  If the argument is a JInput object that object will become
	 *                          the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config  An optional argument to provide dependency injection for the application's
	 *                          config object.  If the argument is a JRegistry object that object will become
	 *                          the application's config object, otherwise a default config object is created.
	 * @param   mixed  $client  An optional argument to provide dependency injection for the application's
	 *                          client object.  If the argument is a JApplicationWebClient object that object will become
	 *                          the application's client object, otherwise a default client object is created.
	 *
	 * @since   11.3
	 */
	public function __construct(JInput $input = null, JRegistry $config = null, JApplicationWebClient $client = null)
	{
		// If a input object is given use it.
		if ($input instanceof JInput)
		{
			$this->input = $input;
		}
		// Create the input based on the application logic.
		else
		{
			$this->input = new JInput;
		}

		// If a config object is given use it.
		if ($config instanceof JRegistry)
		{
			$this->config = $config;
		}
		// Instantiate a new configuration object.
		else
		{
			$this->config = new JRegistry;
		}

		// If a client object is given use it.
		if ($client instanceof JApplicationWebClient)
		{
			$this->client = $client;
		}
		// Instantiate a new web client object.
		else
		{
			$this->client = new JApplicationWebClient;
		}

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Setup the response object.
		$this->response = new stdClass;
		$this->response->cachable = false;
		$this->response->headers = array();
		$this->response->body = array();

		// Set the system URIs.
		$this->loadSystemUris();
	}

	/**
	 * Returns a reference to the global JApplicationWeb object, only creating it if it doesn't already exist.
	 *
	 * This method must be invoked as: $web = JApplicationWeb::getInstance();
	 *
	 * @param   string  $name  The name (optional) of the JApplicationWeb class to instantiate.
	 *
	 * @return  JApplicationWeb
	 *
	 * @since   11.3
	 * @deprecated  14.1  Use JApplicationBase::getInstance() instead.
	 */
	public static function getInstance($name = null)
	{
		JLog::add(sprintf('%s is deprecated, use JApplicationBase::getInstance() instead.', __METHOD__), JLog::WARNING, 'deprecated');

		return parent::getInstance($name);
	}

	/**
	 * Initialise the application.
	 *
	 * @param   mixed  $session     An optional argument to provide dependency injection for the application's
	 *                              session object.  If the argument is a JSession object that object will become
	 *                              the application's session object, if it is false then there will be no session
	 *                              object, and if it is null then the default session object will be created based
	 *                              on the application's loadSession() method.
	 * @param   mixed  $document    An optional argument to provide dependency injection for the application's
	 *                              document object.  If the argument is a JDocument object that object will become
	 *                              the application's document object, if it is false then there will be no document
	 *                              object, and if it is null then the default document object will be created based
	 *                              on the application's loadDocument() method.
	 * @param   mixed  $language    An optional argument to provide dependency injection for the application's
	 *                              language object.  If the argument is a JLanguage object that object will become
	 *                              the application's language object, if it is false then there will be no language
	 *                              object, and if it is null then the default language object will be created based
	 *                              on the application's loadLanguage() method.
	 * @param   mixed  $dispatcher  An optional argument to provide dependency injection for the application's
	 *                              event dispatcher.  If the argument is a JEventDispatcher object that object will become
	 *                              the application's event dispatcher, if it is null then the default event dispatcher
	 *                              will be created based on the application's loadDispatcher() method.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @deprecated  13.1
	 * @see     loadSession()
	 * @see     loadDocument()
	 * @see     loadLanguage()
	 * @see     loadDispatcher()
	 * @since   11.3
	 */
	public function initialise($session = null, $document = null, $language = null, $dispatcher = null)
	{
		// Create the session based on the application logic.
		if ($session !== false)
		{
			$this->loadSession($session);
		}

		// Create the document based on the application logic.
		if ($document !== false)
		{
			$this->loadDocument($document);
		}

		// Create the language based on the application logic.
		if ($language !== false)
		{
			$this->loadLanguage($language);
		}

		$this->loadDispatcher($dispatcher);

		return $this;
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function execute()
	{
		// Trigger the onBeforeExecute event.
		$this->triggerEvent('onBeforeExecute');

		// Perform application routines.
		$this->doExecute();

		// Trigger the onAfterExecute event.
		$this->triggerEvent('onAfterExecute');

		// If we have an application document object, render it.
		if ($this->document instanceof JDocument)
		{
			// Trigger the onBeforeRender event.
			$this->triggerEvent('onBeforeRender');

			// Render the application output.
			$this->render();

			// Trigger the onAfterRender event.
			$this->triggerEvent('onAfterRender');
		}

		// If gzip compression is enabled in configuration and the server is compliant, compress the output.
		if ($this->get('gzip') && !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler'))
		{
			$this->compress();
		}

		// Trigger the onBeforeRespond event.
		$this->triggerEvent('onBeforeRespond');

		// Send the application response.
		$this->respond();

		// Trigger the onAfterRespond event.
		$this->triggerEvent('onAfterRespond');
	}

	/**
	 * Method to run the Web application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of action that populates a JDocument object so that output
	 * can be rendered to the client.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   11.3
	 */
	protected function doExecute()
	{
		// Your application routines go here.
	}

	/**
	 * Rendering is the process of pushing the document buffers into the template
	 * placeholders, retrieving data from the document and pushing it into
	 * the application response buffer.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function render()
	{
		// Setup the document options.
		$options = array(
			'template' => $this->get('theme'),
			'file' => $this->get('themeFile', 'index.php'),
			'params' => $this->get('themeParams')
		);

		if ($this->get('themes.base'))
		{
			$options['directory'] = $this->get('themes.base');
		}
		// Fall back to constants.
		else
		{
			$options['directory'] = defined('JPATH_THEMES') ? JPATH_THEMES : (defined('JPATH_BASE') ? JPATH_BASE : __DIR__) . '/themes';
		}

		// Parse the document.
		$this->document->parse($options);

		// Render the document.
		$data = $this->document->render($this->get('cache_enabled'), $options);

		// Set the application output data.
		$this->setBody($data);
	}

	/**
	 * Checks the accept encoding of the browser and compresses the data before
	 * sending it to the client if possible.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function compress()
	{
		// Supported compression encodings.
		$supported = array(
			'x-gzip' => 'gz',
			'gzip' => 'gz',
			'deflate' => 'deflate'
		);

		// Get the supported encoding.
		$encodings = array_intersect($this->client->encodings, array_keys($supported));

		// If no supported encoding is detected do nothing and return.
		if (empty($encodings))
		{
			return;
		}

		// Verify that headers have not yet been sent, and that our connection is still alive.
		if ($this->checkHeadersSent() || !$this->checkConnectionAlive())
		{
			return;
		}

		// Iterate through the encodings and attempt to compress the data using any found supported encodings.
		foreach ($encodings as $encoding)
		{
			if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate'))
			{
				// Verify that the server supports gzip compression before we attempt to gzip encode the data.
				// @codeCoverageIgnoreStart
				if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				// Attempt to gzip encode the data with an optimal level 4.
				$data = $this->getBody();
				$gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

				// If there was a problem encoding the data just try the next encoding scheme.
				// @codeCoverageIgnoreStart
				if ($gzdata === false)
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				// Set the encoding headers.
				$this->setHeader('Content-Encoding', $encoding);
				$this->setHeader('X-Content-Encoded-By', 'Joomla');

				// Replace the output with the encoded data.
				$this->setBody($gzdata);

				// Compression complete, let's break out of the loop.
				break;
			}
		}
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function respond()
	{
		// Send the content-type header.
		$this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);

		// If the response is set to uncachable, we need to set some appropriate headers so browsers don't cache the response.
		if (!$this->response->cachable)
		{
			// Expires in the past.
			$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);

			// Always modified.
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);

			// HTTP 1.0
			$this->setHeader('Pragma', 'no-cache');
		}
		else
		{
			// Expires.
			$this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');

			// Last modified.
			if ($this->modifiedDate instanceof JDate)
			{
				$this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s'));
			}
		}

		$this->sendHeaders();

		echo $this->getBody();
	}

	/**
	 * Redirect to another URL.
	 *
	 * If the headers have not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * or "303 See Other" code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param   string   $url    The URL to redirect to. Can only be http/https URL
	 * @param   boolean  $moved  True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function redirect($url, $moved = false)
	{
		// Import library dependencies.
		jimport('phputf8.utils.ascii');

		// Check for relative internal links.
		if (preg_match('#^index\.php#', $url))
		{
			$url = $this->get('uri.base.full') . $url;
		}

		// Perform a basic sanity check to make sure we don't have any CRLF garbage.
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		/*
		 * Here we need to check and see if the URL is relative or absolute.  Essentially, do we need to
		 * prepend the URL with our base URL for a proper redirect.  The rudimentary way we are looking
		 * at this is to simply check whether or not the URL string has a valid scheme or not.
		 */
		if (!preg_match('#^[a-z]+\://#i', $url))
		{
			// Get a JURI instance for the requested URI.
			$uri = JURI::getInstance($this->get('uri.request'));

			// Get a base URL to prepend from the requested URI.
			$prefix = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

			// We just need the prefix since we have a path relative to the root.
			if ($url[0] == '/')
			{
				$url = $prefix . $url;
			}
			// It's relative to where we are now, so lets add that.
			else
			{
				$parts = explode('/', $uri->toString(array('path')));
				array_pop($parts);
				$path = implode('/', $parts) . '/';
				$url = $prefix . $path . $url;
			}
		}

		// If the headers have already been sent we need to send the redirect statement via JavaScript.
		if ($this->checkHeadersSent())
		{
			echo "<script>document.location.href='$url';</script>\n";
		}
		else
		{
			// We have to use a JavaScript redirect here because MSIE doesn't play nice with utf-8 URLs.
			if (($this->client->engine == JApplicationWebClient::TRIDENT) && !utf8_is_ascii($url))
			{
				$html = '<html><head>';
				$html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charSet . '" />';
				$html .= '<script>document.location.href=\'' . $url . '\';</script>';
				$html .= '</head><body></body></html>';

				echo $html;
			}
			else
			{
				// All other cases use the more efficient HTTP header for redirection.
				$this->header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
				$this->header('Location: ' . $url);
				$this->header('Content-Type: text/html; charset=' . $this->charSet);
			}
		}

		// Close the application after the redirect.
		$this->close();
	}

	/**
	 * Load an object or array into the application configuration object.
	 *
	 * @param   mixed  $data  Either an array or object to be loaded into the configuration object.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function loadConfiguration($data)
	{
		// Load the data into the configuration object.
		if (is_array($data))
		{
			$this->config->loadArray($data);
		}
		elseif (is_object($data))
		{
			$this->config->loadObject($data);
		}

		return $this;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $key      The name of the property.
	 * @param   mixed   $default  The default value (optional) if none is set.
	 *
	 * @return  mixed   The value of the configuration.
	 *
	 * @since   11.3
	 */
	public function get($key, $default = null)
	{
		return $this->config->get($key, $default);
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $key    The name of the property.
	 * @param   mixed   $value  The value of the property to set (optional).
	 *
	 * @return  mixed   Previous value of the property
	 *
	 * @since   11.3
	 */
	public function set($key, $value = null)
	{
		$previous = $this->config->get($key);
		$this->config->set($key, $value);

		return $previous;
	}

	/**
	 * Set/get cachable state for the response.  If $allow is set, sets the cachable state of the
	 * response.  Always returns the current state.
	 *
	 * @param   boolean  $allow  True to allow browser caching.
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	public function allowCache($allow = null)
	{
		if ($allow !== null)
		{
			$this->response->cachable = (bool) $allow;
		}

		return $this->response->cachable;
	}

	/**
	 * Method to set a response header.  If the replace flag is set then all headers
	 * with the given name will be replaced by the new one.  The headers are stored
	 * in an internal array to be sent when the site is sent to the browser.
	 *
	 * @param   string   $name     The name of the header to set.
	 * @param   string   $value    The value of the header to set.
	 * @param   boolean  $replace  True to replace any headers with the same name.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function setHeader($name, $value, $replace = false)
	{
		// Sanitize the input values.
		$name = (string) $name;
		$value = (string) $value;

		// If the replace flag is set, unset all known headers with the given name.
		if ($replace)
		{
			foreach ($this->response->headers as $key => $header)
			{
				if ($name == $header['name'])
				{
					unset($this->response->headers[$key]);
				}
			}

			// Clean up the array as unsetting nested arrays leaves some junk.
			$this->response->headers = array_values($this->response->headers);
		}

		// Add the header to the internal array.
		$this->response->headers[] = array('name' => $name, 'value' => $value);

		return $this;
	}

	/**
	 * Method to get the array of response headers to be sent when the response is sent
	 * to the client.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getHeaders()
	{
		return $this->response->headers;
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function clearHeaders()
	{
		$this->response->headers = array();

		return $this;
	}

	/**
	 * Send the response headers.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function sendHeaders()
	{
		if (!$this->checkHeadersSent())
		{
			foreach ($this->response->headers as $header)
			{
				if ('status' == strtolower($header['name']))
				{
					// 'status' headers indicate an HTTP status, and need to be handled slightly differently
					$this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
				}
				else
				{
					$this->header($header['name'] . ': ' . $header['value']);
				}
			}
		}

		return $this;
	}

	/**
	 * Set body content.  If body content already defined, this will replace it.
	 *
	 * @param   string  $content  The content to set as the response body.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function setBody($content)
	{
		$this->response->body = array((string) $content);

		return $this;
	}

	/**
	 * Prepend content to the body content
	 *
	 * @param   string  $content  The content to prepend to the response body.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function prependBody($content)
	{
		array_unshift($this->response->body, (string) $content);

		return $this;
	}

	/**
	 * Append content to the body content
	 *
	 * @param   string  $content  The content to append to the response body.
	 *
	 * @return  JApplicationWeb  Instance of $this to allow chaining.
	 *
	 * @since   11.3
	 */
	public function appendBody($content)
	{
		array_push($this->response->body, (string) $content);

		return $this;
	}

	/**
	 * Return the body content
	 *
	 * @param   boolean  $asArray  True to return the body as an array of strings.
	 *
	 * @return  mixed  The response body either as an array or concatenated string.
	 *
	 * @since   11.3
	 */
	public function getBody($asArray = false)
	{
		return $asArray ? $this->response->body : implode((array) $this->response->body);
	}

	/**
	 * Method to get the application document object.
	 *
	 * @return  JDocument  The document object
	 *
	 * @since   11.3
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * Method to get the application language object.
	 *
	 * @return  JLanguage  The language object
	 *
	 * @since   11.3
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Method to get the application session object.
	 *
	 * @return  JSession  The session object
	 *
	 * @since   11.3
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Method to check the current client connnection status to ensure that it is alive.  We are
	 * wrapping this to isolate the connection_status() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the connection is valid and normal.
	 *
	 * @codeCoverageIgnore
	 * @see     connection_status()
	 * @since   11.3
	 */
	protected function checkConnectionAlive()
	{
		return (connection_status() === CONNECTION_NORMAL);
	}

	/**
	 * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
	 * headers_sent() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the headers have already been sent.
	 *
	 * @codeCoverageIgnore
	 * @see     headers_sent()
	 * @since   11.3
	 */
	protected function checkHeadersSent()
	{
		return headers_sent();
	}

	/**
	 * Method to detect the requested URI from server environment variables.
	 *
	 * @return  string  The requested URI
	 *
	 * @since   11.3
	 */
	protected function detectRequestUri()
	{
		$uri = '';

		// First we need to detect the URI scheme.
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$scheme = 'https://';
		}
		else
		{
			$scheme = 'http://';
		}

		/*
		 * There are some differences in the way that Apache and IIS populate server environment variables.  To
		 * properly detect the requested URI we need to adjust our algorithm based on whether or not we are getting
		 * information from Apache or IIS.
		 */

		// If PHP_SELF and REQUEST_URI are both populated then we will assume "Apache Mode".
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// The URI is built from the HTTP_HOST and REQUEST_URI environment variables in an Apache environment.
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		// If not in "Apache Mode" we will assume that we are in an IIS environment and proceed.
		else
		{
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the QUERY_STRING variable exists append it to the URI string.
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return trim($uri);
	}

	/**
	 * Method to load a PHP configuration class file based on convention and return the instantiated data object.  You
	 * will extend this method in child classes to provide configuration data from whatever data source is relevant
	 * for your specific application.
	 *
	 * @param   string  $file   The path and filename of the configuration file. If not provided, configuration.php
	 *                          in JPATH_BASE will be used.
	 * @param   string  $class  The class name to instantiate.
	 *
	 * @return  mixed   Either an array or object to be loaded into the configuration object.
	 *
	 * @since   11.3
	 */
	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		// Instantiate variables.
		$config = array();

		if (empty($file) && defined('JPATH_BASE'))
		{
			$file = JPATH_BASE . '/configuration.php';

			// Applications can choose not to have any configuration data
			// by not implementing this method and not having a config file.
			if (!file_exists($file))
			{
				$file = '';
			}
		}

		if (!empty($file))
		{
			JLoader::register($class, $file);

			if (class_exists($class))
			{
				$config = new $class;
			}
			else
			{
				throw new RuntimeException('Configuration class does not exist.');
			}
		}

		return $config;
	}

	/**
	 * Method to send a header to the client.  We are wrapping this to isolate the header() function
	 * from our code base for testing reasons.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *                             replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @see     header()
	 * @since   11.3
	 */
	protected function header($string, $replace = true, $code = null)
	{
		header($string, $replace, $code);
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 *
	 * @since   12.2
	 */
	public function isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}

	/**
	 * Allows the application to load a custom or default document.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a document,
	 * if required, based on more specific needs.
	 *
	 * @param   JDocument  $document  An optional document object. If omitted, the factory document is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   11.3
	 */
	public function loadDocument(JDocument $document = null)
	{
		$this->document = ($document === null) ? JFactory::getDocument() : $document;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default language.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a language,
	 * if required, based on more specific needs.
	 *
	 * @param   JLanguage  $language  An optional language object. If omitted, the factory language is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   11.3
	 */
	public function loadLanguage(JLanguage $language = null)
	{
		$this->language = ($language === null) ? JFactory::getLanguage() : $language;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default session.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a session,
	 * if required, based on more specific needs.
	 *
	 * @param   JSession  $session  An optional session object. If omitted, the session is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   11.3
	 */
	public function loadSession(JSession $session = null)
	{
		if ($session !== null)
		{
			$this->session = $session;

			return $this;
		}

		// Generate a session name.
		$name = md5($this->get('secret') . $this->get('session_name', get_class($this)));

		// Calculate the session lifetime.
		$lifetime = (($this->get('sess_lifetime')) ? $this->get('sess_lifetime') * 60 : 900);

		// Get the session handler from the configuration.
		$handler = $this->get('sess_handler', 'none');

		// Initialize the options for JSession.
		$options = array(
			'name' => $name,
			'expire' => $lifetime,
			'force_ssl' => $this->get('force_ssl')
		);

		$this->registerEvent('onAfterSessionStart', array($this, 'afterSessionStart'));

		// Instantiate the session object.
		$session = JSession::getInstance($handler, $options);
		$session->initialise($this->input, $this->dispatcher);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}
		else
		{
			$session->start();
		}

		// Set the session object.
		$this->session = $session;

		return $this;
	}

	/**
	 * After the session has been started we need to populate it with some default values.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function afterSessionStart()
	{
		$session = JFactory::getSession();

		if ($session->isNew())
		{
			$session->set('registry', new JRegistry('session'));
			$session->set('user', new JUser);
		}
	}

	/**
	 * Method to load the system URI strings for the application.
	 *
	 * @param   string  $requestUri  An optional request URI to use instead of detecting one from the
	 *                               server environment variables.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function loadSystemUris($requestUri = null)
	{
		// Set the request URI.
		// @codeCoverageIgnoreStart
		if (!empty($requestUri))
		{
			$this->set('uri.request', $requestUri);
		}
		else
		{
			$this->set('uri.request', $this->detectRequestUri());
		}
		// @codeCoverageIgnoreEnd

		// Check to see if an explicit base URI has been set.
		$siteUri = trim($this->get('site_uri'));

		if ($siteUri != '')
		{
			$uri = JUri::getInstance($siteUri);
		}
		// No explicit base URI was set so we need to detect it.
		else
		{
			// Start with the requested URI.
			$uri = JUri::getInstance($this->get('uri.request'));

			// If we are working from a CGI SAPI with the 'cgi.fix_pathinfo' directive disabled we use PHP_SELF.
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				// We aren't expecting PATH_INFO within PHP_SELF so this should work.
				$uri->setPath(rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));
			}
			// Pretty much everything else should be handled with SCRIPT_NAME.
			else
			{
				$uri->setPath(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
			}

			// Clear the unused parts of the requested URI.
			$uri->setQuery(null);
			$uri->setFragment(null);
		}

		// Get the host and path from the URI.
		$host = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$path = rtrim($uri->toString(array('path')), '/\\');

		// Check if the path includes "index.php".
		if (strpos($path, 'index.php') !== false)
		{
			// Remove the index.php portion of the path.
			$path = substr_replace($path, '', strpos($path, 'index.php'), 9);
			$path = rtrim($path, '/\\');
		}

		// Set the base URI both as just a path and as the full URI.
		$this->set('uri.base.full', $host . $path . '/');
		$this->set('uri.base.host', $host);
		$this->set('uri.base.path', $path . '/');

		// Set the extended (non-base) part of the request URI as the route.
		$this->set('uri.route', substr_replace($this->get('uri.request'), '', 0, strlen($this->get('uri.base.full'))));

		// Get an explicitly set media URI is present.
		$mediaURI = trim($this->get('media_uri'));

		if ($mediaURI)
		{
			if (strpos($mediaURI, '://') !== false)
			{
				$this->set('uri.media.full', $mediaURI);
				$this->set('uri.media.path', $mediaURI);
			}
			else
			{
				// Normalise slashes.
				$mediaURI = trim($mediaURI, '/\\');
				$mediaURI = !empty($mediaURI) ? '/' . $mediaURI . '/' : '/';
				$this->set('uri.media.full', $this->get('uri.base.host') . $mediaURI);
				$this->set('uri.media.path', $mediaURI);
			}
		}
		// No explicit media URI was set, build it dynamically from the base uri.
		else
		{
			$this->set('uri.media.full', $this->get('uri.base.full') . 'media/');
			$this->set('uri.media.path', $this->get('uri.base.path') . 'media/');
		}
	}
}
