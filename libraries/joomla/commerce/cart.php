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
	public $items = array();
	// Order currency code.
	public $currency = 'USD';
	// Order subtotal.
	public $subtotal = 0.0;
	// Order shipping total.
	public $shippingTotal = 0.0;
	// Order handling total.
	public $handlingTotal = 0.0;
	// Order duty total.
	public $dutyTotal = 0.0;
	// Order tax total.
	public $taxTotal = 0.0;
	// Order total.
	public $total = 0.0;
	// Number of store credits applied to the order.
	public $storeCredits = 0;
	// Store credit total applied to the order.
	public $storeCredit = 0.0;
	// List of applied promotional codes.
	public $promotionalCodes = array();
	// Single promotional code applied to the order.
	public $promotionalCode;
	// Total amount of discount from promotional code(s).
	public $promotionalDiscount = 0.0;
}