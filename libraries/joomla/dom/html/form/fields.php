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


class JDomHtmlFormFields extends JDomHtmlForm
{
	var $level = 3;				//Namespace position
	var $last = true;		//This class is last call

	var $domID;
	var $label;



	/*
	 * Constuctor
	 * 	@namespace 		: requested class
	 *  @options		: Configuration
	 *
	 *
	 *  @fields			: JDom Objects (use JDom::get('', ...))
	 * 	@selectors		: raw selectors
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('fields'		, 2, $args);
		$this->arg('selectors'	, 3, $args);


	}

	function build()
	{
		$html = '<table>';

		if ($this->fields)
		foreach($this->fields as $htmlField)
		{
			$html .= LN
				.	'<tr'
				.	$this->buildDomClass()
				.	'>' .LN
				.	$this->indent($htmlField, 1) .LN
				.	'</tr>'.LN;

		}

		$html .= '</table';

		return $html;
	}




}