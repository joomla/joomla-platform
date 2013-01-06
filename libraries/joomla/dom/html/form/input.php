<?php
/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <     JDom Class - Cook Self Service library    |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo---------------------------------- www.j-cook.pro --- +
* @version		1.0.0
* @package		Cook Self Service
* @subpackage	JDom
* @license		GNU General Public License
* @author		100% Vitamin - Jocelyn HUARD
*
*	-> You can reuse this framework for all purposes. Keep this signature. <-
*
* /!\  Joomla! is free software.
* This version may have been modified pursuant to the GNU General Public License,
* and as distributed it includes or is derivative of works licensed under the
* GNU General Public License or other free or open source software licenses.
*
*             .oooO  Oooo.     See COPYRIGHT.php for copyright notices and details.
*             (   )  (   )
* -------------\ (----) /----------------------------------------------------------- +
*               \_)  (_/
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JDomHtmlFormInput extends JDomHtmlForm
{
	var $level = 3;				//Namespace position
	var $fallback = 'text';		//Used for default

	var $dataKey;
	var $dataObject;
	var $dataValue;
	var $domId;
	var $domName;
	var $formControl;
	protected $required;
	protected $validatorHandler;
	protected $dateFormat;
	protected $validatorRegex;
	protected $validatorMsgInfo;
	protected $validatorMsgIncorrect;
	protected $validatorMsgRequired;


	protected $validatorInsensitive = false;
	protected $validatorInvert = false;
	protected $validatorModifiers = '';


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 *
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domId		: HTML id (DOM)  default=dataKey
	 *  @domName	: HTML name (DOM)  default=dataKey
	 *  @formControl: Form group (JForms)
	 *
	 * Validator
	 *  @required	: If the field is required
	 *  @validatorHandler 		: Validator alias
	 *  @dateFormat				: Date format to convert in regex
	 *  @validatorRegex			: Validation regex
	 *  @validatorMsgInfo 		: Introdution message
	 *  @validatorMsgIncorrect 	: Error message
	 *  @validatorMsgRequired 	: Required error message
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('dataKey'	, null, $args);
		$this->arg('dataObject'	, null, $args);
		$key = $this->dataKey;
		$this->arg('dataValue'	, null, $args, (($this->dataObject && $key)?(isset($this->dataObject->$key)?$this->dataObject->$key:null):null));
		$this->arg('domId'		, null, $args);
		$this->arg('domName'	, null, $args);
		$this->arg('formControl', null, $args);
		$this->arg('required' 				, null, $args, false);
		$this->arg('validatorHandler' 		, null, $args);
		$this->arg('dateFormat' 			, null, $args);
		$this->arg('validatorRegex' 		, null, $args);
		$this->arg('validatorMsgInfo' 		, null, $args);
		$this->arg('validatorMsgIncorrect' 	, null, $args, "JDOM_VALIDATOR_INCORRECT");
		$this->arg('validatorMsgRequired' 	, null, $args, "JDOM_VALIDATOR_REQUIRED");


		if (isset($this->dateFormat))
		{
			$this->validatorRegex = $this->strftime2regex($this->dateFormat);
		}

		if (isset($this->validatorRegex))
		{
			if (!defined('JQUERY'))
			{
				//DEPRECATED
				if (substr($this->validatorRegex, 0, 1) == '!')
				{
					$this->validatorRegex = substr($this->validatorRegex, 1);
					$this->validatorInvert = true;
				}

			}

			//Last char is a 'i' modifier
			if (substr(strrev($this->validatorRegex), 0, 1) == 'i')
			{
				$this->validatorRegex = substr($this->validatorRegex, 0, strlen($this->validatorRegex) - 1);
				$this->validatorInsensitive = true;
				$this->validatorModifiers = 'i';
			}

			//Trim slashes
			$this->validatorRegex = trim($this->validatorRegex, "/");

		}


		$this->addClass('inputbox');

		if (isset($this->required) && $this->required)
			$this->addClass('required');

		if (isset($this->validatorHandler) && $this->validatorHandler)
			$this->addClass('validate-' . $this->validatorHandler);

	}

	function addValidatorHandler($regex= null, $handler = null)
	{
		if (defined('JQUERY'))
		{
			if (!isset($this->validatorHandler))
				return;

			if (!$jsRule = self::getJsonRule())
				return;

			$script = 'jQuery.validationEngineLanguage.allRules.' . $this->validatorHandler . ' = ' . $jsRule;
		}
		else
		{
			//DEPRECATED (MooTools)

			if (!$handler && isset($this->validatorHandler))
			$handler = $this->validatorHandler;

			if (!$regex && isset($this->validatorRegex))
				$regex = $this->validatorRegex;

			//Escape quotes now because escapes has been automaticaly removed.
			$regex = str_replace('"', '\\"', $regex);

			if ((!$handler) || (!$regex))
				return;

			$jsRegex = $this->reformateRegexForJS($regex);

			$script = 	'window.addEvent("domready", function() {' .LN
					.	'	if (typeof(document.formvalidator) != "undefined")'.LN
					.	'	document.formvalidator.setHandler("' . $handler . '", function(value, el) {' .LN;

			if($this->validatorInsensitive)
				$script .=	'		var regex = new RegExp("' . $jsRegex . '", \'i\');' .LN;
			else
				$script .=	'		var regex = new RegExp("' . $jsRegex . '");' .LN;

			if($this->validatorInvert)
				$script .=	'		return !regex.test(value);' .LN;
			else
				$script .=	'		return regex.test(value);' .LN;


			$script .=	'	});' .LN
					.	'});';


		}



		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);
	}

	function reformateRegexForJS($regex)
	{
		$regex = preg_replace("/\\\\/", "\\\\\\\\", $regex);
		$regex = preg_replace("/\\\\s/", " ", $regex);
		$regex = preg_replace("/\\\\d/", "[0-9]", $regex);
		return $regex;
	}

	function buildValidatorMessage()
	{
		if (defined('JQUERY'))
		{
			if (isset($this->validatorMsgInfo))
				$this->loadScriptPromptInfo($this->getInputId(), $this->JText($this->validatorMsgInfo));

			return '';
		}

		//DEPRECATED (MooTools)

		$html = '<div class="field-validator field_message" style="clear:left;"'
			.		' id="field_message_' . $this->dataKey . '">';

		//Field is required
		if (isset($this->required) && $this->required
			&& isset($this->validatorMsgRequired))
		{
			$html	.=	'<span class="message msg-required message_required" id="message_' . $this->dataKey . '_required" style="display:none;">'
				.		$this->JText($this->validatorMsgRequired)
				.		'</span>';
		}

		if (isset($this->validatorHandler))
		{
			//Message if field is empty
			if (isset($this->validatorMsgInfo))
			{
				$html	.=	'<span class="message msg-info message_info" id="message_' . $this->dataKey . '_info" style="display:none;">'
					.		$this->JText($this->validatorMsgInfo)
					.		'</span>';
			}

			//Validator returns wrong
			if (isset($this->validatorMsgIncorrect))
			{
				$html	.=	'<span class="message msg-incorrect message_incorrect" id="message_' . $this->dataKey . '_incorrect" style="display:none;">'
					.		$this->JText($this->validatorMsgIncorrect)
					.		'</span>';
			}
		}

		$html	.=	'</div>';

		return $html;
	}

	function buildValidatorIcon()
	{
		if (defined('JQUERY'))
			return;

		if ((!isset($this->required) || !$this->required) && !isset($this->validatorHandler))
			return '';


		$html = '<div class="validatoricon validator-icon"'
			.	' id="validatoricon_' . $this->dataKey . '"'
			.	' style="display:inline-block">'
			.	'</div>';


		return $html;
	}

	function parseVars($vars = array())
	{

		return array_merge(array(
				'DOM_ID'		=> $this->getInputId(),
				'INPUT_NAME'	=> $this->getInputName(),
				'STYLE'		=> $this->buildDomStyles(),
				'CLASS'			=> $this->buildDomClass(),		//With attrib name
				'CLASSES'		=> $this->getDomClass(),		// Only classes
				'SELECTORS'		=> $this->buildSelectors(),
				'VALUE'			=> htmlspecialchars($this->dataValue, ENT_COMPAT, 'UTF-8'),
				'MESSAGE' 		=> $this->buildValidatorMessage(),
				'VALIDOR_ICON' 	=> $this->buildValidatorIcon(),
				'JSON_REL' 		=> htmlspecialchars($this->jsonArgs(), ENT_COMPAT, 'UTF-8'),
				), $vars);
	}

	function getInputName()
	{
		if (isset($this->domName))
			return $this->domName;

		if (!empty($this->formControl))
			return $this->formControl . "[" . $this->dataKey . "]";

		return $this->dataKey;
	}

	function getInputId()
	{
		if (isset($this->domId))
			return $this->domId;

		if (!empty($this->formControl))
			return $this->formControl . "_" . $this->dataKey;

		return $this->dataKey;
	}


	function buildJS()
	{
		$this->addValidatorHandler();
	}



	//jQuery Validator

	/**
	* Render a prompt information to guide the user.
	*
	* @access	public static
	* @param	string	$id	The input id.
	* @param	string	$message	The message to display
	*
	* @return	void
	* @return	void
	*/
	public static function loadScriptPromptInfo($id, $message)
	{
		$script = 'jQuery(document).ready(function(){' .
					'var el = jQuery("#' . $id . '");' .
					'el.validationEngine("showPrompt", "' . addslashes($message) . '", "pass", false);' .
				'});';

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);
	}

	/**
	* Get the JSON object rule for the validator.
	*
	* @access	public static
	* @param	JXMLElement	$fieldNode	The XML field node.
	* @param	JFormRule	$rule	The validator rule.
	*
	* @return	string	JSON string.
	*/
	public function getJsonRule()
	{
		if (!isset($this->validatorRegex))
			return;

		//Escape quotes now because escapes has been automaticaly removed.
		$regex = str_replace('"', '\"', $this->validatorRegex);

		//reformate Regex for javascript
		$jsRegex = $this->reformateRegexForJS($regex);

		$values = array(
			"#regex" => 'new RegExp("' . $jsRegex . '", \'' . $this->validatorModifiers . '\')',
			"alertText" => LI_PREFIX . addslashes(JText::_($this->validatorMsgIncorrect))
		);


		$json = self::jsonFromArray($values);

		return "{" . LN . $json . LN . "}";
	}

	/**
	* Transform a recursive associative array in JSON string.
	*
	* @access	public static
	* @param	array	$values	Associative array only (can be recursive).
	*
	* @return	string	JSON string.
	*/
	public static function jsonFromArray($values)
	{
		$entries = array();
		foreach($values as $key => $value)
		{
			$q = "'";

			if (is_array($value))
			{
				// ** Recursivity **
				$value = "{" . LN . self::jsonFromArray($value) . LN . "}";
				$q = "";
			}
			else if (substr($key, 0, 1) == '#')
			{
				//Do not require quotes
				$key = substr($key, 1);
				$q = "";
			}

			$entries[] = '"'. $key. '" : '. $q. $value. $q;
		}

		return implode(',' .LN, $entries);
	}




}