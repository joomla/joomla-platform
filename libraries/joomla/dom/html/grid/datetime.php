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


class JDomHtmlGridDatetime extends JDomHtmlGrid
{
	var $level = 3;			//Namespace position
	var $last = true;		//This class is last call

	var $format;


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@num		: Num position in list
	 *
	 *	@format		: Date format  -   default='%Y-%m-%d'
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('dateFormat' , null, $args, "%Y-%m-%d");
	}

	function build()
	{
		
		return JDom::_('html.fly.datetime', $this->options);
		
//DEPRECATED : Use fly.datetime		
		$formatedDate = "";

		if ($this->dataValue
			&& ($this->dataValue != "0000-00-00")
			&& ($this->dataValue != "00:00:00")
			&& ($this->dataValue != "0000-00-00 00:00:00"))
		{
			jimport("joomla.utilities.date");
			$date = new JDate($this->dataValue);
			$formatedDate = $date->toFormat($this->dateFormat);
		}

		$this->addClass('grid-date');

		$html = '<span <%STYLE%><%CLASS%><%SELECTORS%>>'
			.	$formatedDate
			.	'</span>';

		return $html;
	}

}