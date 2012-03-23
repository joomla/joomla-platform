<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Inspector for the JApplicationWeb class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @since       11.3
 */
class JApplicationWebInspector extends JApplicationWeb
{
	/**
	 * @var     boolean  True to mimic the headers already being sent.
	 * @since   11.3
	 */
	public static $headersSent = false;

	/**
	 * @var     boolean  True to mimic the connection being alive.
	 * @since   11.3
	 */
	public static $connectionAlive = true;

	/**
	 * @var     array  List of sent headers for inspection. array($string, $replace, $code).
	 * @since   11.3
	 */
	public $headers = array();

	/**
	 * @var     integer  The exit code if the application was closed otherwise null.
	 * @since   11.3
	 */
	public $closed;

	/**
	 * Allows public access to protected method.
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	public function checkConnectionAlive()
	{
		return self::$connectionAlive;
	}

	/**
	 * Allows public access to protected method.
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	public function checkHeadersSent()
	{
		return self::$headersSent;
	}

	/**
	 * Mimic exiting the application.
	 *
	 * @param   integer  $code  The exit code (optional; default is 0).
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function close($code = 0)
	{
		$this->closed = $code;
	}

	/**
	 * Allows public access to protected method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function doExecute()
	{
		$this->triggerEvent('JWebDoExecute');
	}

	/**
	 * Allows public access to protected method.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *                             replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function header($string, $replace = true, $code = null)
	{
		$this->headers[] = array($string, $replace, $code);
	}
}
