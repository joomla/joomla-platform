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


class JDomHtmlToolbarDefault extends JDomHtmlToolbar
{
	var $level = 3;			//Namespace position
	var $last = true;		//This class is last call

	/*
	 * Constuctor
	 * 	@namespace 	: Requested class
	 *  @options	: Parameters
	 *  @bar		: Joomla Toolbar
	 *
	 *
	 *  @align		: Toolbar alignement  (float)
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('align'		, 3, $args, 'left');
		$this->arg('domClass'	, 4, $args);
		$this->arg('selectors'	, 5, $args);


	}

	function build()
	{
		if (!$this->items || !count($this->items))
			return '';


		$this->domClass .= ' toolbar-list';

		$html =	'<div id="toolbar"'
			.	$this->buildDomClass()
			.	$this->buildSelectors()
			.	'>' .LN
			.	$this->indent($this->buildItems(), 1)
			.	'</div>';




		return $html;
	}

	function buildItems()
	{
		$htmlItems = '';


		if ($this->align == 'right')
			$this->items = array_reverse($this->items);


		foreach($this->items as $item)
		{
			if (!count($item))
				continue;

			$itemNameSpace = 'html.toolbar.button.' . strtolower($item[0]);
			$htmlItems .= JDom::_($itemNameSpace, $this->options, $item, $this->align) .LN;
		}


		$html = '<ul'
			.	($this->align == 'center'?' style="text-align:center;"':'')
			. 	'>'
			.	$htmlItems
			.	'</ul>';


//		$html .= ($this->align != 'center'?'<br clear="all"/>':'');
		$html .= '<br clear="all"/>';

		return $html;

	}


}