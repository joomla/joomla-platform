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


class JDomHtmlGridBoolDefault extends JDomHtmlGridBool
{
	var $level = 4;			//Namespace position
	var $last = true;		//This class is last call

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@num		: Num position in list
	 *	@commandAcl : ACL rights to toggle
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->togglable = ($this->togglable?empty($this->dataValue):null);
		$ctrl = ($this->ctrl?$this->ctrl.'.':'');
		$this->task = ($this->togglable?$ctrl . 'default':null);
		
	}
	
	function build()
	{
		$html = JDom::_('html.grid.bool', array_merge($this->options, array(
											'strNO' => ($this->togglable?'JTOOLBAR_DEFAULT':'JNO'),
											'strYES' => 'JYES',
											'iconClass' => 'grid-task-icon '
													.	'icon-16-task-' . ($this->dataValue?'default':'default_no')
											)));
		return $html;
	}
}