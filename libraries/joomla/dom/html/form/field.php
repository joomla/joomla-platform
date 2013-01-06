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


class JDomHtmlFormField extends JDomHtmlForm
{
	var $level = 3;				//Namespace position
	var $last = true;		//This class is last call


	var $domID;
	var $label;

	var $labelSelectors;
	var $fieldSelectors;


	var $htmlFieldLabel;
	var $htmlFieldInput;


	/*
	 * Constuctor
	 * 	@namespace 		: requested class
	 *  @options		: Configuration
	 *
	 *
	 * 	@htmlFieldLabel	: rendered HTML label
	 * 	@htmlFieldInput	: rendered HTML input
	 *  @domClass		: CSS class
	 * 	@labelSelectors		: selectors for label TD
	 * 	@fieldSelectors		: selectors for field TD
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('htmlFieldLabel'		, 2, $args);
		$this->arg('htmlFieldInput'		, 3, $args);
		$this->arg('domClass'			, 4, $args);

		$defaultSelectors = array(
								'width'	=>	'140',
								'align'	=>	'right',
								'class'	=>	'key',
								);

		$this->arg('labelSelectors'		, 5, $args, $defaultSelectors);
		$this->arg('fieldSelectors'		, 6, $args);


	}

	function build()
	{

		$html = '<td'
			.	$this->buildSelectors($this->labelSelectors)
			.	'>'.LN
			.	$this->indent($this->htmlFieldLabel, 1) .LN
			.	'</td>'.LN
			.	'<td'
			.	$this->buildSelectors($this->fieldSelectors)
			.	'>'.LN
			.	$this->indent($this->htmlFieldInput, 1) .LN
			.	'</td>';


		return $html;
	}




}