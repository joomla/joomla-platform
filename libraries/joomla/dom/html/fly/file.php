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

class JDomHtmlFlyFile extends JDomHtmlFly
{
	var $level = 3;			//Namespace position
	var $fallback = 'default';		//Used for default


	protected $indirect;
	protected $width;
	protected $height;
	protected $root;
	protected $attrs;

	protected $cid;
	protected $view;
	protected $thumb;




	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 *
	 *	@indirect	: Indirect File access
	 *	@root		: Default folder (alias : ex [DIR_TABLE_FIELD]) -> Need a parser (Cook helper)
	 *	@width		: Thumb width
	 *	@height		: Thumb height
	 *	@attrs		: File attributes ('crop', 'fit', 'center', 'quality')
	 *
	 *	-> Token - db mode : Require the cid of the image to show it.
	 *	@cid		: Cid of the image item (Token DB file check)
	 *  @view		: Table from which this image is from
	 */
	function __construct($args)
	{

		parent::__construct($args);


		$this->arg('indirect'	, null, $args);
		$this->arg('root'		, null, $args);
		$this->arg('width'		, null, $args, 0);
		$this->arg('height'		, null, $args, 0);
		$this->arg('attrs'		, null, $args);

		$this->arg('cid'		, null, $args);
		$this->arg('view'		, null, $args);

		$this->thumb = ($this->width || $this->height);

	}

	function getFileUrl($thumb = false)
	{
		if ($this->indirect)
			return $this->getIndirectUrl($thumb);
		else
			return $this->root .DS. $this->dataValue;
	}

	function getIndirectUrl($thumb)
	{
		$indirectUrl = "";

		$path = $this->dataValue;
		if (!preg_match("/\[.+\]/", $this->dataValue))
			$path = $this->root . $path;

		$indirectUrl = JURI::base(true) . "/index.php?option=" . $this->getExtension()
					. "&task=file";

		if (!$thumb && ($this->target == 'download'))
			$indirectUrl .= "&action=download";

		if($this->cid)
			$indirectUrl .= "&cid=" . $this->cid;

		if($this->view)
			$indirectUrl .= "&view=" . $this->view;

		if ($thumb && $this->thumb)
		{

			$w = (int)$this->width;
			$h = (int)$this->height;

			if ($w || $h)
				$indirectUrl .= "&size=" . $w ."x". $h;

		}

		if ($thumb && $this->attrs)
			$indirectUrl .= "&attrs=" . implode(",", $this->attrs);


		$indirectUrl .= "&path=" . $path;

		return $indirectUrl;

	}


}