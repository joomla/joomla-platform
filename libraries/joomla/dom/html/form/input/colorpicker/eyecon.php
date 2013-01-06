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


class JDomHtmlFormInputColorpickerEyecon extends JDomHtmlFormInputColorpicker
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	var $assetName = 'colorpicker-eyecon';

	var $attachJs = array(
		'colorpicker.js'
	);

	var $attachCss = array(
		'colorpicker.css',
	);

	function __construct($args)
	{
		parent::__construct($args);

	}

	function build()
	{
		$id = $this->getInputId();


		$html = "";

		$html .= '<div style="position:relative;height:36px" class="form-widget">';

		$html .= JDom::_('html.form.input.text', array_merge($this->options, array(
			'size' => 6,
			'styles' => array(
								'position'=>'absolute',
								'left'=> '40px'
							)
		)));

		$html .=		'<div id="__' . $id . '_pick" class="colorpicker_pick">' .
						'<div style="background-color: #' . $this->dataValue . '">' .
						'</div>' .
					'</div>' .
				'</div>';

		return $html;
	}


	function buildJS()
	{
		$id = $this->getInputId();

		$script = 'jQuery("#__' . $id . '_pick").ColorPicker({' .

				'onSubmit: function(hsb, hex, rgb, el) {
					jQuery("#' . $id . '").val(hex);
					jQuery(el).ColorPickerHide();
					jQuery("#__' . $id . '_pick div").css("backgroundColor", "#" + hex)
				},
				onShow: function (colpkr) {
					jQuery(colpkr).fadeIn(200);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(200);
					return false;
				},
				onBeforeShow: function () {
					jQuery(this).ColorPickerSetColor(jQuery("#' . $id . '").val());
				}' .


				'});';


		$this->addScriptInline($script, true);
	}


}