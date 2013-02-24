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


class JDomHtmlFormInputOrdering extends JDomHtmlFormInput
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call

	var $labelKey;
	var $items;
	var $chop;


	protected $labelFirst;
	protected $labelLast;
	protected $labelNew;

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *
	 *	@items		: List of values
	 *	@labelKey	: Item label key
	 *	@neworder	: Defines default position for a new item
	 *	@chop		: Truncate the text value (default:30)
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('items'		, null, $args);
		$this->arg('labelKey'	, null, $args, 'text');
		$this->arg('neworder'	, null, $args, 30);
		$this->arg('chop'		, null, $args, 30);




		if (version_compare(JVERSION, "1.6", "<"))
		{//1.5.x
			$this->labelFirst = "FIRST";
			$this->labelLast = "LAST";
			if ($this->neworder > 0)
				$this->labelNew = $this->JText('DESCNEWITEMSLAST');
			else
				$this->labelNew = $this->JText('DESCNEWITEMSFIRST');
		}
		else
		{//1.6 or Later
			$this->labelFirst = "JOPTION_ORDER_FIRST";
			$this->labelLast = "JOPTION_ORDER_LAST";

			if ($this->neworder > 0)
				$this->labelNew = $this->JText('JGLOBAL_NEWITEMSLAST_DESC');
			else
				$this->labelNew = $this->JText('JGLOBAL_NEWITEMSFIRST_DESC');

		}

	}

	function build()
	{
		$items = array();

		$dataKey = $this->dataKey;
		$chop = $this->chop;
		$first = new stdClass();


// NEW ITEM
		if ($this->dataValue === null)
		{
			$html = JDom::_('html.form.input.hidden', $this->options)
				.	'<span class="readonly">' . $this->labelNew . '</span>'.LN
				.	'<%VALIDOR_ICON%>'.LN
				.	'<%MESSAGE%>';

			return $html;
		}


// EXISTING ORDER
		if (empty($this->items))
		{
			$first->$dataKey = '1';
			$first->text = $this->JText($this->labelFirst);

			$items[] = $first;
		}
		else
		{
			$first->$dataKey = '0';
			$first->text = $this->JText($this->labelFirst);

			$items[] = $first;

			for($i = 1 ; $i <= count($this->items) ; $i++)
			{
				$item = $this->items[$i - 1];
				$text = $this->JText($this->parseKeys($item, $this->labelKey));

				if (JString::strlen($text) > $chop) {
					$text = JString::substr($text,0,$chop)."...";
				}

				$indexStr = str_pad((string)$i, 3, "-", STR_PAD_LEFT);

				$text = $indexStr . ' (' . $text . ')';



				$elem = new stdClass();
				$elem->$dataKey = $i;
				$elem->text = $text;

				$items[] = $elem;
			}

			$last = new stdClass();
			$last->$dataKey = $i;
			$last->text = $this->JText($this->labelLast);


			$items[] = $last;

		}



		$html = JDom::_('html.form.input.select', array_merge($this->options, array(
																	'list' => $items,
																	'listKey' => $dataKey,
																	'labelKey' => 'text',
																	'dataValue' => (int)$this->dataValue
																	)));
		return $html;
	}

}