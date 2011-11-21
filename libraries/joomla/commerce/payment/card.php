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
 * Joomla Payment Card Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommercePaymentCard extends JCommercePayment
{
	/**
	 * @const  integer Visa
	 * @since  12.1
	 */
	const VISA = 1;

	/**
	 * @const  integer MasterCard
	 * @since  12.1
	 */
	const MASTERCARD = 2;

	/**
	 * @const  integer American Express
	 * @since  12.1
	 */
	const AMEX = 3;

	/**
	 * @const  integer Discover
	 * @since  12.1
	 */
	const DISCOVER = 4;

	/**
	 * @const  integer Diners Club
	 * @since  12.1
	 */
	const DINERS_CLUB = 5;

	/**
	 * @const  integer JCB
	 * @since  12.1
	 */
	const JCB = 6;

	/**
	 * @const  integer Australian BankCard
	 * @since  12.1
	 */
	const AU_BANKCARD = 7;

	/**
	 * @var    array  The pre-defined test credit card numbers available for test transactions.
	 * @since  12.1
	 */
	protected static $testCardNumbers = array('4111111111111111', '5431111111111111', '341111111111111', '6011601160116011');

	/**
	 * @var    integer
	 * @since  12.1
	 */
	public $expirationMonth = 0;

	/**
	 * @var    integer
	 * @since  12.1
	 */
	public $expirationYear = 0;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $name;

	/**
	 * @var    integer
	 * @since  12.1
	 */
	public $number = 0;

	/**
	 * @var    integer
	 * @since  12.1
	 */
	public $securityCode = 0;

	/**
	 * @var    integer
	 * @since  12.1
	 */
	public $type = 0;

	/**
	 * Method to dectect the payment card type based on number.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function detectType()
	{
		// Determine what credit card type the card number is.
		if (preg_match('/^4[0-9]{12}([0-9]{3})?$/', $this->number))
		{
			$this->type = self::VISA;
		}
		elseif (preg_match('/^5[1-5][0-9]{14}$/', $this->number))
		{
			$this->type = self::MASTERCARD;
		}
		elseif (preg_match('/^3[47][0-9]{13}$/', $this->number))
		{
			$this->type = self::AMEX;
		}
		elseif (preg_match('/^6011[0-9]{12}$/', $this->number))
		{
			$this->type = self::DISCOVER;
		}
		elseif (preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $this->number))
		{
			$this->type = self::DINERS_CLUB;
		}
		elseif (preg_match('/^(3[0-9]{4}|2131|1800)[0-9]{11}$/', $this->number))
		{
			$this->type = self::JCB;
		}
		elseif (preg_match('/^5610[0-9]{12}$/', $this->number))
		{
			$this->type = self::AU_BANKCARD;
		}
		// If the payment card type could not be detected throw an exception.
		else
		{
			throw new UnexpectedValueException('Unknown payment card type.');
		}
	}

	/**
	 * Method to determine whether or not the payment card is for testing purposes or not.
	 *
	 * @return  boolean  True if the card is a test card.
	 *
	 * @since   12.1
	 */
	public function isTest()
	{
		return (in_array($this->number, self::$testCardNumbers)) ? true : false;
	}

	/**
	 * Method to determine the validity of the payment card details.
	 *
	 * @return  boolean  True for a valid payment card.
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function isValid()
	{
		// Sanitize the payment card number.
		$this->number = (int) preg_replace('/[^0-9]/', '', $this->number);

		// Detect the payment card type.
		$this->detectType();

		// Validate the expiration date.
		$this->validateExpirationDate();

		// Validate the card number.
		$this->validateCardNumber();

		return true;
	}

	/**
	 * Method provides an implementation of the Luhn Algorithm. This algorithm is used to validate
	 * most payment cards.
	 *
	 * @return  boolean  True if the card number is valid.
	 *
	 * @since   12.1
	 * @link    http://en.wikipedia.org/wiki/Luhn_algorithm
	 * @throws  InvalidArgumentException
	 */
	public function validateCardNumber()
	{
		// Iterate over each number in the string in reverse.
		$this->number = strrev($this->number);
		for ($sum = 0, $i = 0; $i < strlen($this->number); $i++)
		{
			$current = substr($this->number, $i, 1);

			// Double every second digit.
			if ($i % 2 == 1)
			{
				$current *= 2;
			}

			// Add digits of 2-digit numbers together.
			if ($current > 9)
			{
				$first = $current % 10;
				$second = ($current - $first) / 10;
				$current = $first + $second;
			}
			$sum += $current;
		}

		// If the total has a remainder it is invalid.
		if ($sum % 10 != 0)
		{
			throw new InvalidArgumentException('Invalid payment card number.');
		}

		return true;
	}

	/**
	 * Method to validate an expiration date for a payment card.  The date must be in the future, but
	 * not more than 10 years in the future.
	 *
	 * @return  boolean  True if the expiration date is valid.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function validateExpirationDate()
	{
		// Validate the expiration month.
		if (!is_numeric($this->expirationMonth) || ($this->expirationMonth <= 0) || ($this->expirationMonth >= 13))
		{
			throw new InvalidArgumentException('Invalid expiration month.');
		}

		// Validate the expiration year.
		$currentYear = date('Y');
		if (!is_numeric($this->expirationYear) || ($this->expirationYear < $currentYear) || ($this->expirationYear > ($currentYear + 10)))
		{
			throw new InvalidArgumentException('Invalid expiration year.');
		}

		// Make sure expiration date has not passed.
		if (($this->expirationYear == $currentYear) && ($this->expirationMonth < date('n')))
		{
			throw new InvalidArgumentException('Expiration data has passed.');
		}

		return true;
	}
}
