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


class JDomHtmlGridHeaderSaveorder extends JDomHtmlGridHeader
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
	 *
	 *	@list		: List items
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('list'		, null, $args);
	}

	function build()
	{

		$dir = $this->pathToUrl($this->systemImagesDir(), true);

		$task = "saveorder";
		$rows = $this->list;

		$alt = JText::_( 'Save Order' );

		$domImage = '<div style="background-image:url(' . $dir . '/filesave.png); width:16px; height:16px; display:inline-block"></div>';

		$html = 	'<a href="javascript:saveorder('.(count( $rows )-1).', \''.$task.'\')" title="'.JText::_( 'Save Order' ).'">'
				.		$domImage
				.	'</a>';

		return $html;
	}






}