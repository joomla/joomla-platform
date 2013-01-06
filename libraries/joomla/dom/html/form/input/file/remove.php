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


class JDomHtmlFormInputFileRemove extends JDomHtmlFormInputFile
{
	var $level = 5;			//Namespace position
	var $last = true;		//This class is last call

	protected $actions;

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
	 *
	 *	@actions	: List of the possible actions
	 */
	function __construct($args)
	{
		parent::__construct($args);
		$this->arg('actions'		, 9, $args, array('remove', 'thumbs', 'trash', 'delete'));
	}

	function build()
	{
		if (trim($this->dataValue) == "")
			return "";

		$html = "";

		$html = "<div>"
			.	"<span>" . $this->JText("JDOM_FILE_REMOVE_REMOVE"). " : </span>";

		$list = array();
		$list[] = array('value' => '', 'text' => "");

		if (in_array('remove', $this->actions))
			$list[] = array('value' => 'remove', 'text' => $this->JText("JDOM_FILE_REMOVE_REMOVE_UNLINK"));

		if (in_array('thumbs', $this->actions))
			$list[] = array('value' => 'thumbs', 'text' => $this->JText("JDOM_FILE_REMOVE_THUMBS_ONLY"));

		if (in_array('trash', $this->actions))
			$list[] = array('value' => 'trash', 'text' => $this->JText("JDOM_FILE_REMOVE_TRASH"));

		if (in_array('delete', $this->actions))
			$list[] = array('value' => 'delete', 'text' => $this->JText("JDOM_FILE_REMOVE_DELETE"));


		$html .= JDom::_('html.form.input.select', array(
													'dataValue' => $this->dataValue,
													'dataKey' => "__" . $this->dataKey . '_remove',
													'formControl' => $this->formControl,
													'list' => $list,
													'listKey' => 'value',
													'labelKey' => 'text'
													));

		$html .= JDom::_('html.form.input.hidden', array(
													'dataValue' => $this->dataValue,
													'dataKey' => "__" . $this->dataKey,
													'formControl' => $this->formControl,
													'domClass' => 'inputbox-current'
													));

		$html .= "</div>";

		return $html;
	}


}