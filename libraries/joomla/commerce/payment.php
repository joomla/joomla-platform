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
 * Commerce system payment object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommercePayment
{
	/**
	 * @var    JCommercePaymentMethod
	 * @since  12.1
	 */
	protected $method = array();

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $gatewayToken;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $token;

	/**
	 * @var    float  The total amount of the transaction.
	 * @since  12.1
	 */
	public $amount;

	/**
	 * @var    integer  The id of the order that was sent to the gateway.
	 * @since  12.1
	 */
	public $orderId;

	/**
	 * @var    integer  The id of the customer whos order was sent to the gateway.
	 * @since  12.1
	 */
	public $customerId;

	/**
	 * @var    boolean  True if the transaction was a test.
	 * @since  12.1
	 */
	public $testMode;

	/**
	 * This method dectects what type of credit card the card is based on the credit card number.
	 *
	 * @param   string  $number  Credit card number for which to detect type.
	 *
	 * @return  integer  The bank card type.
	 *
	 * @since   12.1
	 */
	public function detectCreditCardType($number)
	{
		// Make sure only integers are used in the credit card number string.
		$number = preg_replace('/[^0-9]/', '', $number);

		// Determine what credit card type the card number is.
		$return = 0;
		if (preg_match('/^4[0-9]{12}([0-9]{3})?$/', $number))
		{
			$return = self::VISA;
		}
		elseif (preg_match('/^5[1-5][0-9]{14}$/', $number))
		{
			$return = self::MASTERCARD;
		}
		elseif (preg_match('/^3[47][0-9]{13}$/', $number))
		{
			$return = self::AMEX;
		}
		elseif (preg_match('/^6011[0-9]{12}$/', $number))
		{
			$return = self::DISCOVER;
		}
		elseif (preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $number))
		{
			$return = self::DINERS_CLUB;
		}
		elseif (preg_match('/^(3[0-9]{4}|2131|1800)[0-9]{11}$/', $number))
		{
			$return = self::JCB;
		}
		elseif (preg_match('/^5610[0-9]{12}$/', $number))
		{
			$return = self::AU_BANKCARD;
		}

		return $return;
	}

	/**
	 * Constructor.
	 *
	 * @param   array  $raw  The raw gateway response as key => value pairs.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function __construct()
	{
	}

	/**
	 * Method to determine the validity of the payment card details for the order.
	 *
	 * @return  boolean  True for a valid payment card.
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function isCardValid()
	{
		// Sanitize the payment card number.
		$this->cardNumber = (int) preg_replace('/[^0-9]/', '', $this->cardNumber);

		// Detect the payment card type.
		$this->cardType = self::detectCreditCardType($this->cardNumber);

		// If the payment card type could not be detected throw an exception.
		if (!$this->cardType)
		{
			throw new InvalidArgumentException(JText::_('JX_COMMERCE_INVALID_CC_TYPE'));
		}

		// Validate the payment card details.
		return self::validateCreditCard($this->cardNumber, $this->cardExpirationMonth, $this->cardExpirationYear);
	}

	/**
	 * Method to determine whether or not the order is for testing purposes or not.  If a testing
	 * credit card number is used or the test mode flag is set this will return true.
	 *
	 * @return  boolean
	 *
	 * @since   12.1
	 */
	public function isTestOrder()
	{
		// If the transaction mode is set to test then this is a test order.
		if ($this->testMode)
		{
			return true;
		}

		// If we were given a test credit card number then this is a test order.
		$number = preg_replace('/[^0-9]/', '', $this->cardNumber);
		if (in_array($number, self::$testCardNumbers))
		{
			return true;
		}

		return false;
	}
}
