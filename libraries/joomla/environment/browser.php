<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Environment
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.log.log');

/**
 * Browser class, provides capability information about the current web client.
 *
 * Browser identification is performed by examining the HTTP_USER_AGENT
 * environment variable provided by the web server.
 *
 * This class has many influences from the lib/Browser.php code in
 * version 3 of Horde by Chuck Hagenbuch and Jon Parise.
 *
 * @package     Joomla.Platform
 * @subpackage  Environment
 * @since       11.1
 * @deprecated  This API may be changed in the near future and should not be considered stable
 */
class JBrowser extends JObject
{

	/**
	 * @var    integer  Major version number
	 * @since  11.1
	 */
	protected $_majorVersion = 0;

	/**
	 * @var    integer  Minor version number
	 * @since  11.1
	 */
	protected $_minorVersion = 0;

	/**
	 * @var    string  Browser name.
	 * @since  11.1
	 */
	protected $_browser = '';

	/**
	 * @var    string  Full user agent string.
	 * @since  11.1
	 */
	protected $_agent = '';

	/**
	 * @var    string  Lower-case user agent string
	 * @since  11.1
	 */
	protected $_lowerAgent = '';

	/**
	 * @var    string  HTTP_ACCEPT string.
	 * @since  11.1
	 */
	protected $_accept = '';

	/**
	 * @var    array  Parsed HTTP_ACCEPT string
	 * @since  11.1
	 */
	protected $_accept_parsed = array();

	/**
	 * @var    string  Platform the browser is running on
	 * @since  11.1
	 */
	protected $_platform = '';

	/**
	 * @var    array  Known robots.
	 * @since  11.1
	 */
	protected $_robots = array(
		/* The most common ones. */
		'Googlebot',
		'msnbot',
		'Slurp',
		'Yahoo',
		/* The rest alphabetically. */
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'Baiduspider',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
		'FAST-WebCrawler',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www.almaden.ibm.com/cs/crawler',
		'ZyBorg');

	/**
	 * @var    boolean  Is this a mobile browser?
	 * @since  11.1
	 */
	protected $_mobile = false;

	/**
	 * @var    array  Features.
	 * @since  11.1
	 * @deprecated 12.1 This variable will be dropped without replacement
	 */
	protected $_features = array(
		'html' => true,
		'wml' => false,
		'images' => true,
		'iframes' => false,
		'frames' => true,
		'tables' => true,
		'java' => true,
		'javascript' => true,
		'dom' => false,
		'utf' => false,
		'rte' => false,
		'homepage' => false,
		'accesskey' => false,
		'xmlhttpreq' => false,
		'xhtml+xml' => false,
		'mathml' => false,
		'svg' => false
	);

	/**
	 * @var    array  Quirks.
	 * @since  11.1
	 * @deprecated 12.1 This variable will be dropped without replacement
	 */
	protected $_quirks = array(
		'avoid_popup_windows' => false,
		'break_disposition_header' => false,
		'break_disposition_filename' => false,
		'broken_multipart_form' => false,
		'cache_same_url' => false,
		'cache_ssl_downloads' => false,
		'double_linebreak_textarea' => false,
		'empty_file_input_value' => false,
		'must_cache_forms' => false,
		'no_filename_spaces' => false,
		'no_hidden_overflow_tables' => false,
		'ow_gui_1.3' => false,
		'png_transparency' => false,
		'scrollbar_in_way' => false,
		'scroll_tds' => false,
		'windowed_controls' => false);

	/**
	 * List of viewable image MIME subtypes.
	 * This list of viewable images works for IE and Netscape/Mozilla.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_images = array('jpeg', 'gif', 'png', 'pjpeg', 'x-png', 'bmp');

	/**
	 * @var    array  JBrowser instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Create a browser instance (constructor).
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @since   11.1
	 */
	public function __construct($userAgent = null, $accept = null)
	{
		$this->match($userAgent, $accept);
	}

	/**
	 * Returns the global Browser object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @return JBrowser  The Browser object.
	 *
	 * @since  11.1
	 */
	static public function getInstance($userAgent = null, $accept = null)
	{
		$signature = serialize(array($userAgent, $accept));

		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new JBrowser($userAgent, $accept);
		}

		return self::$instances[$signature];
	}

	/**
	 * Identify which of two types is preferred
	 *
	 * @param   string  $a  The first item in the comparision
	 * @param   string  $b  The second item in the comparison
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function _sortMime($a, $b)
	{
		if ($a[1] > $b[1])
		{
			return -1;
		}
		elseif ($a[1] < $b[1])
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Parses the user agent string and inititializes the object with
	 * all the known features and quirks for the given browser.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function match($userAgent = null, $accept = null)
	{
		// Set our agent string.
		if (is_null($userAgent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$this->_agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		}
		else
		{
			$this->_agent = $userAgent;
		}
		$this->_lowerAgent = strtolower($this->_agent);

		// Set our accept string.
		if (is_null($accept))
		{
			if (isset($_SERVER['HTTP_ACCEPT']))
			{
				$this->_accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
			}
		}
		else
		{
			$this->_accept = strtolower($accept);
		}

		// Parse the HTTP Accept Header
		$accept_mime = explode(",", $this->_accept);
		for ($i = 0, $count = count($accept_mime); $i < $count; $i++)
		{
			$parts = explode(';q=', trim($accept_mime[$i]));
			if (count($parts) === 1)
			{
				$parts[1] = 1;
			}
			$accept_mime[$i] = $parts;
		}

		// Sort so the preferred value is the first
		usort($accept_mime, array(__CLASS__, '_sortMime'));

		$this->_accept_parsed = $accept_mime;

		// Check if browser accepts content type application/xhtml+xml. */* doesn't count ;)
		foreach ($this->_accept_parsed as $mime)
		{
			if (($mime[0] == 'application/xhtml+xml'))
			{
				$this->setFeature('xhtml+xml');
			}
		}

		// Check for a mathplayer plugin is installed, so we can use MathML on several browsers.
		if (strpos($this->_lowerAgent, 'mathplayer') !== false)
		{
			$this->setFeature('mathml');
		}

		// Check for UTF support.
		if (isset($_SERVER['HTTP_ACCEPT_CHARSET']))
		{
			$this->setFeature('utf', strpos(strtolower($_SERVER['HTTP_ACCEPT_CHARSET']), 'utf') !== false);
		}

		if (!empty($this->_agent))
		{
			$this->_setPlatform();

			if (strpos($this->_lowerAgent, 'mobileexplorer') !== false
				|| strpos($this->_lowerAgent, 'openwave') !== false
				|| strpos($this->_lowerAgent, 'opera mini') !== false
				|| strpos($this->_lowerAgent, 'opera mobi') !== false
				|| strpos($this->_lowerAgent, 'operamini') !== false)
			{
				$this->setFeature('frames', false);
				$this->setFeature('javascript', false);
				$this->setQuirk('avoid_popup_windows');
				$this->_mobile = true;
			}
			elseif (preg_match('|Opera[/ ]([0-9.]+)|', $this->_agent, $version))
			{
				$this->setBrowser('opera');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				$this->setFeature('javascript', true);
				$this->setQuirk('no_filename_spaces');

				if ($this->_majorVersion >= 7)
				{
					$this->setFeature('dom');
					$this->setFeature('iframes');
					$this->setFeature('accesskey');
					$this->setQuirk('double_linebreak_textarea');
				}
				/* Due to changes in Opera UA, we need to check Version/xx.yy,
				 * but only if version is > 9.80. See: http://dev.opera.com/articles/view/opera-ua-string-changes/ */
				if ($this->_majorVersion == 9 && $this->_minorVersion >= 80)
				{
					preg_match('|Version[/ ]([0-9.]+)|', $this->_agent, $version);
					list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				}
			}
			elseif (preg_match('|Chrome[/ ]([0-9.]+)|', $this->_agent, $version))
			{
				$this->setBrowser('chrome');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				$this->setFeature('javascript', true);
			}
			elseif (strpos($this->_lowerAgent, 'elaine/') !== false
				|| strpos($this->_lowerAgent, 'palmsource') !== false
				|| strpos($this->_lowerAgent, 'digital paths') !== false)
			{
				$this->setBrowser('palm');
				$this->setFeature('images', false);
				$this->setFeature('frames', false);
				$this->setFeature('javascript', false);
				$this->setQuirk('avoid_popup_windows');
				$this->_mobile = true;
			}
			elseif ((preg_match('|MSIE ([0-9.]+)|', $this->_agent, $version)) || (preg_match('|Internet Explorer/([0-9.]+)|', $this->_agent, $version)))
			{
				$this->setBrowser('msie');
				$this->setQuirk('cache_ssl_downloads');
				$this->setQuirk('cache_same_url');
				$this->setQuirk('break_disposition_filename');

				if (strpos($version[1], '.') !== false)
				{
					list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				}
				else
				{
					$this->_majorVersion = $version[1];
					$this->_minorVersion = 0;
				}

				/* IE (< 7) on Windows does not support alpha transparency in
			 		 * PNG images. */
				if (($this->_majorVersion < 7) && preg_match('/windows/i', $this->_agent))
				{
					$this->setQuirk('png_transparency');
				}

				/* Some Handhelds have their screen resolution in the
				 * user agent string, which we can use to look for
				 * mobile agents.
				 */
				if (preg_match('/; (120x160|240x280|240x320|320x320)\)/', $this->_agent))
				{
					$this->_mobile = true;
				}

				switch ($this->_majorVersion)
				{
					case 7:
						$this->setFeature('javascript', 1.4);
						$this->setFeature('dom');
						$this->setFeature('iframes');
						$this->setFeature('utf');
						$this->setFeature('rte');
						$this->setFeature('homepage');
						$this->setFeature('accesskey');
						$this->setFeature('xmlhttpreq');
						$this->setQuirk('scrollbar_in_way');
						break;

					case 6:
						$this->setFeature('javascript', 1.4);
						$this->setFeature('dom');
						$this->setFeature('iframes');
						$this->setFeature('utf');
						$this->setFeature('rte');
						$this->setFeature('homepage');
						$this->setFeature('accesskey');
						$this->setFeature('xmlhttpreq');
						$this->setQuirk('scrollbar_in_way');
						$this->setQuirk('broken_multipart_form');
						$this->setQuirk('windowed_controls');
						break;

					case 5:
						if ($this->getPlatform() == 'mac')
						{
							$this->setFeature('javascript', 1.2);
						}
						else
						{
							// MSIE 5 for Windows.
							$this->setFeature('javascript', 1.4);
							$this->setFeature('dom');
							$this->setFeature('xmlhttpreq');
							if ($this->_minorVersion >= 5)
							{
								$this->setFeature('rte');
								$this->setQuirk('windowed_controls');
							}
						}
						$this->setFeature('iframes');
						$this->setFeature('utf');
						$this->setFeature('homepage');
						$this->setFeature('accesskey');
						if ($this->_minorVersion == 5)
						{
							$this->setQuirk('break_disposition_header');
							$this->setQuirk('broken_multipart_form');
						}
						break;

					case 4:
						$this->setFeature('javascript', 1.2);
						$this->setFeature('accesskey');
						if ($this->_minorVersion > 0)
						{
							$this->setFeature('utf');
						}
						break;

					case 3:
						$this->setFeature('javascript', 1.5);
						$this->setQuirk('avoid_popup_windows');
						break;
				}
			}
			elseif (preg_match('|amaya/([0-9.]+)|', $this->_agent, $version))
			{
				$this->setBrowser('amaya');
				$this->_majorVersion = $version[1];
				if (isset($version[2]))
				{
					$this->_minorVersion = $version[2];
				}
				if ($this->_majorVersion > 1)
				{
					$this->setFeature('mathml');
					$this->setFeature('svg');
				}
				$this->setFeature('xhtml+xml');
			}
			elseif (preg_match('|W3C_Validator/([0-9.]+)|', $this->_agent, $version))
			{
				$this->setFeature('mathml');
				$this->setFeature('svg');
				$this->setFeature('xhtml+xml');
			}
			elseif (preg_match('|ANTFresco/([0-9]+)|', $this->_agent, $version))
			{
				$this->setBrowser('fresco');
				$this->setFeature('javascript', 1.5);
				$this->setQuirk('avoid_popup_windows');
			}
			elseif (strpos($this->_lowerAgent, 'avantgo') !== false)
			{
				$this->setBrowser('avantgo');
				$this->_mobile = true;
			}
			elseif (preg_match('|Konqueror/([0-9]+)|', $this->_agent, $version) || preg_match('|Safari/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version))
			{
				// Konqueror and Apple's Safari both use the KHTML
				// rendering engine.
				$this->setBrowser('konqueror');
				$this->setQuirk('empty_file_input_value');
				$this->setQuirk('no_hidden_overflow_tables');
				$this->_majorVersion = $version[1];
				if (isset($version[2]))
				{
					$this->_minorVersion = $version[2];
				}

				if (strpos($this->_agent, 'Safari') !== false && $this->_majorVersion >= 60)
				{
					// Safari.
					$this->setBrowser('safari');
					$this->setFeature('utf');
					$this->setFeature('javascript', 1.4);
					$this->setFeature('dom');
					$this->setFeature('iframes');
					if ($this->_majorVersion > 125 || ($this->_majorVersion == 125 && $this->_minorVersion >= 1))
					{
						$this->setFeature('accesskey');
						$this->setFeature('xmlhttpreq');
					}
					if ($this->_majorVersion > 522)
					{
						$this->setFeature('svg');
						$this->setFeature('xhtml+xml');
					}
					// Set browser version, not engine version
					preg_match('|Version[/ ]([0-9.]+)|', $this->_agent, $version);
					list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				}
				else
				{
					// Konqueror.
					$this->setFeature('javascript', 1.5);
					switch ($this->_majorVersion)
					{
						case 3:
							$this->setFeature('dom');
							$this->setFeature('iframes');
							$this->setFeature('xhtml+xml');
							break;
					}
				}
			}
			elseif (preg_match('|Mozilla/([0-9.]+)|', $this->_agent, $version))
			{
				$this->setBrowser('mozilla');
				$this->setQuirk('must_cache_forms');

				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				switch ($this->_majorVersion)
				{
					case 5:
						if ($this->getPlatform() == 'win')
						{
							$this->setQuirk('break_disposition_filename');
						}
						$this->setFeature('javascript', 1.4);
						$this->setFeature('dom');
						$this->setFeature('accesskey');
						$this->setFeature('xmlhttpreq');
						if (preg_match('|rv:(.*)\)|', $this->_agent, $revision))
						{
							if ($revision[1] >= 1)
							{
								$this->setFeature('iframes');
							}
							if ($revision[1] >= 1.3)
							{
								$this->setFeature('rte');
							}
							if ($revision[1] >= 1.5)
							{
								$this->setFeature('svg');
								$this->setFeature('mathml');
								$this->setFeature('xhtml+xml');
							}
						}
						break;

					case 4:
						$this->setFeature('javascript', 1.3);
						$this->setQuirk('buggy_compression');
						break;

					case 3:
					default:
						$this->setFeature('javascript', 1);
						$this->setQuirk('buggy_compression');
						break;
				}
			}
			elseif (preg_match('|Lynx/([0-9]+)|', $this->_agent, $version))
			{
				$this->setBrowser('lynx');
				$this->setFeature('images', false);
				$this->setFeature('frames', false);
				$this->setFeature('javascript', false);
				$this->setQuirk('avoid_popup_windows');
			}
			elseif (preg_match('|Links \(([0-9]+)|', $this->_agent, $version))
			{
				$this->setBrowser('links');
				$this->setFeature('images', false);
				$this->setFeature('frames', false);
				$this->setFeature('javascript', false);
				$this->setQuirk('avoid_popup_windows');
			}
			elseif (preg_match('|HotJava/([0-9]+)|', $this->_agent, $version))
			{
				$this->setBrowser('hotjava');
				$this->setFeature('javascript', false);
			}
			elseif (strpos($this->_agent, 'UP/') !== false || strpos($this->_agent, 'UP.B') !== false || strpos($this->_agent, 'UP.L') !== false)
			{
				$this->setBrowser('up');
				$this->setFeature('html', false);
				$this->setFeature('javascript', false);
				$this->setFeature('wml');

				if (strpos($this->_agent, 'GUI') !== false && strpos($this->_agent, 'UP.Link') !== false)
				{
					/* The device accepts Openwave GUI extensions for
					 * WML 1.3. Non-UP.Link gateways sometimes have
					 * problems, so exclude them. */
					$this->setQuirk('ow_gui_1.3');
				}
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'Xiino/') !== false)
			{
				$this->setBrowser('xiino');
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'Palmscape/') !== false)
			{
				$this->setBrowser('palmscape');
				$this->setFeature('javascript', false);
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'Nokia') !== false)
			{
				$this->setBrowser('nokia');
				$this->setFeature('html', false);
				$this->setFeature('wml');
				$this->setFeature('xhtml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'Ericsson') !== false)
			{
				$this->setBrowser('ericsson');
				$this->setFeature('html', false);
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_lowerAgent, 'wap') !== false)
			{
				$this->setBrowser('wap');
				$this->setFeature('html', false);
				$this->setFeature('javascript', false);
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_lowerAgent, 'docomo') !== false || strpos($this->_lowerAgent, 'portalmmm') !== false)
			{
				$this->setBrowser('imode');
				$this->setFeature('images', false);
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'BlackBerry') !== false)
			{
				$this->setBrowser('blackberry');
				$this->setFeature('html', false);
				$this->setFeature('javascript', false);
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_agent, 'MOT-') !== false)
			{
				$this->setBrowser('motorola');
				$this->setFeature('html', false);
				$this->setFeature('javascript', false);
				$this->setFeature('wml');
				$this->_mobile = true;
			}
			elseif (strpos($this->_lowerAgent, 'j-') !== false)
			{
				$this->setBrowser('mml');
				$this->_mobile = true;
			}
		}
	}

	/**
	 * Match the platform of the browser.
	 *
	 * This is a pretty simplistic implementation, but it's intended
	 * to let us tell what line breaks to send, so it's good enough
	 * for its purpose.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function _setPlatform()
	{
		if (strpos($this->_lowerAgent, 'wind') !== false)
		{
			$this->_platform = 'win';
		}
		elseif (strpos($this->_lowerAgent, 'mac') !== false)
		{
			$this->_platform = 'mac';
		}
		else
		{
			$this->_platform = 'unix';
		}
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 *
	 * @since   11.1
	 */
	public function getPlatform()
	{
		return $this->_platform;
	}

	/**
	 * Sets the current browser.
	 *
	 * @param   string  $browser  The browser to set as current.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setBrowser($browser)
	{
		$this->_browser = $browser;
	}

	/**
	 * Retrieve the current browser.
	 *
	 * @return  string  The current browser.
	 *
	 * @since   11.1
	 */
	public function getBrowser()
	{
		return $this->_browser;
	}

	/**
	 * Retrieve the current browser's major version.
	 *
	 * @return  integer  The current browser's major version
	 *
	 * @since   11.1.
	 */
	public function getMajor()
	{
		return $this->_majorVersion;
	}

	/**
	 * Retrieve the current browser's minor version.
	 *
	 * @return  integer  The current browser's minor version.
	 *
	 * @since   11.1
	 */
	public function getMinor()
	{
		return $this->_minorVersion;
	}

	/**
	 * Retrieve the current browser's version.
	 *
	 * @return  string  The current browser's version.
	 *
	 * @since   11.1
	 */
	public function getVersion()
	{
		return $this->_majorVersion . '.' . $this->_minorVersion;
	}

	/**
	 * Return the full browser agent string.
	 *
	 * @return  string  The browser agent string
	 *
	 * @since   11.1
	 */
	public function getAgentString()
	{
		return $this->_agent;
	}

	/**
	 * Returns the server protocol in use on the current server.
	 *
	 * @return  string  The HTTP server protocol version.
	 *
	 * @since   11.1
	 */
	public function getHTTPProtocol()
	{
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (($pos = strrpos($_SERVER['SERVER_PROTOCOL'], '/')))
			{
				return substr($_SERVER['SERVER_PROTOCOL'], $pos + 1);
			}
		}
		return null;
	}

	/**
	 * Set unique behavior for the current browser.
	 *
	 * @param   string  $quirk  The behavior to set.
	 * @param   string  $value  Special behavior parameter.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function setQuirk($quirk, $value = true)
	{
		JLog::add('JBrowser::setQuirk() is deprecated.', JLog::WARNING, 'deprecated');
		$this->_quirks[$quirk] = $value;
	}

	/**
	 * Check unique behavior for the current browser.
	 *
	 * @param   string  $quirk  The behavior to check.
	 *
	 * @return  boolean  Does the browser have the behavior set?
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function hasQuirk($quirk)
	{
		JLog::add('JBrowser::hasQuirk() is deprecated.', JLog::WARNING, 'deprecated');
		return !empty($this->_quirks[$quirk]);
	}

	/**
	 * Retrieve unique behavior for the current browser.
	 *
	 * @param   string  $quirk  The behavior to retrieve.
	 *
	 * @return  string  The value for the requested behavior.
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function getQuirk($quirk)
	{
		JLog::add('JBrowser::getQuirk() is deprecated.', JLog::WARNING, 'deprecated');
		return isset($this->_quirks[$quirk]) ? $this->_quirks[$quirk] : null;
	}

	/**
	 * Set capabilities for the current browser.
	 *
	 * @param   string  $feature  The capability to set.
	 * @param   string  $value    Special capability parameter.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function setFeature($feature, $value = true)
	{
		JLog::add('JBrowser::setFeature() is deprecated.', JLog::WARNING, 'deprecated');
		$this->_features[$feature] = $value;
	}

	/**
	 * Check the current browser capabilities.
	 *
	 * @param   string  $feature  The capability to check.
	 *
	 * @return  boolean  Does the browser have the capability set?
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function hasFeature($feature)
	{
		JLog::add('JBrowser::hasFeature() is deprecated.', JLog::WARNING, 'deprecated');
		return !empty($this->_features[$feature]);
	}

	/**
	 * Retrieve the current browser capability.
	 *
	 * @param   string  $feature  The capability to retrieve.
	 *
	 * @return  string  The value of the requested capability.
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function getFeature($feature)
	{
		JLog::add('JBrowser::getFeature() is deprecated.', JLog::WARNING, 'deprecated');
		return isset($this->_features[$feature]) ? $this->_features[$feature] : null;
	}

	/**
	 * Determines if a browser can display a given MIME type.
	 *
	 * Note that  image/jpeg and image/pjpeg *appear* to be the same
	 * entity, but Mozilla doesn't seem to want to accept the latter.
	 * For our purposes, we will treat them the same.
	 *
	 * @param   string  $mimetype  The MIME type to check.
	 *
	 * @return  boolean  True if the browser can display the MIME type.
	 *
	 * @since   11.1
	 */
	public function isViewable($mimetype)
	{
		$mimetype = strtolower($mimetype);
		list ($type, $subtype) = explode('/', $mimetype);

		if (!empty($this->_accept))
		{
			$wildcard_match = false;

			if (strpos($this->_accept, $mimetype) !== false)
			{
				return true;
			}

			if (strpos($this->_accept, '*/*') !== false)
			{
				$wildcard_match = true;
				if ($type != 'image')
				{
					return true;
				}
			}

			// Deal with Mozilla pjpeg/jpeg issue
			if ($this->isBrowser('mozilla') && ($mimetype == 'image/pjpeg') && (strpos($this->_accept, 'image/jpeg') !== false))
			{
				return true;
			}

			if (!$wildcard_match)
			{
				return false;
			}
		}

		if (!$this->hasFeature('images') || ($type != 'image'))
		{
			return false;
		}

		return (in_array($subtype, $this->_images));
	}

	/**
	 * Determine if the given browser is the same as the current.
	 *
	 * @param   string  $browser  The browser to check.
	 *
	 * @return  boolean  Is the given browser the same as the current?
	 *
	 * @since   11.1
	 */
	public function isBrowser($browser)
	{
		return ($this->_browser === $browser);
	}

	/**
	 * Determines if the browser is a robot or not.
	 *
	 * @return  boolean  True if browser is a known robot.
	 *
	 * @since   11.1
	 */
	public function isRobot()
	{
		foreach ($this->_robots as $robot)
		{
			if (strpos($this->_agent, $robot) !== false)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines if the browser is mobile version or not.
	 *
	 * @return boolean  True if browser is a known mobile version.
	 *
	 * @since   11.1
	 */
	public function isMobile()
	{
		return $this->_mobile;
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 *
	 * @since   11.1
	 */
	public function isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}
}
