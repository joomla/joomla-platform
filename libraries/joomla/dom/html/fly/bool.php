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


class JDomHtmlFlyBool extends JDomHtmlFly
{
	var $level = 3;			//Namespace position
	var $last = true;

	private $text;
	private $images;

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 *
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);

		$imageYES = "tick.png";
		$imageUndefined = "disabled.png";

		if (version_compare(JVERSION, '1.6', '<'))
		{
			$strYES = "YES";
			$strNO = "NO";
			$strUndefined = "";

			$imageNO = "publish_x.png";
		}
		else
		{
			$strYES = "JYES";
			$strNO = "JNO";
			$strUndefined = "";

			$imageNO = "publish_r.png";

		}

		if ($this->dataValue === null)
		{
			$text = $strUndefined;
			$image = $imageUndefined;
		}
		else if ($this->dataValue)
		{
			$text = $strYES;
			$image = $imageYES;
		}
		else
		{
			$text = $strNO;
			$image = $imageNO;
		}


		$imagesFolder = JURI::base() . $this->pathToUrl($this->systemImagesDir(), true);

		$this->image = $imagesFolder .'/'. $image;
		$this->text = $this->JText($text);

	}

	function build()
	{


		$html = '';

		$title = $this->text;

        $html .= "<img src='<%IMAGE_SOURCE%>' border='0' alt='<%ALT%>'"
			.	" title='" . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . "' />" .LN;




		return $html;
	}

	function parseVars($vars = array())
	{
		return array_merge(array(
				'IMAGE_SOURCE' 	=> $this->image,
				'ALT' 			=> htmlspecialchars($this->text, ENT_COMPAT, 'UTF-8'),

				), $vars);
	}

}