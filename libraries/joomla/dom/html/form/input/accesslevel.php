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


class JDomHtmlFormInputAccesslevel extends JDomHtmlFormInput
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
			$db = JFactory::getDBO();

			$query = 'SELECT id AS value, name AS text'
			. ' FROM #__groups'
			. ' ORDER BY id'
			;
			$db->setQuery( $query );
			$groups = $db->loadObjectList();

			$html = JHTML::_('select.genericlist',   $groups, $this->getInputName(), 'class="<%CLASSES%>" size="3"<%STYLE%>', 'value', 'text', intval( $this->dataValue ), '', 1 );

		}
		else
		{

			$html = JHtml::_('access.assetgrouplist', $this->getInputName(), $this->dataValue, 'class="<%CLASSES%>" size="3"<%STYLE%>');
		}

		$html .= LN
			.	'<%VALIDOR_ICON%>'.LN
			.	'<%MESSAGE%>';


		return $html;
	}


}