<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Credentials base class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.3
 */
class JOAuth1Credentials
{
	/**
	 * @var    integer  Indicates temporary credentials.  These are ready to be authorised.
	 * @since  12.3
	 */
	const TEMPORARY = 0;

	/**
	 * @var    integer  Indicates authorised temporary credentials.  These are ready to be converted to token credentials.
	 * @since  12.3
	 */
	const AUTHORISED = 1;

	/**
	 * @var    integer  Indicates token credentials.  These are ready to be used for accessing protected resources.
	 * @since  12.3
	 */
	const TOKEN = 2;

	/**
	 * @var    JOAuth1TableCredentials  Connector object for table class.
	 * @since  12.3
	 */
	private $_table;

	/**
	 * @var    JOAuth1CredentialsState  The current credential state.
	 * @since  12.3
	 */
	private $_state;

	/**
	 * Object constructor.
	 *
	 * @param   JOAuth1TableCredentials  $table  Connector object for table class.
	 *
	 * @since   12.3
	 */
	public function __construct(JOAuth1TableCredentials $table = null)
	{
		// Setup the database object.
		$this->_table = $table ? $table : JTable::getInstance('Credentials', 'JOAuth1Table');

		// Assume the base state for any credentials object to be new.
		$this->_state = new JOAuth1CredentialsStateNew($this->_table);
	}

	/**
	 * Method to authorise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function authorise($resourceOwnerId)
	{
		$this->_state = $this->_state->authorise($resourceOwnerId);
	}

	/**
	 * Method to convert a set of authorised credentials to token credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function convert()
	{
		$this->_state = $this->_state->convert();
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function deny()
	{
		$this->_state = $this->_state->deny();
	}

	/**
	 * Get the callback url associated with this token.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function getCallbackUrl()
	{
		return $this->_state->callback_url;
	}

	/**
	 * Get the consumer key associated with this token.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function getClientKey()
	{
		return $this->_state->client_key;
	}

	/**
	 * Get the credentials key value.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function getKey()
	{
		return $this->_state->key;
	}

	/**
	 * Get the ID of the user this token has been issued for.  Not all tokens
	 * will have known users.
	 *
	 * @return  integer
	 *
	 * @since   12.3
	 */
	public function getResourceOwnerId()
	{
		return $this->_state->resource_owner_id;
	}

	/**
	 * Get the token secret.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function getSecret()
	{
		return $this->_state->secret;
	}

	/**
	 * Get the credentials type.
	 *
	 * @return  integer
	 *
	 * @since   12.3
	 */
	public function getType()
	{
		return $this->_state->type;
	}

	/**
	 * Get the credentials verifier key.
	 *
	 * @return  integer
	 *
	 * @since   12.3
	 */
	public function getVerifierKey()
	{
		return $this->_state->verifier_key;
	}

	/**
	 * Get the expiration date.
	 *
	 * @return  integer
	 *
	 * @since   12.3
	 */
	public function getExpirationDate()
	{
		return $this->_state->expiration_date;
	}

	/**
	 * Get the temporary expiration date.
	 *
	 * @return  integer
	 *
	 * @since   12.3
	 */
	public function getTemporaryExpirationDate()
	{
		return $this->_state->temporary_expiration_date;
	}

	/**
	 * Method to initialise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   string  $clientKey    The key of the client requesting the temporary credentials.
	 * @param   string  $callbackUrl  The callback URL to set for the temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function initialise($clientKey, $callbackUrl)
	{
		$this->_state = $this->_state->initialise($clientKey, $callbackUrl);
	}

	/**
	 * Method to load a set of credentials by key.
	 *
	 * @param   string  $key  The key of the credentials set to load.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  InvalidArgumentException
	 */
	public function load($key)
	{
		$this->_table->credentials_id = 0;

		$this->_table->loadByKey($key);

		// If nothing was found we will setup a new credential state object.
		if (!$this->_table->credentials_id)
		{
			$this->_state = new JOAuth1CredentialsStateNew($this->_table);

			return;
		}

		// Cast the type for validation.
		$this->_table->type = (int) $this->_table->type;

		// If we are loading a temporary set of credentials load that state.
		if ($this->_table->type === self::TEMPORARY)
		{
			$this->_state = new JOAuth1CredentialsStateTemporary($this->_table);
		}
		// If we are loading a authorised set of credentials load that state.
		elseif ($this->_table->type === self::AUTHORISED)
		{
			$this->_state = new JOAuth1CredentialsStateAuthorised($this->_table);
		}
		// If we are loading a token set of credentials load that state.
		elseif ($this->_table->type === self::TOKEN)
		{
			$this->_state = new JOAuth1CredentialsStateToken($this->_table);
		}
		// Unknown OAuth credential type.
		else
		{
			throw new InvalidArgumentException('OAuth credentials not found.');
		}
	}

	/**
	 * Delete expired credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function clean()
	{
		$this->_table->clean();
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function revoke()
	{
		$this->_state = $this->_state->revoke();
	}
}
