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
 * Joomla Order Payment Processor Interface for Indirect payment processing.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
interface JCommercePaymentProcessorIndirect extends JCommercePaymentProcessor
{
	/**
	 * Method to get a redirect url to a third party payment processor where the purchaser will authorise
	 * the transaction.  This method is used for indirect payment gateway interfaces.
	 *
	 * @param   string  $returnUrl  The URL to redirect the user for a completed transaction.
	 * @param   string  $cancelUrl  The URL to redirect the user for a cancelled transaction.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function getExternalRedirect($returnUrl, $cancelUrl);

	/**
	 * Method to load order information from the payment gateway.  This is primarily used for indirect
	 * payment gateway interfaces where payment information is gathered at a third party Web site.
	 *
	 * @param   string  $token  The order token to use for loading external order information from
	 *                          the payment gateway.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function loadExternalData($token);
}
