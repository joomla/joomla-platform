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

class JDomHtmlFlyFileImage extends JDomHtmlFlyFile
{
	var $level = 4;			//Namespace position
	var $last = true;

	var $fallback = null;		//Used for default


	protected $alt;
	protected $title;

	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 * 	@dataKey	: database field name
	 * 	@dataObject	: complete object row (stdClass or Array)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 *	@indirect	: Indirect File access
	 *	@root		: Default folder (alias : ex [DIR_TABLE_FIELD]) -> Need a parser (Cook helper)
	 *	@width		: Thumb width
	 *	@height		: Thumb height
	 *	@preview	: Preview type
	 *	@href		: Link on the file
	 *	@target		: Target of the link  ('download', '_blank', 'modal', ...)
	 *
	 *	@alt		: Meta alt
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);
		$this->arg('alt'	, null, $args);
		$this->arg('title'	, null, $args);
	}

	function build()
	{

        $pos = $this->imageInfos();

        $thumbUrl = $this->getFileUrl(true);

        $imgStyle = $this->getStyles();
       
        $html = "\n" . '<div class="img-zone" style="width:' . $pos->wrapWidth . 'px;height:' . $pos->wrapHeight . 'px; overflow:hidden;'
            .   'display:inline-block;' . $imgStyle . '"'
            .   '>';

        $html .= "\n" . '<img src="' . $thumbUrl . '"'
            .   ($this->title?' title="' . htmlspecialchars($this->title). '"':'')
            .   ($this->alt?' alt="' . htmlspecialchars($this->alt). '"':'')
            .   ' style="'
            .       ($pos->margin['top']?'margin-top:' . (int)$pos->margin['top'] . 'px;':'')
            .       ($pos->margin['bottom']?'margin-bottom:' . (int)$pos->margin['bottom'] . 'px;':'')
            .       ($pos->margin['left']?'margin-left:' . (int)$pos->margin['left'] . 'px;':'')
            .       ($pos->margin['right']?'margin-right:' . (int)$pos->margin['right'] . 'px;':'')
            .   '"'
            .   ($pos->width?' width="' . (int)$pos->width . 'px" ':'')
            .   ($pos->height?' height="' . (int)$pos->height . 'px" ':'')
            .   '/>';


        $html .= '</div>';


        return $html;
	}


	function imageInfos()
	{
		@include_once($this->extensionDir() .DS. 'classes' .DS. 'image' .DS . 'image.php');
		$imageClass = ucfirst(substr($this->getExtension(), 4)) . 'ClassFileImage';
		
		if (!class_exists($imageClass))
		{
			//DEPRECATED
			@include_once($this->extensionDir() .DS. 'classes' .DS. 'images.php'); //DEPRECATED
			$imageClass = ucfirst(substr($this->getExtension(), 4)) . 'Images';
		}
		
		if (!class_exists($imageClass))
		{
			echo('Class <strong>' . $imageClass . '<strong> not found');
			return;
		}
		
		$class = new $imageClass();
		
		$path = $this->dataValue;
		if (!preg_match("/\[.+\]/", $this->dataValue))
			$path = $this->root . $path;

		
		$filePath = $class->parsePath($path);
		$mime = $class->getMime($filePath);
		$thumb = new $imageClass($filePath, $mime);


		if ($this->attrs)
			$thumb->attrs($this->attrs);


		$thumb->width((int)$this->width);
		$thumb->height((int)$this->height);


		$info = $thumb->info();

		if (!$info)
			return;

		$margin = array(
					'top' => 0,
					'bottom' => 0,
					'left' => 0,
					'right' => 0,
					);

		$htmlPositions = new stdClass();

		$htmlPositions->width = (isset($info->w)?$info->w:null);
		$htmlPositions->height = (isset($info->h)?$info->h:null);

		if (!is_array($this->attrs) || (!in_array('fit', $this->attrs)))
		{
			if (isset($info->imagesize))
			{
				$htmlPositions->width = min($htmlPositions->width, $info->imagesize->width);
				$htmlPositions->height = min($htmlPositions->height, $info->imagesize->height);
			}

		}


		if (isset($info->resize) && isset($info->imagesize))
		{
			$w = $info->imagesize->width;
			$h = $info->imagesize->height;

			if($this->attrs)
				if (in_array('center', $this->attrs))
				{
					if ($info->w != $info->widthCanvas)
					{
						$hzMarg = $info->widthCanvas - $htmlPositions->width;
						$margin['left'] = round($hzMarg/2);
						$margin['right'] = $hzMarg - $margin['left'];
					}


					if ($info->h != $info->heightCanvas)
					{
						$vtMarg = $info->heightCanvas - $htmlPositions->height;
						$margin['top'] = round($vtMarg/2);
						$margin['bottom'] = $vtMarg - $margin['top'];
					}

				}

		}



		$htmlPositions->wrapWidth = isset($info->widthCanvas)?$info->widthCanvas:null;
		$htmlPositions->wrapHeight = isset($info->heightCanvas)?$info->heightCanvas:null;
		$htmlPositions->margin = $margin;
		$htmlPositions->scale = isset($info->scale)?$info->scale:null;

		return $htmlPositions;

	}



}