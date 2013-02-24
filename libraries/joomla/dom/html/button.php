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


class JDomHtmlButton extends JDomHtml
{
	var $level = 2;				//Namespace position
	var $fallback = 'default';	//Used for default

	protected $href;
	protected $js;
	protected $text;
	protected $title;
	protected $target;
	protected $icon;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 *	@href		: Link of the button
	 *	@js			: Javascript for the button
	 *	@text		: Text of the button
	 *	@title		: Title of the button (default : @text)
	 *	@target		: Target of the link
	 *	@icon		: Icon of the button (Joomla class name)
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('href'		, 2, $args);
		$this->arg('js'			, 3, $args);
		$this->arg('text'		, 4, $args);
		$this->arg('title'		, 5, $args, $this->text);

		$this->arg('target'		, 6, $args);
		$this->arg('icon'		, 7, $args);


	}

	function parseVars($vars = array())
	{
		return array_merge(array(
				'TITLE' => " title=\"".htmlspecialchars($this->text)."\"",
				'HREF' => ($this->href?" href=\"" .htmlspecialchars($this->href) . "\"":""),
				'JS' => ($this->js?" onclick=\"" .htmlspecialchars($this->js) . "\"":""),
				'TARGET' => ($this->target?" target='" . $this->target . "'":""),
				'BUTTON_ICON' => $this->icon,


				), $vars);
	}


}