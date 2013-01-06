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


class JDomHtmlFormInputCalendar extends JDomHtmlFormInput
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call

	var $dateFormat;
	var $filter;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *
	 *
	 *	@dateFormat	: Date Format
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('dateFormat'		, 6, $args, "%Y-%m-%d");
		$this->arg('filter'		, 6, $args);

		//Instance the validator
		if ($this->dateFormat)
			$this->validatorRegex = $this->regexFromDateFormat();

		$this->addValidatorHandler();
	}

	function regexFromDateFormat()
	{
		$d2 = '[0-9]{2}';
		$d4 = '[1-9][0-9]{3}';

		$patterns =
array(	'\\','/','#','!','^','$','(',')','[',']','{','}','|','?','+','*','.',
		'%?Y','%?y','%?m','%?d', '%?H', '%?I', 'i', '%?l', '%?M', '%?S', ' ');
		$replacements =
array(	'\\\\', '\\/','\\#','\\!','\\^','\\$','\\(','\\)','\\[','\\]','\\{','\\}','\\|','\\?','\\+','\\*','\\.',
		$d4,$d2,$d2,$d2,$d2,$d2,$d2,$d2,$d2,$d2,'\s');





		return "^" . str_replace($patterns, $replacements, $this->dateFormat) . "$";
	}

	function build()
	{

		$dateFormat = $this->dateFormat;

		//JDate::toFormat() is deprecated. CONVERT Legacy Joomla Format
		//Minutes : ‰M > i
		$dateFormat = str_replace("%M", "i", $dateFormat);
		//remove the %
		$dateFormat = str_replace("%", "", $dateFormat);
	
	
		$formatedDate = $this->dataValue;

		if ($this->dataValue
		&& ($this->dataValue != "0000-00-00")
		&& ($this->dataValue != "00:00:00")
		&& ($this->dataValue != "0000-00-00 00:00:00"))
		{
			jimport("joomla.utilities.date");
			$date = JFactory::getDate($this->dataValue);
			$formatedDate = $date->format($dateFormat);

			$config = JFactory::getConfig();
			// If a known filter is given use it.
			switch (strtoupper(($this->filter)))
			{
				case 'SERVER_UTC':
					// Convert a date to UTC based on the server timezone.
					if (intval($this->dataValue))
					{
						// Get a date object based on the correct timezone.
						$date = JFactory::getDate($this->dataValue, 'UTC');
						$date->setTimezone(new DateTimeZone($config->get('offset')));

						// Format the date string.
						$formatedDate = $date->format($dateFormat, true);
					}
					break;

				case 'USER_UTC':
					// Convert a date to UTC based on the user timezone.
					if (intval($this->dataValue))
					{
						// Get a date object based on the correct timezone.
						$date = JFactory::getDate($this->dataValue, 'UTC');
						$user = JFactory::getUser();
						$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

						// Format the date string.
						$formatedDate = $date->format($dateFormat, true);
					}
					break;
			}

		}
		else
			$formatedDate = "";

		$config = array();
		if ($this->submitEventName == 'onchange')
		{
			$jsEvent = $this->getSubmitAction();

			$config['onClose'] = "function(cal){if(cal.dateClicked){"
			. $jsEvent
			. "}cal.hide();}";
		}

		$html = self::calendar(
					$formatedDate,
					$this->getInputName(),
					$this->getInputId(),
					$this->dateFormat,		//Keep deprecated date format style
					'class="' . $this->getDomClass() . '"<%STYLE%><%SELECTORS%>',
					$config)
			.	LN
			.	'<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%>';



		return $html;
		
	}

	/**
	 * Displays a calendar control field
	 *
	 * @param   string  $value    The date value
	 * @param   string  $name     The name of the text field
	 * @param   string  $id       The id of the text field
	 * @param   string  $format   The date format
	 * @param   array   $attribs  Additional HTML attributes
	 * @param	array	$config		Additional JS Config parameters
	 *
	 * @return  string  HTML markup for a calendar field
	 *
	 * @since   11.1
	 */
	public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null, $config = array())
	{
		static $done;

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		if ((!$readonly) && (!$disabled))
		{
			// Load the calendar behavior
			JHtml::_('behavior.calendar');
			JHtml::_('behavior.tooltip');

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{


				$document = JFactory::getDocument();

				$jsonConfig = "";
				foreach($config as $key => $quotedValue)
				{
					$jsonConfig .= "," . $key . ":" . $quotedValue;

				}


				if (version_compare(JVERSION, "1.6", "<"))
				{
					$imgBaseUrl = '/templates/system/images';
					$imgAlt = 'calendar';
				}
				else
				{
					$jsonConfig .= ",firstDay: " . JFactory::getLanguage()->getFirstDay();
					$imgBaseUrl = "system";
					$imgAlt = JText::_('JLIB_HTML_CALENDAR');

				}

				$document->addScriptDeclaration(
					'window.addEvent(\'domready\', function() {if($("' . $id . '")) Calendar.setup({
					// Id of the input field
					inputField: "' . $id . '",
					// Format of the input field
					ifFormat: "' . $format . '",
					// Trigger for the calendar (button ID)
					button: "' . $id . '_img",
					// Alignment (defaults to "Bl")
					align: "Tl",
					singleClick: true' . $jsonConfig . '
					});});'
				);




				$done[] = $id;
			}
		}

		return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id
			. '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
			. ($readonly ? ''
			: JHtml::_('image', $imgBaseUrl . '/calendar.png', $imgAlt, array('class' => 'calendar', 'id' => $id . '_img'), true));


	}


}