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
 * Application helper functions
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.1
 */
class JApplicationHelper
{
	/**
	 * Client information array
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $clients = null;

	/**
	 * Client information array
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $_clients = null;

	/**
	 * Return the name of the request component [main component]
	 *
	 * @param   string  $default  The default option
	 *
	 * @return  string  Option (e.g. com_something)
	 *
	 * @since   11.1
	 */
	public static function getComponentName($default = null)
	{
		static $option;

		if ($option)
		{
			return $option;
		}

		$option = strtolower(JRequest::getCmd('option'));

		if (empty($option))
		{
			$option = $default;
		}

		JRequest::setVar('option', $option);
		return $option;
	}

	/**
	 * Gets information on a specific client id.  This method will be useful in
	 * future versions when we start mapping applications in the database.
	 *
	 * This method will return a client information array if called
	 * with no arguments which can be used to add custom application information.
	 *
	 * @param   integer  $id      A client identifier
	 * @param   boolean  $byName  If True, find the client by its name
	 *
	 * @return  mixed  Object describing the client or false if not known
	 *
	 * @since   11.1
	 */
	public static function getClientInfo($id = null, $byName = false)
	{
		// Only create the array if it does not exist
		if (self::$_clients === null)
		{
			$obj = new stdClass;

			// Site Client
			$obj->id = 0;
			$obj->name = 'site';
			$obj->path = JPATH_SITE;
			self::$_clients[0] = clone $obj;

			// Administrator Client
			$obj->id = 1;
			$obj->name = 'administrator';
			$obj->path = JPATH_ADMINISTRATOR;
			self::$_clients[1] = clone $obj;

			// Installation Client
			$obj->id = 2;
			$obj->name = 'installation';
			$obj->path = JPATH_INSTALLATION;
			self::$_clients[2] = clone $obj;
		}

		// If no client id has been passed return the whole array
		if (is_null($id))
		{
			return self::$_clients;
		}

		// Are we looking for client information by id or by name?
		if (!$byName)
		{
			if (isset(self::$_clients[$id]))
			{
				return self::$_clients[$id];
			}
		}
		else
		{
			foreach (self::$_clients as $client)
			{
				if ($client->name == strtolower($id))
				{
					return $client;
				}
			}
		}

		return null;
	}

	/**
	 * Adds information for a client.
	 *
	 * @param   mixed  $client  A client identifier either an array or object
	 *
	 * @return  boolean  True if the information is added. False on error
	 *
	 * @since   11.1
	 */
	public static function addClientInfo($client)
	{
		if (is_array($client))
		{
			$client = (object) $client;
		}

		if (!is_object($client))
		{
			return false;
		}

		$info = self::getClientInfo();

		if (!isset($client->id))
		{
			$client->id = count($info);
		}

		self::$_clients[$client->id] = clone $client;

		return true;
	}

	/**
	 * Parse a XML install manifest file.
	 *
	 * XML Root tag should be 'install' except for languages which use meta file.
	 *
	 * @param   string  $path  Full path to XML file.
	 *
	 * @return  array  XML metadata.
	 *
	 * @since   11.1
	 * @deprecated  13.3
	 */
	public static function parseXMLInstallFile($path)
	{
		JLog::add('JApplicationHelper::parseXMLInstallFile is deprecated. Use JInstaller::parseXMLInstallFile instead.', JLog::WARNING, 'deprecated');
		return JInstaller::parseXMLInstallFile($path);
	}

	/**
	 * Parse a XML language meta file.
	 *
	 * XML Root tag  for languages which is meta file.
	 *
	 * @param   string  $path  Full path to XML file.
	 *
	 * @return  array  XML metadata.
	 */
	public static function parseXMLLangMetaFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = JFactory::getXML($path);

		if (!$xml)
		{
			return false;
		}

		/*
		 * Check for a valid XML root tag.
		 *
		 * Should be 'metafile'.
		 */
		if ($xml->getName() != 'metafile')
		{
			unset($xml);
			return false;
		}

		$data = array();

		$data['name'] = (string) $xml->name;
		$data['type'] = $xml->attributes()->type;

		$data['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : JText::_('JLIB_UNKNOWN');
		$data['author'] = ((string) $xml->author) ? (string) $xml->author : JText::_('JLIB_UNKNOWN');

		$data['copyright'] = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl'] = (string) $xml->authorUrl;
		$data['version'] = (string) $xml->version;
		$data['description'] = (string) $xml->description;
		$data['group'] = (string) $xml->group;

		return $data;
	}
}
