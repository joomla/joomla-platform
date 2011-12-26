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
 * Authorize.NET gateway for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommercePaymentProcessorAuthorizenet implements JCommercePaymentProcessor
{
	/**
	 * @const  string  URL of gateway to connect to for live transactions.
	 * @since  12.1
	 */
	const PROD_URL = 'https://secure.authorize.net/gateway/transact.dll';

	/**
	 * @const  string  URL of gateway to connect to for test transactions.
	 * @since  12.1
	 */
	const TEST_URL = 'https://certification.authorize.net/gateway/transact.dll';

	/**
	 * @var    JCommercePayment
	 * @since  12.1
	 */
	protected $payment;

	/**
	 * @var    JCommerceAddress
	 * @since  12.1
	 */
	protected $billing;

	/**
	 * @var    JRegistry
	 * @since  12.1
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param   JCommercePayment  $payment  The payment object.
	 * @param   JCommerceAddress  $billing  The billing address object.
	 * @param   JRegistry         $options  Optional options object.
	 *
	 * @since   12.1
	 */
	public function __construct(JCommercePayment $payment, JCommerceAddress $billing, JRegistry $options = null)
	{
		$this->payment = $payment;
		$this->billing = $billing;
		$this->options = isset($options) ? $options : new JRegistry;
	}

	/**
	 * Method to get a JForm object for the payment gateway.
	 *
	 * @return  JForm
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function getForm()
	{
		// Get the base path.
		$base = dirname(__FILE__) . '/authorizenet';

		// Add the field and rule paths for the form object.
		JForm::addFieldPath($base . '/fields');
		JForm::addRulePath($base . '/rules');

		// Instantiate the form object.
		$form = JForm::getInstance($this->name, $base . '/form.xml');

		return $form;
	}

	/**
	 * Method to process the order with the payment gateway.
	 *
	 * @return  JCommercePaymentResponse
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function process()
	{
		// If we are dealing with a test order set the test mode to true.
		if ($this->order->isTestOrder())
		{
			$this->options->set('test', 1);
		}

		// Validate the credit card.
		try
		{
			$this->order->validateCard();
		} catch (Exception $e)
		{
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}

		// Get the PayPal API URL to post the purchase request.
		$apiUrl = ($this->options->get('test', 1)) ? self::TEST_URL : self::PROD_URL;

		// Generate the Authorize.NET purchase request payload.
		$payload = $this->buildRequest();

		// Send the Authorize.NET purchase request.
		$response = $this->http->post($apiUrl, $payload);
		if ($response && ($response->code == 200))
		{
			// Convert the gateway returned string into a data array.
			$data = explode('|', $response->body);

			return $this->buildResponse($data);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to build purchase request payload for PayPal Pro as key => value pairs.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	protected function buildRequest()
	{

		// Build the request payload array.
		$request = array(
			'x_method' => 'CC',
			'x_version' => '3.1',
			'x_login' => $this->options->get('user'),
			'x_trans_key' => $this->options->get('password'),
			'x_delim_char' => '|',
			'x_delim_data' => 'TRUE',
			'x_relay_response' => 'FALSE',
			'x_test_request' => $this->options->get('test', 1) ? 'TRUE' : 'FALSE',
			'x_customer_ip' => $this->order->get('customer_ip'),
			'x_cust_id' => $this->order->get('customer_id'),
			'x_invoice_num' => $this->order->get('order_id'),
			'x_description' => $this->order->get('description'),
			'o_session_id' => $this->order->get('session_id'),
			'x_type' => $this->options->get('capture', 1) ? 'AUTH_CAPTURE' : 'AUTH_ONLY',

			'x_currency_code' => $this->order->get('currency'),
			'x_freight' => $this->order->get('shipping_total') + $this->order->get('handling_total'),
			'x_tax' => $this->order->get('tax_total'),
			'x_amount' => $this->order->get('total'),
			'x_card_num' => $this->order->get('payment.card_number'),
			'x_exp_date' => $this->order->get('payment.card_exp_month') . substr($this->order->get('payment.card_exp_year'), - 2),
			'x_card_code' => $this->order->get('payment.csc'),

			'x_first_name' => $this->order->get('billing.first_name'),
			'x_last_name' => $this->order->get('billing.last_name'),
			'x_company' => $this->order->get('billing.company'),
			'x_address' => $this->order->get('billing.address') . ' ' . $this->order->get('billing.address2'),
			'x_city' => $this->order->get('billing.city'),
			'x_state' => $this->order->get('billing.region'),
			'x_zip' => $this->order->get('billing.postal_code'),
			'x_country' => $this->order->get('billing.country'),
			'x_phone' => $this->order->get('billing.phone'),
			'x_email' => $this->order->get('billing.email'),

			'x_ship_to_first_name' => $this->order->get('shipping.first_name'),
			'x_ship_to_last_name' => $this->order->get('shipping.last_name'),
			'x_ship_to_address' => $this->order->get('shipping.address') . ' ' . $this->order->get('shipping.address2'),
			'x_ship_to_city' => $this->order->get('shipping.city'),
			'x_ship_to_state' => $this->order->get('shipping.region'),
			'x_ship_to_zip' => $this->order->get('shipping.postal_code'),
			'x_ship_to_country' => $this->order->get('shipping.country')
		);

		return $request;
	}

	/**
	 * Method to build the response object.
	 *
	 * @param   array  $data  The raw response data from the gateway as key => value pairs.
	 *
	 * @return  JCommercePaymentResponse
	 *
	 * @since   12.1
	 */
	protected function buildResponse(array $data)
	{
		// Log the raw gateway response just in case we need to do troubleshooting.
		JLog::add(json_encode(array('orderId' => $this->order->get('order_id'), 'txn' => $data)), JLog::INFO, 'transaction_raw');

		// Create a new gateway response object with the raw data.
		$response = new JCommercePaymentResponse($data);

		// Set the system generated values for the response object.
		$response->gateway = $this->name;
		$response->testMode = ($this->options->get('test', 1)) ? true : false;
		$response->orderId = $this->order->get('order_id');
		$response->customerId = $this->order->get('customer_id');

		// If the response code is empty set it to failure and return it.
		if (empty($data[0]))
		{
			$response->responseCode = JCommercePaymentResponse::FAILURE;
			return $response;
		}

		// Get transaction information from the gateway.
		$response->transactionId = isset($data[6]) ? $data[6] : null;
		$response->amount = isset($data[9]) ? $data[9] : null;
		$response->message = isset($data[8]) ? $data[8] : null;

		// Set the appropriate response code.
		switch ($data[0])
		{
			case 1:
				$response->responseCode = JCommercePaymentResponse::APPROVED;
				break;

			case 2:
				$response->responseCode = JCommercePaymentResponse::DECLINED;
				break;

			case 3:
			default:
				$response->responseCode = JCommercePaymentResponse::FAILURE;
				break;
		}

		// Add the error information if it exists.
		$response->errorCode = isset($data[2]) ? $data[2] : null;
		$response->errorMessage = isset($data[3]) ? $data[3] : null;

		// Add the verification system responses.
		$response->avsCode = isset($data[5]) ? $data[5] : null;
		$response->cscCode = isset($data[38]) ? $data[38] : null;

		return $response;
	}
}
