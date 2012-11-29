<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Commerce
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Commerce system address object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommerceAddress
{
	/**
	 * @var    string
	 * @since  12.1
	 */
	public $title;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $firstName;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $middleName;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $lastName;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $companyName;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $poBox;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $streetNumber;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $streetName;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $streetUnit;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $city;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $region;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $district;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $postalCode;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $country;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $email;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $phone;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $phone2;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $timeZone;

	/**
	 * @var    float
	 * @since  12.1
	 */
	public $longitude;

	/**
	 * @var    float
	 * @since  12.1
	 */
	public $latitude;
}
