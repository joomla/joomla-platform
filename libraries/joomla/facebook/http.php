<?php

/**
 * @package     Joomla.Platform
 * @subpackage  Facebook
 */

defined('JPATH_PLATFORM') or die();


/**
 * HTTP client class for connecting to a Facebook instance.
 *
 * @package     Joomla.Platform
 * @subpackage  Facebook
 */

class JFacebookHttp extends JHttp
{
	/**
	 * @const  integer  Use no authentication for HTTP connections.
	 */
	const AUTHENTICATION_NONE = 0;

	/**
	 * @const  integer  Use basic authentication for HTTP connections.
	 */
	const AUTHENTICATION_BASIC = 1;


	/**
	 * @const  integer  Use OAuth authentication for HTTP connections.
	 */
	const AUTHENTICATION_OAUTH = 2;

  
	/**
	 * Constructor.
	 *
	 * @param   JRegistry       &$options   Client options object.
	 * @param   JHttpTransport  $transport  The HTTP transport object.
	 */
	public function __construct(JRegistry &$options = null, JHttpTransport $transport = null)
  	{
	    // Call the JHttp constructor to setup the object.
	    parent::__construct($options, $transport);
	
	    // Make sure the user agent string is defined.
	    $this->options->def('userAgent', 'JFacebook');
	
	    // Set the default timeout to 120 seconds.
	    $this->options->def('timeout', 120);
	}
}
