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


class JDomHtmlToolbarButton extends JDomHtmlToolbar
{
	var $level = 3;
	var $fallback = 'standard';


	var $item;

	var $name;
	var $text;
	var $task;
	var $message;
	var $list;

	var $align;

	/*
	 * Constuctor
	 * 	@namespace 	: Requested class
	 *  @options	: Parameters
	 *  //@bar		: Joomla Toolbar
	 *
	 *
	 *  @item		: Joomla Toolbar Item arguments (array)  (Overwrite $bar parameter)
	 *	@align		: Item alignement  (float)
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);
		$this->arg('item'	, 2, $args);
		$this->arg('align'	, 3, $args);

	}

	function parseVars($vars = array())
	{
		switch($this->align)
		{
			case 'left':
			case 'right':
				$alignStyle = "float: " . $this->align . ";";
				break;

			case 'center':
				$alignStyle = "display: inline-block;";
				break;
		}



		return array_merge(array(
				'LI_STYLE' 		=> "list-style:none; " . $alignStyle,
				'LI_ID' 		=> 'toolbar-' . $this->task,
				'BUTTON_STYLE' 	=> 'cursor:pointer',
				'COMMAND' 		=> $this->jsCommand(),
				'ICON_CLASS' 	=> 'icon-16 icon-16-task-' . $this->name,
				'TEXT' 			=> $this->JText($this->text)
				), $vars);
	}

	function getTaskExec($ctrl = false)
	{

		//Get the task behind the controller alias (Joomla 2.5)
		if (!$task = $this->task)
			return;

		$ctrlName = "";

		$parts = explode(".", $task);
		$len = count($parts);
		$taskName = $parts[$len - 1]; //Last
		if ($len > 1)
			$ctrlName = $parts[0];


		if ($ctrl)
			return $ctrlName . "." . $taskName;

		return $taskName;
	}

	function jsCommand()
	{
		$task = $this->getTaskExec();
		$taskCtrl = $this->getTaskExec(true);




		if (version_compare(JVERSION, "1.6", "<"))//1.5.x
		{
			$jsSubmitForm = "submitform";
			$messageList	= JText::sprintf('PLEASE MAKE A SELECTION FROM THE LIST TO', $task);
		}
		else
		{
			$jsSubmitForm = "Joomla.submitform";
			$messageList	= JText::sprintf('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST', $task);

		}

		$submitAction = "return " . $jsSubmitForm . "('" . $taskCtrl . "');";





		switch ($this->getTaskExec())
		{
			case 'save':
			case 'save2new':
			case 'save2copy':
			case 'apply':
				if (!defined('JQUERY'))
					$submitAction = "return document.formvalidator.submitform(document.adminForm, '" . $taskCtrl . "', function(pressbutton){return " . $jsSubmitForm . "(pressbutton);});";
				$cmd = "javascript:" . $submitAction;
				break;


			case 'delete':
				if ($this->message)
					$messageDelete = $this->message;
				else
					$messageDelete	= $this->JText('JDOM_ALERT_ASK_BEFORE_REMOVE');

				if ($this->list)
				{
					$cmd = 	"javascript:"
						.	"if (document.adminForm.boxchecked.value == 0){"
						.		"alert('" . addslashes($messageList)  ."');"
						.	"}else{"
						.		"if (window.confirm('" . addslashes($messageDelete) . "')){"
						.			$submitAction
						.		"}"
						.	"}";
				}
				else
					$cmd = "javascript:if (window.confirm('" . addslashes($messageDelete) . "')){"
						. 		$submitAction
						. 	"}";
				break;

			default:

				if ($this->list) {
					$cmd = 	"javascript:if (document.adminForm.boxchecked.value==0){"
						.		"alert('" . addslashes($messageList)  ."');"
						.	"}else{"
						. 		$submitAction
						. 	"}";
				}
				else {
					$cmd = "javascript:" . $submitAction;
				}

				break;

		}

		return $cmd;
	}


}