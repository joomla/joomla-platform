<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.adapterinstance');

/**
 * Abstract adapter for the installer.
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       12.2
 */
abstract class JInstallerAdapter extends JAdapterInstance
{
	/**
	 * The unique identifier for the extension (e.g. mod_login)
	 *
	 * @var    string
	 * @since  12.2
	 * */
	protected $element = null;

	/**
	 * Copy of the XML manifest file
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $manifest = null;

	/**
	 * Name of the extension
	 *
	 * @var    string
	 * @since  12.2
	 * */
	protected $name = null;

	/**
	 * Load language files
	 *
	 * @param   string  $extension  Name of the extension
	 * @param   string  $source     Location of the extension on the filesystem
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function doLoadLanguage($extension, $source)
	{
		$lang = JFactory::getLanguage();
		$lang->load($extension . '.sys', $source, null, false, false)
			|| $lang->load($extension . '.sys', JPATH_SITE, null, false, false)
			|| $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
			|| $lang->load($extension . '.sys', JPATH_SITE, $lang->getDefault(), false, false);
	}

	/**
	 * Generic install method for extensions.
	 * This is not a fully functioning method and needs to be extended.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function install()
	{
		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();

		// Set the extensions name
		$name = (string) $this->manifest->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->name = $name;

		// Get the component description
		$description = (string) $this->manifest->description;
		if ($description)
		{
			$this->parent->set('message', JText::_($description));
		}
		else
		{
			$this->parent->set('message', '');
		}
	}
}
