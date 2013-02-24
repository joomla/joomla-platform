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


class JDomHtmlFlyImage extends JDomHtmlFly
{
	var $level = 3;			//Namespace position
	var $last = true;

	protected $width;
	protected $height;
	protected $markup;
	protected $src;
	protected $indirect;
	protected $root;
	protected $title;
	protected $alt;
	

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 *
	 * 	@width		: Width of the image
	 *	@height		: Height of the image
	 *  @markup		: Image HTML Markup (div, span, img)
	 *  @src		: Source of the image (can be empty if domClass defined
	 *  @indirect	: Indirect File access
	 *  @root		: root directory (used in indirect file access)
	 *  @title		: Title text for this image
	 *  @alt		: Alternative text for this image (default : title)
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('width' 		, null, $args);
		$this->arg('height' 	, null, $args);
		$this->arg('markup' 	, null, $args, 'img');
		$this->arg('src' 		, null, $args);
		$this->arg('indirect'	, null, $args);
		$this->arg('root'		, null, $args);
		$this->arg('title'		, null, $args);
		$this->arg('alt' 		, null, $args, $this->title);


		if (!$this->width || !$this->height)
			$this->markup = 'img';
		
		if ($this->indirect)
		{
			$this->indirectUrl();
		}
		
		if ($this->markup == 'img')
		{
			if ($this->src)
				$this->addSelector('src', $this->src);
			
			if ($this->width)
				$this->addSelector('width', $this->width . 'px');

			if ($this->height)
				$this->addSelector('height', $this->height . 'px');
				
			if ($this->alt)
				$this->addSelector('alt', $this->JText($this->alt));
		}
		else
		{				
			if ($this->src)
				$this->addStyle('background-image', 'url(' . $this->src . ')');	

			if ($this->width)
				$this->addStyle('width', $this->width . 'px');
	
			if ($this->height)
				$this->addStyle('height', $this->height . 'px');

			$this->addStyle('background-repeat', 'no-repeat');	
			$this->addStyle('background-position', 'center');
			$this->addStyle('display', 'inline-block');
		}

		if ($this->title)
			$this->addSelector('title', $this->JText($this->title));

	}
	
	function indirectUrl()
	{
		$indirectUrl = "";
		if ($this->indirect)
		{
			$path = $this->dataValue;
			if (!empty($this->url))
				$path = $this->url;
			
			if (!preg_match("/\[.+\]/", $this->dataValue))
				$path = $this->root . $path;

			$indirectUrl = JURI::base(true) . "/index.php?option=" . $this->getExtension()
						. "&task=file&path=" . $path;

			if ($this->width && $this->height)
				$indirectUrl .= "&size=" . $this->width ."x". $this->height;
			
			$this->src = $indirectUrl;
		}
	}

	function build()
	{
		$html = '';
		
		if ($this->markup == 'img')
			$html = '<img<%CLASS%><%STYLE%><%SELECTORS%>/>';
		else
			$html = '<<%MARKUP%><%CLASS%><%STYLE%><%SELECTORS%>>' . $html . '</<%MARKUP%>>';

		return $html;
	}

}