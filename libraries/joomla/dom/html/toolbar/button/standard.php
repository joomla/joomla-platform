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


class JDomHtmlToolbarButtonStandard extends JDomHtmlToolbarButton
{
	var $level = 4;
	var $last = true;		//This class is last call

	/*
	 * Constuctor
	 * 	@namespace 	: Requested class
	 *  @options	: Parameters
	 *  @item		: Joomla Toolbar Item arguments (array)
	 *
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);

		//Dispatch arguments
		$this->name = $this->item[1];
		$this->text = $this->item[2];
		$this->task = $this->item[3];
		$this->list = $this->item[4];

	}

	function build()
	{

		$html =		'<li class="button" style="<%LI_STYLE%>" id="<%LI_ID%>" >'.LN
				.	'	<div class="toolbar" style="<%BUTTON_STYLE%>" onclick="<%COMMAND%>">'.LN
				.	'		<div class="<%ICON_CLASS%>">' .LN
				.	'		</div>' .LN
				.	'		<span class="text" style="white-space:nowrap"><%TEXT%></span>' .LN
				.	'	</div>'
				.	'</li>' .LN;

		return $html;
	}


}