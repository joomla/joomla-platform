<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Inspector classes for the JLog package.
 */

/**
 * @package		Joomla.UnitTest
 * @subpackage  Log
 */
class JMailMock
{
	/**
	 * @var    string  From (email address)
	 * @since  12.1
	 */
	public $From;

	/**
	 * @var    string  FromName (Name of sender)
	 * @since  12.1
	 */
	public $FromName;

	/**
	 * @var    string  to
	 * @since  12.1
	 */
	public $to;

	/**
	 * @var    string  Subject
	 * @since  12.1
	 */
	public $Subject;

	/**
	 * @var    string  Body
	 * @since  12.1
	 */
	public $Body;

	/**
	 * @var    array  Sent
	 * @since  12.1
	 */
	public $Sent = array();

	/**
	 * Set the Sender value
	 *
	 * @param   array	$value
	 *
	 * @return  void
	 *
	 * @since	12.1
	 */
	public function setSender($value)
	{
		if (is_array($value))
		{
			$this->From = $value[0];
			$this->FromName = $value[1];
		}
		else
		{
			$this->From = $value;
		}
	}

	/**
	 * Add Recipient value
	 *
	 * @param   string	$value
	 *
	 * @return  void
	 *
	 * @since	12.1
	 */
	public function addRecipient($value)
	{
		$this->to = $value;
	}

	/**
	 * Set the Subject value
	 *
	 * @param   string	$value
	 *
	 * @return  void
	 *
	 * @since	12.1
	 */
	public function setSubject($value)
	{
		$this->Subject = $value;
	}

	/**
	 * Set the Body value
	 *
	 * @param   string	$value
	 *
	 * @return  void
	 *
	 * @since	12.1
	 */
	public function setBody($value)
	{
		$this->Body = $value;
	}

	/**
	 * Send the email
	 *
	 * @return  boolean
	 *
	 * @since	12.1
	 */
	public function Send()
	{
		if ($this->From === null
			|| $this->to === null
			|| $this->Subject === null
			|| $this->Body === null)
		{
			return false;
		}
		else
		{
			$this->Sent = array(
				$this->From,
				$this->FromName,
				$this->to,
				$this->Subject,
				$this->Body
			);
			return true;
		}
	}
}
