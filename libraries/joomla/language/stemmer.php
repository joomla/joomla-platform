<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Stemmer base class.
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       12.1
 */
abstract class JLanguageStemmer
{
	/**
	 * An internal cache of stemmed tokens.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $cache = array();

	/**
	 * @var    array  JLanguageStemmer instances.
	 * @since  12.1
	 */
	protected static $instances = array();

	/**
	 * Method to get a stemmer, creating it if necessary.
	 *
	 * @param   string  $adapter  The type of stemmer to load.
	 *
	 * @return  JLanguageStemmer  A JLanguageStemmer instance.
	 *
	 * @since   12.1
	 * @throws  RuntimeException on invalid stemmer.
	 */
	public static function getInstance($adapter)
	{
		// Only create one stemmer for each adapter.
		if (isset(self::$instances[$adapter]))
		{
			return self::$instances[$adapter];
		}

		// Setup the adapter for the stemmer.
		$class = 'JLanguageStemmer' . ucfirst(trim($adapter));

		// Check if a stemmer exists for the adapter.
		if (!class_exists($class))
		{
			// Throw invalid adapter exception.
			throw new RuntimeException(JText::sprintf('JLIB_STEMMER_INVALID_STEMMER', $adapter));
		}

		self::$instances[$adapter] = new $class;

		return self::$instances[$adapter];
	}

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 *
	 * @return  string  The root token.
	 *
	 * @since   12.1
	 */
	abstract public function stem($token, $lang);
}
