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


class JDomHtmlFormInputFileImagepicker extends JDomHtmlFormInputFile
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	var $assetName = 'imagepicker';

	var $attachJs = array(
		'insert.js'
	);

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *	@indirect	: Indirect File access
	 *	@root		: Default folder (alias : ex [DIR_TABLE_FIELD]) -> Need a parser (Cook helper)
	 *	@width		: Thumb width
	 *	@height		: Thumb height
	 *	@attrs		: File attributes ('crop', 'fit', 'center', 'quality')
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);

	}

	function build()
	{
		$html = '';

		// TODO : MooTools is deprecated
		JHTML::_('behavior.modal');

		$id = $this->getInputId();

		$html .= '<div class="form-widget">';

		$pickerStyle = "";
		if ($this->thumb)
			$pickerStyle = 'border:dashed 3px #ccc; padding:5px; margin:5px;display:inline-block';


		$html .= "<div id='_" . $id . "_preview' style='" . $pickerStyle . "'>";
		$html .= JDom::_("html.fly.file", $this->options);
		$html .= "</div>";

		if ($pickerStyle)
			$html .= '<br/>';

		$html .= JDom::_("html.link.button.joomla", array(
										'href' => 'index.php?option=com_media&view=images&layout=default&tmpl=component&e_name='.$id,
										'content' => JText::_('Image'),
										'icon' => 'image',
										'target' => 'modal',
										'handler' => 'iframe'

										));

		$html .= JDom::_("html.form.input.hidden", $this->options);

		$html.= '</div>';

		return $html;
	}


	function buildJS()
	{
		$size = "";
		$attrs = "";

		$w = (int)$this->width;
		$h = (int)$this->height;

		if ($w || $h)
			$size = $w ."x". $h;

		if ($this->attrs)
			$attrs .= implode(",", $this->attrs);

		$indirectUrl = JURI::base(true) . "/index.php?option=" . $this->getExtension() . "&task=file&path=[IMAGES]";
		$id = $this->getInputId();

		$script = "jInsertFields['" . $id . "'] = {"
				.	"'url': \"" . $indirectUrl . "\","
				.	"'size': \"" . $size . "\","
				.	"'attrs': \"" . $attrs . "\","
				.	"'preview': " . (int)$this->thumb

				.	"}";

		$this->addScriptInline($script);
	}


}