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


class JDomHtmlFormInputColorpickerMoocook extends JDomHtmlFormInput
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	var $assetName = 'colorpicker';


	var $attachJs = array(
		'colorpicker.js'
	);

	var $attachCss = array(
	);



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
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('domClass'	, 7, $args);
		$this->arg('selectors'	, 8, $args);


	}

	function build()
	{

		$this->addStyle('float', 'left');


		$html =	'<div class="jdom" rel="<%JSON_REL%>">'


//Input Box
			.	'<input type="text" id="<%DOM_ID%>" name="<%INPUT_NAME%>"<%STYLE%><%CLASS%><%SELECTORS%>'
			.	' value="<%VALUE%>"'
			.	' size="6"'
			.	'/>' .LN

//Selected color view
			.	'<div id="' . $this->dataKey . '_selection"'
			.	' style="width:20px;height:20px;border:solid 1px #eee;'
			.	'-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:15px;'
			.	'float:left"'
			.	'>'
			.	'</div>'
			.	'<%VALIDOR_ICON%>'.LN
			.	'<br clear="left"/>'
			.	'</div>' .LN
			.	'<div clear="left"><%MESSAGE%></div>';


		return $html;
	}



	function jsonArgs($args = array())
	{
		$args = array_merge($args, array(
					'asset' => 'colorpicker',
					'variant' => 'rvb',
					'name' => $this->dataKey,
					'selection' => $this->dataKey . '_selection',

				));

		return json_encode($args);

	}

}