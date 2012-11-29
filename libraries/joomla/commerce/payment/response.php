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
 * Payment response object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommercePaymentResponse
{
	/**
	 * @const  integer  The transaction is approved.
	 * @since  12.1
	 */
	const APPROVED = 1;

	/**
	 * @const  integer  The transaction is declined.
	 * @since  12.1
	 */

	const DECLINED = 2;

	/**
	 * @const  integer  There was a failure processing the transaction.
	 * @since  12.1
	 */
	const FAILURE = 3;

	/**
	 * @var    integer  The gateway response code: approved, declined or failure.
	 * @since  12.1
	 */
	public $responseCode;

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
	 * @var    string  An extra message sent by the gateway about the transaction.
	 * @since  12.1
	 */
	public $message;

	/**
	 * @var    string  The transaction id from the gateway.
	 * @since  12.1
	 */
	public $transactionId;

	/**
	 * @var    string  The name of the gateway to which the transaction was sent.
	 * @since  12.1
	 */
	public $gateway;

	/**
	 * @var    boolean  True if the transaction was a test.
	 * @since  12.1
	 */
	public $testMode;

	/**
	 * @var    string  The error code from the gateway.
	 * @since  12.1
	 */
	public $errorCode;

	/**
	 * @var    string  The error message from the gateway.
	 * @since  12.1
	 */
	public $errorMessage;

	/**
	 * The response code can be any of the following values:
	 *
	 * A => Address only (no ZIP)
	 * B => Address only (no ZIP) : International
	 * C => None*
	 * D => Address and Postal Code : International
	 * E => Not Applicable*
	 * F => Address and Postal Code : UK Specific
	 * G => Not Applicable : Global
	 * I => Not Applicable : International
	 * N => None*
	 * P => Postal Code only (no Address) : International
	 * R => Not Applicable
	 * S => Not Applicable
	 * U => Not Applicable
	 * W => Whole ZIP (9 digit ZIP, no Address)
	 * X => Address and 9 digit ZIP
	 * Y => Address and 5 digit ZIP
	 * Z => 5 digit ZIP (no Address)
	 *
	 * @var    string  The address verification system value from the gateway.
	 * @see    http://en.wikipedia.org/wiki/Address_Verification_System
	 * @since  12.1
	 */
	public $avsCode;

	/**
	 * The response code can be any of the following values:
	 *
	 * M => Matched
	 * N => Not Matched
	 * P => Not Processed
	 * S => Service Not Supported
	 * U => Service Not Available
	 * X => No Response
	 *
	 * @var    string  The card security code verification value from the gateway.
	 * @see    http://en.wikipedia.org/wiki/Card_security_code
	 * @since  12.1
	 */
	public $cscCode;

	/**
	 * @var    array  The raw gateway response value array.
	 * @since  12.1
	 */
	protected $raw = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $raw  The raw gateway response as key => value pairs.
	 *
	 * @since   12.1
	 */
	public function __construct(array $raw = array())
	{
		// Set the raw response data.
		$this->raw = $raw;
	}

	/**
	 * Method to return the raw gateway response data.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getRaw()
	{
		return $this->raw;
	}

	/**
	 * Method to log the transaction.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function log()
	{
		// Build a log entry for an approved transaction.
		if ($this->responseCode == self::APPROVED)
		{
			// Create the new log entry for the transaction.
			$entry = new JLogEntry($this->transactionId, JLog::INFO, 'transaction_approved');
			$entry->amount = $this->amount;
			$entry->orderId = $this->orderId;
			$entry->customerId = $this->customerId;
			$entry->comment = $this->message;
			$entry->avsCode = $this->avsCode;
			$entry->cscCode = $this->cscCode;
			$entry->gateway = $this->gateway;
		}
		// Build a log entry for a declined transaction.
		elseif ($this->responseCode == self::DECLINED)
		{
			// Create the new log entry for the transaction.
			$entry = new JLogEntry($this->transactionId, JLog::NOTICE, 'transaction_declined');
			$entry->amount = $this->amount;
			$entry->orderId = $this->orderId;
			$entry->customerId = $this->customerId;
			$entry->comment = $this->errorMessage;
			$entry->avsCode = $this->avsCode;
			$entry->cscCode = $this->cscCode;
			$entry->gateway = $this->gateway;
			$entry->errorCode = $this->errorCode;
		}
		// Yes, we especially want to log errors for an error.
		else
		{
			// Create the new log entry for the transaction.
			$entry = new JLogEntry($this->transactionId, JLog::ERROR, 'transaction_error');
			$entry->amount = $this->amount;
			$entry->orderId = $this->orderId;
			$entry->customerId = $this->customerId;
			$entry->comment = $this->errorMessage;
			$entry->avsCode = $this->avsCode;
			$entry->cscCode = $this->cscCode;
			$entry->gateway = $this->gateway;
			$entry->errorCode = $this->errorCode;
		}

		// Log the transaction.
		JLog::add($entry);
	}
}
