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


class JDomHtmlFormInputBool extends JDomHtmlFormInput
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
	 * 	@domID		: HTML id (DOM)  default=dataKey
	 *
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);



	}

	function build()
	{
		if (version_compare(JVERSION, '1.6', '<'))
		{
			$strNO = "NO";
			$strYES = "YES";
		}
		else
		{
			$strNO = "JNO";
			$strYES = "JYES";
		}


		$itemNO = new stdClass();
		$itemNO->value = '0';
		$itemNO->text = JText::_($strNO);


		$itemYES = new stdClass();
		$itemYES->value = '1';
		$itemYES->text = JText::_($strYES);

		$items = array($itemNO, $itemYES);


		$html =	JDom::_('html.form.input.radio', array_merge($this->options, array(
											'list' => $items,
											'listKey' => 'value',
											'labelKey' => 'text',
											'direction' => 'horizontal',

										)));

		return $html;
	}


}