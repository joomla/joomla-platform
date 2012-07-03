<?php
/**
 * @package     Joomla.Compat
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JsonSerializable interface. This file should only be loaded on PHP < 5.4
 * It allows us to implement it in classes without requiring PHP 5.4
 *
 * @package     Joomla.Platform
 * @since       12.2
 * @link        http://www.php.net/manual/en/jsonserializable.jsonserialize.php
 */
interface JsonSerializable
{
	/**
	 * Return data which should be serialized by json_encode().
	 *
	 * @return  mixed
	 *
	 * @since   12.2
	 */
	public function jsonSerialize();
}
