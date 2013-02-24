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


class JDomHtmlFormInputRadio extends JDomHtmlFormInput
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call

	var $list;
	var $listKey;
	var $labelKey;
	var $direction;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *
	 * 	@list		: Possibles values list (array of objects)
	 * 	@listKey	: ID key name of the list
	 * 	@labelKey	: Caption key name of the list
	 * 	@direction	: 'horizontal' or 'vertical'  (default: horizontal)
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('list'		, 6, $args);
		$this->arg('listKey'	, 7, $args, 'id');
		$this->arg('labelKey'	, 8, $args, 'text');
		$this->arg('direction'	, 9, $args, 'horizontal');
		$this->arg('domClass'	, 10, $args);
		$this->arg('selectors'	, 11, $args);

		//Reformate items
		$i = 0;
		$newArray = array();
		if (count($this->list))
		foreach($this->list as $item)
		{
			if (is_array($item))
			{
				$newItem = new stdClass();
				foreach($item as $key => $value)
				{
					$newItem->$key = $value;
				}

				$newArray[$i] = $newItem;

			}
			$i++;
		}

		if (count($newArray))
			$this->list = $newArray;

	}

	function build()
	{
		$this->addStyle('float', 'left');

		$html =	'<fieldset id="__<%DOM_ID%>" class="radio radio_wrapper " <%STYLE%>>';


		$i = 0;
		foreach($this->list as $item)
		{
			$id = $this->dataKey . '_' . $i;
			$html .= $this->buildRadio($item, $this->listKey, $this->labelKey, $id);

			if ($this->direction == 'vertical')
				$html .= BR;
			$i++;
		}

		$html .=	LN
				.	'</fieldset>'
				.	'<%VALIDOR_ICON%>'.LN
				.	'<%MESSAGE%>';


		return $html;
	}

	function buildRadio($item, $listKey, $labelKey, $id)
	{
		$checked = ($item->$listKey == $this->dataValue);

		$js = '';

		$html =	'<input type="radio" name="<%INPUT_NAME%>"'
			.	' id="' . $id . '"'
			.	' value="' . $item->$listKey . '"'
			.	' ' . $js
			.	($checked?' checked="checked"':'');

		if (!defined('JQUERY') && (isset($this->required)))
			$html .= ' class="required"';

		else if (defined('JQUERY') && count($this->classes))
			$html .= ' class="<%CLASSES%>"';


		$html	.=	'/>'.LN;


		$html .= JDom::_('html.form.label', array(
											'domId' => $id,
											'label' => $this->parseKeys($item, $labelKey),

											)) .LN;

		return $html;
	}


}