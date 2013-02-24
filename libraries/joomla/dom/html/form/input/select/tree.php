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


class JDomHtmlFormInputSelectTree extends JDomHtmlFormInputSelect
{
	var $level = 5;			//Namespace position : function
	var $last = true;		//This class is last call

	var $keyPattern = null;

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 * 	@list		: Possibles values list (array of objects)
	 * 	@listKey	: ID key name of the list
	 * 	@labelKey	: Caption key name of the list
	 * 	@size		: Size in rows ( 0,null = combobox, > 0 = list)
	 * 	@nullLabel	: First choice label for value = '' (no null value if null)
	 * 	@groupBy	: Group values on key(s)  (Complex Array Struct)
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 *
	 *	@keyPattern : fieldname for the pattern value
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('keyPattern'		, 14, $args, "pattern");


	}

	function build()
	{

		if ($this->groupBy)
			$options = $this->buildOptionsGroup();
		else
			$options = $this->buildOptions();


		$html =	'<select id="<%DOM_ID%>" name="<%INPUT_NAME%>"<%STYLE%><%CLASS%><%SELECTORS%>'
			.	($this->size?' size="' . $this->size . '"':'') . '>' .LN
			.	$this->indent($this->buildDefault(), 1)
			.	$this->indent($options, 1)
			.	'</select>'.	LN
			.	'<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%>';

		return $html;

	}

	function buildDefault()
	{
		if (!$this->nullLabel)
			return '';

		$item = new stdClass();
		$item->id = '';
		$item->text = JText::_($this->nullLabel);

		return $this->buildOption($item, 'id', 'text');

	}

	function buildOptions()
	{
		$html =	'';

		if ($this->list)
		foreach($this->list as $item)
		{
			$prefix = $this->treePattern($item);

			$html .= $this->buildOption($item, $this->listKey, $this->labelKey, $prefix);
		}

		return $html;

	}


	function buildOption($item, $listKey, $labelKey, $prefix = '')
	{
		if (!isset($item->$listKey))
			$item->$listKey = null;

		$selected = ($item->$listKey == $this->dataValue);

		$html =	'<option value="'
			.	htmlspecialchars($item->$listKey, ENT_COMPAT, 'UTF-8')
			. 	'"'
			.	($selected?' selected="selected"':'')
			.	'>'
			.	$prefix . $item->$labelKey
			. 	'</option>'.LN;

		return $html;
	}


	function treePattern($item)
	{

		$strPattern = "";
		$lastIndent = "&nbsp;&nbsp;";

		$pattern = $item->{$this->keyPattern};

		for($i = 0 ; $i < strlen($pattern) ; $i++)
		{

			switch(substr($pattern, $i, 1))
			{
				case '.':	// Blank Space
					$strPattern .= "&nbsp;&nbsp;&nbsp;";
					break;


				case 'l':	// Vertical line
					$strPattern .= "&nbsp;|&nbsp;";

					break;





				case 't':	// Item  (Leaf)
					$strPattern .= "&nbsp;L" . $lastIndent;
					break;

				case 'L':	// Last item  (Leaf)
					$strPattern .= "&nbsp;L" . $lastIndent;
					break;



				case 'u':	// Item node (Node)
					$strPattern .= "&nbsp;L" . $lastIndent;
					break;

				case 'M':	// Last item node (Node)
					$strPattern .= "&nbsp;L" . $lastIndent;
					break;


			}
		}


		return $strPattern;
	}



}