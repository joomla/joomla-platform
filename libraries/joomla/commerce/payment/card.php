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
 * Joomla Payment Card Interface.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
abstract class JCommercePaymentCard
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
	 * Method provides an implementation of the Luhn Algorithm. This algorithm is used to validate
	 * most payment cards.
	 *
	 * @param   integer  $number  Payment card number to validate.
	 *
	 * @return  boolean  True if the card number is valid.
	 *
	 * @since   12.1
	 * @link    http://en.wikipedia.org/wiki/Luhn_algorithm
	 * @throws  InvalidArgumentException
	 */
	public function validateCardNumber($number)
	{
		// Make sure only integers are used in the payment card number string.
		$number = preg_replace('/[^0-9]/', '', $number);

		// Iterate over each number in the string in reverse.
		$number = strrev($number);
		for ($sum = 0, $i = 0; $i < strlen($number); $i++)
		{
			$current = substr($number, $i, 1);

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
	 * @param   integer  $month   Payment card expiration month to validate.
	 * @param   integer  $year    Payment card expiration year to validate.
	 *
	 * @return  boolean  True if the expiration date is valid.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function validateExpirationDate($month, $year)
	{
		// Validate the payment card expiration month.
		if (!is_numeric($month) || ($month <= 0) || ($month >= 13))
		{
			throw new InvalidArgumentException('Invalid expiration month.');
		}

		// Validate the payment card expiration year.
		$currentYear = date('Y');
		if (!is_numeric($year) || ($year < $currentYear) || ($year > ($currentYear + 10)))
		{
			throw new InvalidArgumentException('Invalid expiration year.');
		}

		// Make sure expiration date has not passed.
		if (($year == $currentYear) && ($month < date('n')))
		{
			throw new InvalidArgumentException('Expiration data has passed.');
		}

		return true;
	}
}
