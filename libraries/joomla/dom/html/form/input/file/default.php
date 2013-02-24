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


class JDomHtmlFormInputFileDefault extends JDomHtmlFormInputFile
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	protected $size;
	protected $uploadMaxSize;
	protected $actions;
	protected $allowedExtensions;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 * 	@indirect	: Indirect file access (bool)
	 * 	@domClass	: CSS class
	 * 	@selectors	: raw selectors (Array) ie: javascript events
	 *
	 *	@size		: Input Size
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('size'			, null, $args, '32');
		$this->arg('uploadMaxSize'	, null, $args);
		$this->arg('actions'		, null, $args);
		$this->arg('allowedExtensions'	, null, $args);

	}

	function build()
	{

		$html = '<div class="form-widget">';

		$pickerStyle = "";
		if ($this->thumb)
			$pickerStyle = 'border:dashed 3px #ccc; padding:5px; margin:5px;display:inline-block';

		$isNew = (empty($this->dataValue));

		if (!$isNew && isset($this->actions))
			$html .= JDom::_('html.form.input.file.remove', $this->options);


		$html .= "<div style='" . $pickerStyle . "'>";
		$html .= JDom::_("html.fly.file", $this->options);
		$html .= "</div>";

		if ($pickerStyle)
			$html .= '<br/>';


		$btnId = '__' . $this->dataKey . '_btnnew';
		$btnCancel = '__' . $this->dataKey . '_btncancel';
		$uploadDivId = '__' . $this->dataKey . '_divupload';

		if (!$isNew)
		{
			$html .= JDom::_("html.link.button.joomla", array(
									'link_js' => 'jQuery(' . $uploadDivId . ').show();jQuery(' . $btnId . ').hide();',
									'content' => JText::_('New upload'),
									'icon' => 'image',
									'styles' => array(),
									'domId' => $btnId

									));
		}


		$html .= ''
			.	'<div id="' . $uploadDivId . '"'
			.	(!$isNew?' style="display:none;"':'')
			.	'>';



		$html .='<input type="file" id="<%DOM_ID%>" name="<%INPUT_NAME%>"<%STYLE%><%CLASS%><%SELECTORS%>'
			.	' value="<%VALUE%>"'
			.	' size="' . $this->size . '"'
			.	' data-allowed-ext="' . htmlspecialchars($this->allowedExtensions) . '"'

			.	'/>' .LN;

		if (!$isNew)
		{
			$html .= JDom::_("html.link.button.joomla", array(
							'link_js' => 'jQuery(' . $uploadDivId . ').hide();jQuery(' . $btnId . ').show()',
							'content' => JText::_('Cancel'),
							'icon' => 'image',
							'styles' => array(),
							'domId' => $btnCancel

							));
		}


		if (isset($this->uploadMaxSize))
		{
			$html .= JDom::_("html.fly", array(
								'dataValue' => $this->uploadMaxSize
								));
		}


		if (isset($this->allowedExtensions))
		{
			$html .= '<br/>(' . preg_replace("/\|/", ', ', $this->allowedExtensions) . ')';

		}

		$html.= '<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%></div></div>';



		return $html;
	}


}