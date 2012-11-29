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
 * Commerce system cart object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommerceCart
{
	/**
	 * @var    array
	 * @since  12.1
	 */
	public $items = array();

	/**
	 * @var    string  Order currency code.
	 * @since  12.1
	 */
	public $currency = 'USD';

	/**
	 * @var    float  Order subtotal.
	 * @since  12.1
	 */
	public $subtotal = 0.0;

	/**
	 * @var    float  Order shipping total.
	 * @since  12.1
	 */
	public $shippingTotal = 0.0;

	/**
	 * @var    float  Order handling total.
	 * @since  12.1
	 */
	public $handlingTotal = 0.0;

	/**
	 * @var    float  Order duty total.
	 * @since  12.1
	 */
	public $dutyTotal = 0.0;

	/**
	 * @var    float  Order tax total.
	 * @since  12.1
	 */
	public $taxTotal = 0.0;

	/**
	 * @var    float  Order total.
	 * @since  12.1
	 */
	public $total = 0.0;

	/**
	 * @var    integer  Number of store credits applied to the order.
	 * @since  12.1
	 */
	public $storeCredits = 0;

	/**
	 * @var    float  Store credit total applied to the order.
	 * @since  12.1
	 */
	public $storeCredit = 0.0;

	/**
	 * @var    array  List of applied promotional codes.
	 * @since  12.1
	 */
	public $promotionalCodes = array();

	/**
	 * @var    string  Total amount of discount from promotional code(s).
	 * @since  12.1
	 */
	public $promotionalDiscount = 0.0;
}
