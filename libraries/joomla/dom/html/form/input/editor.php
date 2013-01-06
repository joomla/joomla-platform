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


class JDomHtmlFormInputEditor extends JDomHtmlFormInput
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call


	var $cols;
	var $rows;
	var $width;
	var $height;
	var $editor;

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
	 * 	@cols		: Textarea width (in caracters)
	 * 	@rows		: Textarea height (in caracters)
	 * 	@width		: Textarea width (in px)
	 * 	@height		: Textarea height (in px)
	 * 	@editor		: Editor name (for example, 'tinymce'). If null then the current editor will be returned
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('cols'		, 6, $args, '32');
		$this->arg('rows'		, 7, $args, '4');
		$this->arg('width'		, 8, $args, $this->cols * 10);
		$this->arg('height'		, 9, $args, $this->rows * 20);

		$this->arg('editor'		, 10, $args);


		$this->arg('domClass'	, 11, $args);
		$this->arg('selectors'	, 12, $args);


	}

	function build()
	{
		$html = '';

		$editor = JFactory::getEditor($this->editor);
		$editor->set( 'toolbar', 'Default' );


		$html .= '<div class="form-widget">';
		$html .= $editor->display($this->getInputName(), $this->dataValue, $this->width, $this->height, $this->cols, $this->rows, false);
		$html .= '</div>';

		return $html;
	}


}