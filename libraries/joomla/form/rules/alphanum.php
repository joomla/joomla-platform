<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.2
 */
class JFormRuleAlphanum extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $regex;

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $modifiers = 'm';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->regex = JCOMPAT_UNICODE_PROPERTIES ? '^[\pL\p{Nd} ]*$' : '^[[:alnum:] ]*$';
	}
}
