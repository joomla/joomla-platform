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


class JDomHtmlGridCheckedout extends JDomHtmlGrid
{
	var $level = 3;			//Namespace position
	var $last = true;		//This class is last call


	protected $keyCheckedOut = null;
	protected $keyCheckedOutTime = null;
	protected $keyEditor = null;
	protected $user = null;
	protected $allow = null;

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@num		: Num position in list
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('dataKey'			, null, $args, "cid");
		$this->arg('keyCheckedOut'		, null, $args, "checked_out");
		$this->arg('keyEditor'			, null, $args, "_checked_out_name");
		$this->arg('keyCheckedOutTime'	, null, $args, "checked_out_time");

		$this->arg('allow'				, null, $args, false);
	}

	function build()
	{
		$html = '';

		$dataKey = $this->dataKey;
		$keyChecked = $this->keyCheckedOut;
		if (property_exists($this->dataObject, $keyChecked))
			$this->dataValue = $this->dataObject->$keyChecked;

		$isLocked = (!empty($this->dataValue) && ($this->dataValue != JFactory::getUser()->get('id')));
		if ($isLocked)
			$html .= $this->checkedOut();

		if (!$isLocked || $this->allow)
			$html .= JHtml::_('grid.id', $this->num, $this->dataObject->id, ($isLocked && !$this->allow), $dataKey);


		return $html;
	}

	function checkedOut($tip = true)
	{
		$hover = '';

		if ($tip)
		{
			$keyTime = $this->keyCheckedOutTime;
			$checked_out_time = $this->dataObject->$keyTime;

			$text = '';
			$keyEditor = $this->keyEditor;
			if (isset($this->dataObject->$keyEditor))
			{
				$editor = $this->dataObject->$keyEditor;
				$text .= addslashes(htmlspecialchars($editor, ENT_COMPAT, 'UTF-8'));
			}


			$date = JHtml::_('date', $checked_out_time, JText::_('DATE_FORMAT_LC1'));
			$time = JHtml::_('date', $checked_out_time, 'H:i');

			$hover = '<span class="editlinktip hasTip" title="' . JText::_('JLIB_HTML_CHECKED_OUT') . '::' . $text . '<br />' . $date . '<br />'
				. $time . '">';
		}

		$checked = $hover . JHtml::_('image', 'admin/checked_out.png', null, null, true) . '</span>';

		return $checked;
	}

}