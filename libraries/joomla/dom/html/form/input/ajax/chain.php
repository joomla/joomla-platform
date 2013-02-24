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


class JDomHtmlFormInputAjaxChain extends JDomHtmlFormInputAjax
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	var $ajaxToken;

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
	 *	@ajaxToken	: Only used to be able to raise an event when the AJAX dom is ready
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('ajaxToken'	, null, $args);

		$this->values = (isset($this->ajaxVars) && isset($this->ajaxVars['values']))?$this->ajaxVars['values']:null;

	}

	function build()
	{
		if (!$this->ajaxVars)
			return '';

		if (!isset($this->ajaxVars['values']) || !is_array($this->ajaxVars['values']))
			return;

		$selected = array_pop($this->ajaxVars['values']);

		$html = "";
		if (isset($selected) && $selected)
		{
			$options = array('vars' => $this->ajaxVars);

			if (!defined('JQUERY'))
				//DEPRECATED
				$onDomready = 'callAjax("' . implode(".", $this->ajaxContext) . '", "' . $this->ajaxWrapper . '", ' . json_encode($options) . ')';
			else
			{

				$onDomready = 'jQuery("#' . $this->ajaxWrapper . '").jdomAjax({
										"namespace":"' . implode(".", $this->ajaxContext) . '",
										"vars":' . json_encode($this->ajaxVars) . '
									});';

			}

		//Use a little trick to be able to load scripts when the AJAXed dom is ready
			$js = 	'registerCallback("' . $this->ajaxToken . '", function(){' . $onDomready . '});';
			$html .= "<script  type='text/javascript'>" . $js . "</script>";

		}
		return $html;
	}

}