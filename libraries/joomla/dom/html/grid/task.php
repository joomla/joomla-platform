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

//HERE
class JDomHtmlGridTask extends JDomHtmlGrid
{
	var $level = 3;			//Namespace position

	var $fallback = 'task';


	protected $task;
	protected $taskIcon;
	protected $label;
	protected $view; //Deprecated here
	protected $viewType;
	protected $iconSize;
	protected $ctrl;
	protected $commandAcl;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@num		: Num position in list
	 *
	 *	@task		: Task name (used also for the icon class if taskIcon is empty)
	 *	@taskIcon	: Task icon
	 *	@label		: Button title, Label text if display='text'
	 *	@view		: Deprecated : use viewType
	 *	@viewType	: View mode (icon/text/both) default: icon
	 *	@iconSize	: Icon size in px (square) default:16px
	 *
	 *
	 *	Note : The icon is called with this class structure : icon-[SIZE]-task-[TASK]
	 *					-> You just need to specify the backgound-image:url(...)
	 *
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('ctrl'			, null, $args);
		$this->arg('task'			, null, $args);
		$this->arg('taskIcon'		, null, $args);
		$this->arg('label'			, null, $args);
		$this->arg('view'			, null, $args, 'icon');
		$this->arg('viewType'		, null, $args, $this->view);
		$this->arg('iconSize'		, null, $args, 16);
		$this->arg('commandAcl'		, null, $args);

	}

	function embedTaskLink($html)
	{
		$html = "<a style='cursor:pointer;' onclick=\"<%COMMAND%>\""
			.	" title='<%TITLE%>'>" .LN
			.	$html
			. 	"</a>" .LN;

		return $html;
	}

	function buildHtml()
	{

		$viewLabel = false;
		$viewIcon = false;

		switch($this->viewType)
		{
			case 'both':
				$viewLabel = true;
			case 'icon':
				$viewIcon = true;
				break;
			case 'text':
				$viewLabel = true;
				break;
		}

		$html = '';

		if ($viewIcon)
		{
			$html .= '<span style="width:' . $this->iconSize .'px;height:' . $this->iconSize .'px;'
				.					'background-repeat:no-repeat;background-position:center;display:inline-block"'
				.	' class="grid-task-icon <%ICON_CLASS%>">'
				.	'</span>' .LN;
		}

		if ($viewLabel)
		{
			$html .= '<span style="grid-task-label">'
				.	'<%LABEL%>'
				.	'</span>' .LN;
		}

		return $html;
	}



	function parseVars($vars = array())
	{
		return array_merge(array(
				'ICON_CLASS' 	=> 'icon-' . $this->iconSize .'-task-' . $this->getIconName(),
				'COMMAND' 		=> $this->jsCommand(),
				'LABEL' 		=> htmlspecialchars($this->JText($this->label), ENT_COMPAT, 'UTF-8'),
				'TITLE' 		=> htmlspecialchars($this->JText($this->label), ENT_COMPAT, 'UTF-8'),

				), $vars);
	}

	function getIconName()
	{
		if (!empty($this->taskIcon))
			return $this->taskIcon;

		return $this->getTaskExec();
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
		$action = "listItemTask('cb" . (int)$this->num . "', '" . $this->getTaskExec(true) . "')";

		switch ($this->getTaskExec())
		{
			case 'delete':
			case 'trash':
				$messageDelete	= $this->JText('JDOM_ALERT_ASK_BEFORE_REMOVE');
				$cmd = "javascript:if (window.confirm('" . addslashes($messageDelete) . "')){"
						. 		$action
						. 	"}";
				break;

			default:
				$cmd = "javascript:" . $action;
				break;

		}
		return $cmd;
	}


}