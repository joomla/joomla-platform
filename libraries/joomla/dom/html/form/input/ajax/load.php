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


class JDomHtmlFormInputAjaxLoad extends JDomHtmlFormInputAjax
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	var $assetName = 'ajax';


//Only if jQuery (see in construct)
	var $attachJs = array(
//		'ajax.js'
	);

	var $attachCss = array(
		'ajax.css'
	);


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *	@ajaxContext: Ajax context (extension.view.layout.render) extension without 'com_'
	 * 	@ajaxWrapper: Ajax Dom div wich will be filled with the result
	 * 	@ajaxVars	: Extends of override the ajax query
	 *
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('ajaxToken'	, null, $args);

		if (defined("JQUERY"))
			$this->attachJs[] = 'ajax.js';

	}

	function build()
	{

		$html = "\n" . "<div id='" . $this->ajaxWrapper . "'"
			.	($this->required?" class='ajax-required'":"")
			.	"></div>";

		$html .= LN
			.	'<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%>';


		$html .= JDom::_('html.form.input.hidden', array_merge($this->options, array(
												'dataValue' => ($this->dataValue == 0?"":$this->dataValue),
												)));

		return $html;

	}

	function buildJS()
	{

		$vars= "{}";
		if ($this->ajaxVars)
			$vars = json_encode($this->ajaxVars);


		if (defined('JQUERY'))
		{

			$script = 'jQuery("#' . $this->ajaxWrapper . '").jdomAjax({
				//	"result":"JSON",		//TO COME
				//	"data":{},				//NOT USED HERE
					"namespace":"' . implode(".", $this->ajaxContext) . '",
					"vars":' . $vars . '
				});';

			$this->addScriptInline($script, true);

			return;
		}



		//DEPRECATED

		$script = "callAjax('" . implode(".", $this->ajaxContext) . "'"
				.	", '" . $this->ajaxWrapper . "'"
				.	", {vars:" . $vars . "});";

		$js = $this->jsEmbedReady($script);


	//Deprecated : TODO
	//For retrieving the image url (spinner)
		$js .= "\n" . 'var jDomBase = "' . $this->domUrl() .'"';

		$this->addScriptInline($js);

	}


}