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


class JDomHtmlPagination extends JDomHtml
{
	var $level = 2;			//Namespace position
	var $last = true;		//This class is last call

	var $pagination;
	var $showLimit;
	var $showCounter;



	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 *
	 * 	@pagination : Joomla pagination object
	 *	@showLimit	: show the selectbox to choose how many elements per page
	 *	@showCounter: show the current number of page and the total (ie: Page 1 of 4)
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$this->arg('pagination'			, 2, $args);
		$this->arg('showLimit'			, 3, $args, true);
		$this->arg('showCounter'		, 4, $args, true);

	}

	function build()
	{
		$pagination = $this->pagination;

		$app = JFactory::getApplication();

		$list = array();		//ISSET() for warning prevention
		$list['prefix']			= (isset($pagination->prefix)?$pagination->prefix:null);
		$list['limit']			= $pagination->limit;
		$list['limitstart']		= $pagination->limitstart;
		$list['total']			= $pagination->total;
		$list['limitfield']		= $pagination->getLimitBox();
		$list['pagescounter']	= $pagination->getPagesCounter();
		$list['pageslinks']		= $pagination->getPagesLinks();

		$chromePath	= JPATH_THEMES . '/' . $app->getTemplate() . '/html/pagination.php';
		if (file_exists($chromePath))
		{
			require_once $chromePath;
			if (function_exists('pagination_list_footer')) {
				return pagination_list_footer($list);
			}
		}


		$html = "<div class=\"list-footer-pagination\">\n";

		if ($this->showLimit)
		{
			if (version_compare(JVERSION, "1.6", "<"))
				$langDisplayNum = $this->JText('DISPLAY NUM');	//1.5
			else
				$langDisplayNum = $this->JText('JGLOBAL_DISPLAY_NUM'); //1.6 or Later

			$html .= "\n<div class=\"limit\">". $langDisplayNum .$list['limitfield']."</div>";

		}




		$html .= $list['pageslinks'];

		if ($this->showCounter)
			$html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

		$html .= "\n<input type=\"hidden\" name=\"" . $list['prefix'] . "limitstart\" value=\"".$list['limitstart']."\" />";
		$html .= "\n</div>";



		return $html;
	}

}