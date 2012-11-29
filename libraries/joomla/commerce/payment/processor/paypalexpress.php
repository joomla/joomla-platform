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
 * PayPal Payments Express gateway for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommercePaymentProcessorPaypalexpress implements JCommercePaymentProcessorIndirect
{
	/**
	 * @const  string  URL of gateway to connect to for live transactions.
	 * @since  12.1
	 */
	const PROD_URL = 'https://api-3t.paypal.com/nvp';

	/**
	 * @const  string  URL of gateway to connect to for test transactions.
	 * @since  12.1
	 */
	const TEST_URL = 'https://api-3t.sandbox.paypal.com/nvp';

	/**
	 * @const  string  Base URL to redirect purchasers for live transactions.
	 * @since  12.1
	 */
	const PROD_REDIRECT_URL = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=';

	/**
	 * @const  string  Base URL to redirect purchasers for test transactions.
	 * @since  12.1
	 */
	const TEST_REDIRECT_URL = 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=';

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
		$base = dirname(__FILE__) . '/paypalexpress';

		// Add the field and rule paths for the form object.
		JForm::addFieldPath($base . '/fields');
		JForm::addRulePath($base . '/rules');

		// Instantiate the form object.
		$form = JForm::getInstance($this->name, $base . '/form.xml');

		return $form;
	}

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
	public function getExternalRedirect($returnUrl, $cancelUrl)
	{
		// If we are dealing with a test order set the test mode to true.
		if ($this->order->isTestOrder())
		{
			$this->options->set('test', 1);
		}

		// Get the PayPal API URL to post the setup request.
		$apiUrl = ($this->options->get('test', 1)) ? self::TEST_URL : self::PROD_URL;

		// Generate the PayPal setup request payload.
		$payload = $this->buildSetupRequest($returnUrl, $cancelUrl);

		// Send the PayPal setup request.
		try
		{
			$response = $this->http->post($apiUrl, $payload);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}

		// Verify a successful request.
		if ($response->code == 200)
		{
			// Convert the gateway returned string into a data array.
			$data = array();
			parse_str(urldecode($response->body), $data);

			// Verify the response code.
			if (!empty($data['ACK']) && ($data['ACK'] == 'Success') && ! empty($data['TOKEN']))
			{
				// Generate and return the redirect url.
				if ($this->options->get('test', 1))
				{
					return self::TEST_REDIRECT_URL . $data['TOKEN'];
				}
				else
				{
					return self::PROD_REDIRECT_URL . $data['TOKEN'];
				}
			}
			else
			{
				throw new Exception(JText::_('JLIB_COMMERCE_PAYPAL_RESPONSE_FAILURE'));
			}
		}
		else
		{
			throw new Exception(JText::_('JLIB_COMMERCE_PAYPAL_REQUEST_FAILURE'), $response->code);
		}
	}

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
	public function loadExternalData($token)
	{
		// If we are dealing with a test order set the test mode to true.
		if ($this->order->isTestOrder())
		{
			$this->options->set('test', 1);
		}

		// Get the PayPal API URL to post the request.
		$apiUrl = ($this->options->get('test', 1)) ? self::TEST_URL : self::PROD_URL;

		// Generate the PayPal setup request payload.
		$payload = $this->buildPurchaserRequest($token);

		// Send the PayPal request.
		try
		{
			$response = $this->http->post($apiUrl, $payload);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}

		// Verify a successful request.
		if ($response->code == 200)
		{
			// Convert the gateway returned string into a data array.
			$data = array();
			parse_str(urldecode($response->body), $data);

			// Verify the response code.
			if (!empty($data['ACK']) && ($data['ACK'] == 'Success'))
			{
				$this->order->set('billing.email', $data['EMAIL']);
				$this->order->set('billing.first_name', $data['FIRSTNAME']);
				$this->order->set('billing.last_name', $data['LASTNAME']);

				$this->order->set('billing.address', $data['SHIPTOSTREET']);
				$this->order->set('billing.city', $data['SHIPTOCITY']);
				$this->order->set('billing.region', $data['SHIPTOSTATE']);
				$this->order->set('billing.postal_code', $data['SHIPTOZIP']);
				$this->order->set('billing.country', $data['SHIPTOCOUNTRYCODE']);

				$this->order->set('payment.token', $data['PAYERID']);
				$this->order->set('payment.gateway_token', $data['TOKEN']);
			}
			else
			{
				throw new Exception(JText::_('JLIB_COMMERCE_PAYPAL_RESPONSE_FAILURE'));
			}
		}
		else
		{
			throw new Exception(JText::_('JLIB_COMMERCE_PAYPAL_REQUEST_FAILURE'), $response->code);
		}
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
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}

		// Get the PayPal API URL to post the purchase request.
		$apiUrl = ($this->options->get('test', 1)) ? self::TEST_URL : self::PROD_URL;

		// Generate the PayPal purchase request payload.
		$payload = $this->buildPaymentRequest();

		// Send the PayPal purchase request.
		$response = $this->http->post($apiUrl, $payload);
		if ($response && ($response->code == 200))
		{
			// Convert the gateway returned string into a data array.
			$data = array();
			parse_str(urldecode($response->body), $data);

			return $this->buildResponse($data);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to build purchase request payload for PayPal Express as key => value pairs.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	protected function buildPaymentRequest()
	{
		// Build the request payload array.
		$request = array(
			'METHOD' => 'DoExpressCheckoutPayment',
			'VERSION' => '3.0',
			'PWD' => $this->options->get('password'),
			'USER' => $this->options->get('user'),
			'SIGNATURE' => $this->options->get('signature'),
			'PAYMENTACTION' => 'Sale',
			'AMT' => $this->order->get('total'),
			'CURRENCYCODE' => $this->order->get('currency'),
			'INVNUM' => $this->order->get('order_id'),
			'TOKEN' => $this->order->get('payment.gateway_token'),
			'PAYERID' => $this->order->get('payment.token')
		);

		// If we have items in the cart let's itemize the cart for better PayPal reporting.
		if (count($this->order->get('cart')) > 0)
		{
			// First set the item total.
			$request['ITEMAMT'] = $this->order->get('total');

			// Itemize the cart.
			foreach ($this->order->get('cart', array()) as $k => $item)
			{
				$request['L_NAME' . $k] = $item->title;
				$request['L_AMT' . $k] = (float) $item->unit_price;
				$request['L_QTY' . $k] = (int) $item->quantity;
			}

			// If there is store credit to be applied, apply it.
			if ($this->order->get('store_credit'))
			{
				$request['L_NAME' . ($k + 1)] = 'Store Credit';
				$request['L_AMT' . ($k + 1)] = '-' . $this->order->get('store_credit');
			}
		}

		return $request;
	}

	/**
	 * Method to build purchaser request payload for PayPal Express as key => value pairs.
	 *
	 * @param   string  $token  The PayPal transaction token.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	protected function buildPurchaserRequest($token)
	{
		// Build the request payload array.
		$request = array(
			'METHOD' => 'GetExpressCheckoutDetails',
			'VERSION' => '3.0',
			'PWD' => $this->options->get('password'),
			'USER' => $this->options->get('user'),
			'SIGNATURE' => $this->options->get('signature'),
			'TOKEN' => $token
		);

		return $request;
	}

	/**
	 * Method to build setup request payload for PayPal Express as key => value pairs.
	 *
	 * @param   string  $returnUrl  The successful return URL.
	 * @param   string  $cancelUrl  The URL to use if the purchase was cancelled.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	protected function buildSetupRequest($returnUrl, $cancelUrl)
	{
		// Build the request payload array.
		$request = array(
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => '3.0',
			'PWD' => $this->options->get('password'),
			'USER' => $this->options->get('user'),
			'SIGNATURE' => $this->options->get('signature'),
			'PAYMENTACTION' => 'Sale',
			'AMT' => $this->order->get('total'),
			'CURRENCYCODE' => $this->order->get('currency'),
			'INVNUM' => $this->order->get('order_id'),
			'RETURNURL' => $returnUrl,
			'CANCELURL' => $cancelUrl
		);

		// If we have items in the cart let's itemize the cart for better PayPal reporting.
		if (count($this->order->get('cart')) > 0)
		{
			// First set the item total.
			$request['ITEMAMT'] = $this->order->get('total');

			// Itemize the cart.
			foreach ($this->order->get('cart', array()) as $k => $item)
			{
				$request['L_NAME' . $k] = $item->title;
				$request['L_AMT' . $k] = (float) $item->unit_price;
				$request['L_QTY' . $k] = (int) $item->quantity;
			}

			// If there is store credit to be applied, apply it.
			if ($this->order->get('store_credit'))
			{
				$request['L_NAME' . ($k + 1)] = 'Store Credit';
				$request['L_AMT' . ($k + 1)] = '-' . $this->order->get('store_credit');
			}
		}

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

		// Get transaction information from the gateway.
		$response->transactionId = isset($data['TRANSACTIONID']) ? $data['TRANSACTIONID'] : null;
		$response->amount = isset($data['AMT']) ? $data['AMT'] : null;
		$response->message = isset($data['L_FMFfilterNAME0']) ? $data['L_FMFfilterNAME0'] : null;

		if (! empty($data['ACK']) && (($data['ACK'] == 'Success') || ($data['ACK'] == 'SuccessWithWarning')))
		{
			$response->responseCode = JCommercePaymentResponse::APPROVED;
		}
		else
		{
			$response->responseCode = JCommercePaymentResponse::DECLINED;
		}

		// Add the error information if it exists.
		$response->errorCode = isset($data['L_ERRORCODE0']) ? $data['L_ERRORCODE0'] : null;
		$response->errorMessage = isset($data['L_LONGMESSAGE0']) ? $data['L_LONGMESSAGE0'] : null;

		// Add the verification system responses.
		$response->avsCode = isset($data['AVSCODE']) ? $data['AVSCODE'] : null;
		$response->cscCode = isset($data['CVV2MATCH']) ? $data['CVV2MATCH'] : null;

		return $response;
	}
}
