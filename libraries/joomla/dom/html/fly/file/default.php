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


class JDomHtmlFlyFileDefault extends JDomHtmlFlyFile
{
	var $level = 4;				//Namespace position
	var $last = true;

	var $fallback = null;


	var $allowWrapLink = false;	// Because this class in only a dispather

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
	 */
	function __construct($args)
	{

		parent::__construct($args);



	}

	function getFileExt()
	{
		$path_parts = pathinfo($this->getFileUrl());
		$ext = isset($path_parts["extension"])?$path_parts["extension"]:'';

		return strtolower($ext);
	}


	function getContent(&$type)
	{

		$type = '';

		//Dispatcher
		switch($this->getFileExt())
		{
			case 'png':
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'bmp':
				$type = 'image';
				break;


			default:
				$type = 'path';
				break;

		}


		$this->buildHref($type);


		if (!$this->thumb)
			return JDom::_('html.fly.file.path', $this->options);


		return JDom::_('html.fly.file.' . $type, $this->options);


	}

	function buildHref($type)
	{
		if ($this->target == 'download')
			$this->target = 'download';
		else if ($this->preview == 'modal')
		{
			switch($type)
			{
				case 'image':
					$this->target = 'modal';
					break;

				case 'flash':
					$this->target = 'modal';
					$this->handler = 'iframe';
					break;

				default:
					$this->target = 'download';
					break;
			}

			$this->options['target'] = $this->target;
			$this->options['handler'] = $this->handler;

		}


		if (($this->href || $this->target) && (basename($this->dataValue) != ""))
		{
			if (!$this->href)
			{
				$this->href = $this->getFileUrl();
				$this->options['href'] = $this->href;
			}


		}
	}

	function build()
	{
		$type = null;
		$html = $this->getContent($type);

		return $html;

	}


}