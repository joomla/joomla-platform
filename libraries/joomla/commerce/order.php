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
 * Commerce system order object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommerceOrder
{
	/**
	 * @var    JCommerceCart
	 * @since  12.1
	 */
	protected $cart;

	/**
	 * @var    JCommerceAddress
	 * @since  12.1
	 */
	protected $billingAddress;

	/**
	 * @var    SplObjectStorage
	 * @since  12.1
	 */
	protected $paymentMethods;

	/**
	 * @var    JCommerceAddress
	 * @since  12.1
	 */
	protected $shippingAddress;

	/**
	 * @var    JCommerceOrderState
	 * @since  12.1
	 */
	protected $state;

	/**
	 * @var    integer  Transaction mode: 1=live 0=test.
	 * @since  12.1
	 */
	public $mode = 1;

	/**
	 * @var    integer  Order id.
	 * @since  12.1
	 */
	public $orderId;

	/**
	 * @var    string  Order token.
	 * @since  12.1
	 */
	public $orderToken;

	/**
	 * @var    string  Order description.
	 * @since  12.1
	 */
	public $description;

	/**
	 * @var    string  Session id of the order session.
	 * @since  12.1
	 */
	public $sessionId;

	/**
	 * @var    string  Customer's IP address.
	 * @since  12.1
	 */
	public $customerIp;

	/**
	 * @var    string  Customer's user id.
	 * @since  12.1
	 */
	public $customerId;

	/**
	 * Constructor
	 *
	 * @param   JCommerceCart        $cart   The shopping cart for the order.
	 * @param   JCommerceOrderState  $state  The current order state object.
	 *
	 * @since   12.1
	 */
	public function __construct(JCommerceCart $cart, JCommerceOrderState $state = null)
	{
		// Set the cart object.
		$this->cart = $cart;

		// Wire up the order state.
		$this->state = isset($state) ? $state : new JCommerceOrderStateNew;

		$this->paymentMethods = new SplObjectStorage;
	}

	/**
	 * Method to add a payment object for processing the payment.
	 *
	 * @param   JPayment  $payment  The payment object.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 */
	public function addPaymentMethod(JPayment $payment)
	{
		$this->paymentMethods->attach($payment);

		return $this;
	}

	/**
	 * Method to get the order's billing address.
	 *
	 * @return  JCommerceAddress
	 *
	 * @since   12.1
	 */
	public function getBillingAddress()
	{
		return $this->billingAddress;
	}

	/**
	 * Method to get the payment object for the order.
	 *
	 * @return  JPayment
	 *
	 * @since   12.1
	 */
	public function getPaymentMethods()
	{
		return $this->paymentMethods;
	}

	/**
	 * Method to get the order's shipping address.
	 *
	 * @return  JCommerceAddress
	 *
	 * @since   12.1
	 */
	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}

	/**
	 * The object to use for payment.
	 *
	 * @param   JPayment  $payment  The payment object.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 */
	public function removePaymentMethod(JPayment $payment)
	{
		$this->paymentMethods->detatch($payment);

		return $this;
	}

	/**
	 * Set the address to use for billing.
	 *
	 * @param   JCommerceAddress  $address  The address object.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 */
	public function setBillingAddress(JCommerceAddress $address)
	{
		$this->billingAddress = $address;

		return $this;
	}

	/**
	 * Set the address to use for shipping.
	 *
	 * @param   JCommerceAddress  $address  The address object.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 */
	public function setShippingAddress(JCommerceAddress $address)
	{
		$this->shippingAddress = $address;

		return $this;
	}

	/**
	 * Method to cancel an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function cancel()
	{
		$state = $this->state->cancelOrder();

		$this->state = $state;

		return $this;
	}

	/**
	 * Method to complete an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function complete()
	{
		$state = $this->state->completeOrder();

		$this->state = $state;

		return $this;
	}

	/**
	 * Method to submit an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function submit()
	{
		$state = $this->state->submitOrder();

		$this->state = $state;

		return $this;
	}

	/**
	 * Method to pay for an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function pay()
	{
		$state = $this->state->payOrder();

		$this->state = $state;

		return $this;
	}

	/**
	 * Method to return an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function returnOrder()
	{
		$state = $this->state->returnOrder();

		$this->state = $state;

		return $this;
	}

	/**
	 * Method to ship an order.
	 *
	 * @return  JCommerceOrder
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  LogicException
	 */
	public function ship()
	{
		$state = $this->state->shipOrder();

		$this->state = $state;

		return $this;
	}
}
