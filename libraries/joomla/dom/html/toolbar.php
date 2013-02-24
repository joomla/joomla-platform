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

class JDomHtmlToolbar extends JDomHtml
{
	var $level = 2;				//Namespace position
	var $fallback = 'default';	//Used for default


	var $bar;
	var $items;


	/*
	 * Constuctor
	 * 	@namespace 	: Requested class
	 *  @options	: Parameters
	 *
	 *	@bar		: Joomla Toolbar
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('bar'	, 2, $args);

		if (is_object($this->bar) && get_class($this->bar) == 'JToolBar')
		{
			if (version_compare(JVERSION, '1.6', '<'))
				$items = $this->bar->_bar;
			else
				$items = $this->bar->getItems();

		}
		else
			$items = $this->bar;

		$this->items = $items;
	}






}