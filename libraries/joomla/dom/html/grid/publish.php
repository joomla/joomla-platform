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


class JDomHtmlGridPublish extends JDomHtmlGrid
{
	var $level = 3;			//Namespace position
	var $last = true;		//This class is last call



	protected $ctrl;
	protected $togglable;
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
	 *	@togglable	: if you want this bool execute a task on click
	 *	@commandAcl : ACL rights to toggle
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('ctrl'			, null, $args);
		$this->arg('togglable'		, null, $args, true);
		$this->arg('commandAcl'		, null, $args, 'core.edit.state');
	}

	function build()
	{
		$html = '';

		$ctrl = ($this->ctrl?$this->ctrl.'.':'');
		$html = JDom::_('html.grid.bool', array_merge($this->options, array(
																	'togglable' => $this->togglable,
																	'taskYes' => $ctrl . 'unpublish',
																	'taskNo' => $ctrl . 'publish',
																	'commandAcl' => $this->commandAcl,
																	)));
		return $html;
	}

}