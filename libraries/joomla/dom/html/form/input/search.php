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


class JDomHtmlFormInputSearch extends JDomHtmlFormInput
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call

	var $assetName = 'searchbox';


	var $attachJs = array(
		'searchbox.js'
	);

	var $size;
	var $label;

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
	 * 	@size		: Input Size
	 *  @label		: Filter label
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('size'		, null, $args, '12');
		$this->arg('label'		, null, $args);
	}

	function build()
	{
		$empty = false;
		if (isset($this->label))
		{
			$empty = ($this->dataValue?0:1);
			$this->addClass('search_default');
		}
		$label = null;
		if (isset($this->label))
			$label = JText::_($this->label);


		$imgURLback = $this->assetImage('searchbox-back.png', 'searchbox');
		$imgURLright = $this->assetImage('searchbox-r.png', 'searchbox');

		$this->addStyle("background", "url(" . $imgURLback . ") left no-repeat");
		$this->addStyle("border", "none");
		$this->addStyle("height", "22px");
		$this->addStyle("padding-left", "14px");


		$html = '<div class="jdom" rel="<%JSON_REL%>"'
			.	'style="'
			.	'background:url(' . $imgURLright. ') right no-repeat;'
			.	'padding-right:20px;'
			.	'display:inline-block;'
			.	($label?' cursor:help;':'')
			.	'"'
			.	($label?' title="' . $label . '"':'')
			.	'>';

		$html .=	'<input type="text" id="search_input_<%DOM_ID%>" <%STYLE%><%CLASS%><%SELECTORS%>'
			.	' value="' . ($empty?$label:htmlspecialchars($this->dataValue, ENT_COMPAT, 'UTF-8')) . '"'
			.	' size="' . $this->size . '"'
			.	' placeholder="' . htmlspecialchars($label, ENT_COMPAT, 'UTF-8') . '"'
			.	'/>' .LN
			.	'<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%>';

		$html .= '</div>';


		$html .= '<input type="hidden" id="<%INPUT_NAME%>" name="<%INPUT_NAME%>" value="<%VALUE%>"/>';

		if (isset($this->label))
		{

			$html .= '<input type="hidden" id="search_label_<%INPUT_NAME%>" value="' . $label . '"/>';

			$html .= '<input type="hidden" size="2" id="search_empty_<%INPUT_NAME%>" value="' . $empty . '"/>';

		}


		return $html;
	}


	function buildJS()
	{
		if (defined('JQUERY'))
		{
			$script = 'jQuery("#' . $this->getInputId() . '").jdomSearchBox({});';
			$this->addScriptInline($script, true);
			return;
		}
	}



	function jsonArgs($args = array())
	{
		$args = array_merge($args, array(
					'asset' => $this->assetName,
					'name' => $this->dataKey,
					'selection' => $this->dataKey . '_selection',

				));

		return json_encode($args);

	}


}