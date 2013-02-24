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


class JDomHtmlGridBool extends JDomHtmlGrid
{
	var $level = 3;			//Namespace position
	var $fallback = 'bool';	//Used for default
	
	var $togglable;
	var $taskYes;
	var $taskNo;
	var $toggleLabel;
	var $commandAcl;


	protected $text;
	protected $images;
	protected $image;
	protected $task;
	protected $viewType;
	protected $iconSize;


	protected $imageUndefined;
	protected $imageNO;
	protected $imageYES;
	
	protected $strUndefined;
	protected $strNO;
	protected $strYES;
	protected $imagesFolder;
	protected $iconClass;
	
	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@num		: Num position in list
	 *
	 *	@togglable	: if you want this bool execute a task on click
	 *	@commandAcl	: ACL rights to toggle
	 *	@taskYes	: task to execute when current is true
	 *	@taskNo		: task to execute when current is true
	 *	@toggleLabel: label to show in image title if togglable
	 *	@viewType	: Type of display
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('togglable'			, null, $args, false);
		$this->arg('commandAcl'			, null, $args);
		$this->arg('taskYes'			, null, $args, 'toggle_' . $this->dataKey);
		$this->arg('taskNo'				, null, $args, $this->taskYes);
		$this->arg('viewType'			, null, $args, 'icon');
		$this->arg('iconSize'			, null, $args, 16);

		

		$this->arg('iconClass'			, null, $args);
		//If iconClass is null
		$this->arg('imageUndefined'		, null, $args, 'disabled.png');
		$this->arg('imageNO'			, null, $args, 'publish_r.png');
		$this->arg('imageYES'			, null, $args, 'tick.png');
		
		$this->arg('strUndefined'		, null, $args, '');
		$this->arg('strNO'				, null, $args, 'JNO');
		$this->arg('strYES'				, null, $args, 'JYES');
		$this->arg('imagesFolder'		, null, $args);


		//Legacy
		if (!$this->ctrl)
		{
			if ($this->dataValue === null)	
				$task = $this->taskNo;
			
			else if ($this->dataValue)
				$task = $this->taskYes;
			else
				$task = $this->taskNo;
	
			$this->task = $task;			
		}

		
		if ($this->dataValue === null)
		{
			$text = $this->strUndefined;
			$image = $this->imageUndefined;
		}
		else if ($this->dataValue)
		{
			$text = $this->strYES;
			$image = $this->imageYES;
		}
		else
		{
			$text = $this->strNO;
			$image = $this->imageNO;
		}
		
		
		$this->imagesFolder = JURI::base() . $this->pathToUrl($this->systemImagesDir(), true);

		
		$this->image = $this->imagesFolder . '/' . $image;
		$this->text = $this->JText($text);
		
				
		if ($this->commandAcl && !$this->access($this->commandAcl))
			$this->task = null;
		
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
				'IMAGE_SOURCE' 	=> $this->image,
				'COMMAND' 		=> $this->jsCommand(),
				'TITLE' 		=> "Toggle value",
				'ALT' 			=> htmlspecialchars($this->text, ENT_COMPAT, 'UTF-8'),

				), $vars);
	}


	//Deprecated
	function jsCommand()
	{
		$cmd = 	"javascript:listItemTask('cb" . (int)$this->num . "', '" . $this->task . "')";
		return $cmd;
	}


}