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


@define("BR", "<br />");
@define("LN", "\n");





/*
 * JDom Framework is an abstraction between your component and the HTML renderer (of your choice)
 *
 * 	Rewrite inside the element classes files you want to change, or override them (see below)
 * 	Using JDom in your component, you'll be able to upgrade all your component DOM possibilities in seconds...
 *
 *  See documentation at www.jcook.pro
 *
 *
 *	OVERRIDES :
 * 	You can place the files you want to override wherever you prefers see the $searches array;
 *
 *	in the app site client	ie : components/com_mycomponent/dom/html/form/input/select.php
 * 	in the template			ie : templates/my_template/html/com_mycomponent/dom/html/form/input/select.php
 *  in the template view	ie : templates/my_template/html/com_mycomponent/my_view/dom/html/form/input/select.php
 *	and more ...
 *
 *	The search array defines the order of priority for overriding
 *
 *  JDom is 100% compatible for all Joomla! versions since 1.5
 *
 */
class JDom extends JObject
{
	var $path;
	var $options;
	var $app;
	var $last = false;

	/*
	 * Constuctor
	 *
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 */
	function __construct($args)
	{
		$this->arg('namespace'	, 0, $args);
		$this->arg('options'	, 1, $args);

		$this->app = JFactory::getApplication();
	}

	/*
	 * Static function to render a DOM object/input
	 *
	 */
	static function _($namespace, $options = array())
	{
		$args = func_get_args();
		$class = self::getClass($namespace, $args);

		if (!$class) return "";

		return $class->build($args);
	}

	function getClass($namespace, $args)
	{

		$domType = self::getName($namespace);
		$class = self::getInstance($args, $domType);

		return $class;
	}

	function build($args)
	{
		$class = $this->getInstance($args);

		if (!$class) return "";

		//ACL Access
		if (!$class->access())
			return "";

		$html = $class->build($args);  //Recursivity

		if ($class->last)
		{
			$html = $class->embedLink($html);

			//Assets implementations
			$class->implementAssets();
		}

		$html = $class->parse($html);   //Recursivity and parsing

		if ($class->last && $class->canEmbed)
			$class->ajaxHeader($html);	//Embed javascript and CSS in case of Ajax call



		$indent = $this->getOption('indent');
		if ($indent && ($this->level == 1))
		{
			$html = $this->indent($html, $indent);
		}

		return $html;

	}

	function implementAssets()
	{
		$this->addScript(); //DEPRECATED

		$this->attachFiles();
		$this->buildJS();		//Javascript inline
		$this->buildCSS();		//CSS inline
	}

	function attachFiles()
	{
		//Javascript
		$this->attachJsFiles();

		//CSS
		$this->attachCssFiles();
	}

	function attachJsFiles()
	{
		//Javascript
		if (!isset($this->attachJs))
			return;

		$attachJs = $this->attachJs;

		if (!is_array($attachJs))
			$attachJs = array($attachJs);

		$fileBase = ""; // dom Root
		if (isset($this->assetName) && ($this->assetName != null))
			$fileBase = 'assets' .DS. $this->assetName .DS. 'js' .DS;

		foreach($attachJs as $jsFileName)
		{
			if (preg_match("/^http/", $jsFileName))
				JFactory::getDocument()->addScript($jsFileName);
			else
				$this->addScript($fileBase . $jsFileName);
		}
	}

	function attachCssFiles()
	{
		if (!isset($this->attachCss))
			return;

		$attachCss = $this->attachCss;

		if (!is_array($attachCss))
			$attachCss = array($attachCss);

		$fileBase = ""; // dom Root
		if (isset($this->assetName) && ($this->assetName != null))
			$fileBase = 'assets' .DS. $this->assetName .DS. 'css' .DS;


		foreach($attachCss as $cssFileName)
		{
			$relativeName = $fileBase . $cssFileName;
			$this->addStyleSheet($relativeName);
		}
	}

	/*
	 * Abstract
	 */
	function buildJS()	{}

	/*
	 * Abstract
	 */
	function buildCSS()	{}

	function getInstance($args, $name = null)
	{

		if ($name)	//Static call  - Level = 0
		{
			$relativeName = $name . ".php";
			$className = "JDom" . ucfirst($name);
			$path = $name;
			$error = self::includeFile($relativeName, $className, $args[1]);

		}
		else
		{
			$name = $this->getName();
			$path = $this->path .DS. $name;


			$relativeName = $path . ".php";
			$className = get_class($this) . ucfirst($name);


			$error = $this->includeFile($relativeName, $className, $args[1]);

		}

		if ($error || !class_exists($className))
			return null;

		$class = new $className($args);
		$class->path = $path;

		return $class;
	}


	/*
	 * Search the appropriate class file, depending on context
	 *
	 */
	function includeFile($relativeName, $className, $options = array())
	{

		$file = self::searchFile($relativeName, $options);

		//Not founded
		if (!$file)
		{
			echo('<strong>' . JText::_('Not found') . '</strong> : ' . $className);
			return true;

		//DEPRECATED
		/*
			JError::setErrorHandling(E_ERROR, 'die'); //force error type to die
			$error = JError::raiseError( 500, JText::_('Unable to load JDom class : ') . $className);

			return $error;
		*/
		}

		require_once($file);
	}


	function searchFile($relativeName, $options = array())
	{
		// Defines the priority ORDER for classes FALLBACKS
		// TODO : Comment some lines, or change order depending on how you want to use this functionnality


		$jinput = new JInput;

		if (isset($options['searches']))
			$searches = $options['searches'];
		else
		{
			$searches = array(
					'template.view',		// 	Files on the view directory of the template -> Filter on particular view
					'template.component',	// 	Files on the component directory of the template -> Filter for this component
					'template',				// 	Files on the root directory of the template
					'client.view',			//	Files on the component view directory -> Search in the current client side (front or back)
					'client',				//	Files on the component root directory -> Search in the current client side (front or back)
					'front.view',			//	Files on the FRONT component view directory (Site client)
					'front',				//	Files on the FRONT component root directory (Site client)
					'back.view',			//	Files on the BACK component view directory (Administrator client)
					'back',					//	Files on the BACK component root directory (Administrator client)

					);
		}



		if (isset($options['extension']))
			$extension = $options['extension'];
		else
			$extension = $jinput->get('option', null, 'CMD');


		//View
		if (isset($options['view']))
			$view = $options['view'];
		else
			$view = $jinput->get('view', null, 'CMD');


		$app = JFactory::getApplication();
		$tmpl = $app->getTemplate();
		$tmplPath = JPATH_SITE .DS. 'templates' .DS. $tmpl .DS. 'html';

		if ($searches)
		foreach($searches as $search)
		{
			switch($search)
			{
				case 'template.view';
					$path = $tmplPath .DS. $extension .DS. $view;
					break;

				case 'template.component';
					$path = $tmplPath .DS. $extension;
					break;

				case 'template';
					$path = $tmplPath;
					break;

				case 'client.view';
					$path = JPATH_COMPONENT .DS. 'views' .DS. $view;
					break;

				case 'client';
					$path = JPATH_COMPONENT;
					break;

				case 'front.view';
					$path = JPATH_SITE .DS. 'components' .DS. $extension .DS. 'views' .DS. $view;
					break;

				case 'front';
					$path = JPATH_SITE .DS. 'components' .DS. $extension;
					break;

				case 'back.view';
					$path = JPATH_ADMINISTRATOR .DS. 'components' .DS. $extension .DS. 'views' .DS. $view;
					break;

				case 'back';
					$path = JPATH_ADMINISTRATOR .DS. 'components' .DS. $extension;
					break;


				default:
					$path = $search;		//Custom path
					break;

			}

			$completePath = $path .DS. 'dom' .DS. $relativeName;



			if (file_exists($completePath))
				return $completePath;

		}


		//Last Fallback : call a children file from the JDom called Class (First instanced)
		if (!file_exists($completePath))
		{
			$classFile = __FILE__;
			if (preg_match("/.+dom\.php$/", $classFile))
			{
				$classRoot = substr($classFile, 0, strlen($classFile) - 8) .DS ;
				$completePath = $classRoot .DS. $relativeName;

				if (file_exists($completePath))
					return $completePath;
			}
		}


		return null;


	}

	function arg($name, $i = null, $args = array(), $fallback = null)
	{
		$optionValue = $this->getOption($name);

		if ($optionValue !== null)
			$this->$name = $this->options[$name];
		else if (($i !== null) && (count($args) > $i))
			if ($args[$i] !== null)
				$this->$name = $args[$i];
			else
				$this->$name = $fallback;

		if (!isset($this->$name) && ($fallback !== null))
			$this->$name = $fallback;


		if ($optionValue)
			$this->options[$name] = $this->$name;

	}

	function isArg($varname)
	{
		if (isset($this->$varname) || (is_array($this->options) && (in_array($varname, array_keys($this->options)))))
			return true;
		else
			return false;
	}

	function getOption($name)
	{
		if (($name != 'options') && (is_array($this->options)) && (in_array($name, array_keys($this->options))))
			return $this->options[$name];

		return null;
	}

	/*
	 * Abstract
	 */
	function jsonArgs($args = array())
	{
		return json_encode($args);

	}

	function getName($namespace = null)
	{
		if ($namespace)		//Static call  - Level = 0
		{
			$parts = explode(".", $namespace);

			$name = $parts[0];
			if (!$name)
				$name = 'html'; 	//Fallback


		}
		else
		{
			$parts = explode(".", $this->namespace);
			if (count($parts) > $this->level)
				$name = $parts[$this->level];
			else
				$name = $this->fallback;

		}

		return $name;


	}

	function indent($contents, $indent)
	{
		if (is_int($indent))
		{
			$indentStr = "";
			for($i = 0 ; $i < $indent ; $i++)
				$indentStr .= "	";
		}
		else
			$indentStr = $indent;



		$lines = explode("\n", $contents);
		$indentedLines = array();

		foreach($lines as $line)
		{
			if (trim($line) != "") //Don't indent line if empty
				$line = $indentStr . $line;

			$indentedLines[] =  $line;
		}

		return implode("\n", $indentedLines);
	}

	function getExtension()
	{
		$extension = $this->getOption('extension');

		if (!$extension)
		{
			$jinput = new JInput;
			$extension = $jinput->get('option', null, 'CMD');
		}

		return $extension;
	}

	function getView()
	{
		$view = $this->getOption('view');

		if (!$view)
		{
			$jinput = new JInput;
			$view = $jinput->get('view', null, 'CMD');
		}

		return $view;
	}




	/*
	 * 	Abstract
	 */
	function parseVars()
	{
		return array();
	}

	function parse($pattern)
	{
		$vars = $this->parseVars();

		$html = $pattern;

		if (isset($vars) && count($vars))
		foreach($vars as $key => $value)
		{
			$html = preg_replace("/<%" . strtoupper($key) . "%>/", $value, $html);
		}

		return $html;

	}

	/*
	 * object	@object	: Object value source
	 * string 	@pattern : pattern composed by object keys:
	 * 					ie : "<%name%> <%surname%> <%_user_email%>" (DEPRECATED)
	 * 					ie : "{name} {surname} {_user_email}" (NEW FORMAT)
	 * 					note : theses values must be available in $object
	 */
	function parseKeys($object, $pattern)
	{
		if (is_array($pattern))
		{
			$namespace = $pattern[0];
			array_shift($pattern);
			$options['labelKey'] = null; // No recursivity

			$options = array_merge($this->options, $pattern);
			$labelKey = $options['labelKey'];

			$options['list'] = null;
			$options['dataValue'] = $this->parseKeys($object, $labelKey);

			return JDom::_($namespace, $options);
		}
		
		//Tags <% %> are deprecated use { } instead
		$tag1 = '[<,{]%?';
		$tag2 = '%?[>,}]';

		
		$matches = array();
		if (preg_match_all("/" . $tag1 . "([a-zA-Z0-9_]+:)?([a-zA-Z0-9_]+)" . $tag2 . "/", $pattern, $matches))
		{

			$label = $pattern;

			$index = 0;
			foreach($matches[0] as $match)
			{
				$key = $matches[2][$index];

				if ($type = $matches[1][$index])
				{
					//JDOM FLY DEFINE
					$type = substr($type, 0, strlen($type) - 1);

					$namespace = "html.fly." . $type;
					$options['dataValue'] = $this->parseKeys($object, $key);

					$value = JDom::_($namespace, $options);
				}
				else
				{
					$value = (isset($object->$key)?$object->$key:"");
				}
				$label = preg_replace("/" . $tag1 . "([a-zA-Z0-9_]+:)?" . $key . "" . $tag2 . "/", $value, $label);
				$index++;

			}

		}
		else
		{
			$key = $pattern;  //No patterns
			$label = (isset($object->$key)?$object->$key:"");
		}

		return $label;
	}


	/*
	 * Parse a string with JText
	 * Accepts a composed string ie : "[MY_FIRST_STRING], [MY_SECOND_STRING] : "
	 */
	function JText($text)
	{
		//Fix a little Joomla bug
		if ((strtolower($text) == 'true') || (strtolower($text) == 'false'))
			return $text;

		if (preg_match("/\[([A-Z0-9_]+)\]/", $text))
		{
			preg_match_all("/\[([A-Z0-9_]+)\]/", $text, $results);
			foreach($results[1] as $string)
			{
				$translated = JText::_($string);
				$text = preg_replace("/\[(" . $string . ")\]/", JText::_($string), $text);
			}
		}
		else
			$text = JText::_($text);

		return $text;

	}

	function addScript($assetPath = null)
	{
		if ((!$assetPath) && (!isset($this->assetName)))
			return;


		if ($assetPath)
		{
			$relativeName = $assetPath;
		}
		else if (!defined('JQUERY'))
		{
			//Deprecated with the coming of jQuery
			$name = $this->assetName;
			$relativeName = 'assets' .DS. $name . DS. 'js' .DS . $name . '.js';
		}
		else
			return;

		$jsFile = $this->searchFile($relativeName, false);
		if ($jsFile)
		{
			$doc = JFactory::getDocument();

			if (!defined('JQUERY'))
			{
				//Deprecated
				JHTML::_('behavior.framework');

				//Main JDom file is deprecated with the coming of jQuery
				//Add JDom Js main class (before)
				$jDomClass = 'assets' .DS. 'jdom.js';
				$jsFileMain = $this->searchFile($jDomClass, false);
				if ($jsFileMain)
				{
					$doc->addScript(self::pathToUrl($jsFileMain));
				}

			}

			//Loads the asset script
			$doc->addScript(self::pathToUrl($jsFile));

		}

	}

	function addStyleSheet($assetPath = null)
	{
		if ((!$assetPath) && (!isset($this->assetName)))
			return;

		if ($assetPath)
			$relativeName = $assetPath;
		else
		{
			$name = $this->assetName;
			$relativeName = 'assets' .DS. $name . DS. 'css' .DS . $name . '.css';
		}

		$cssFile = $this->searchFile($relativeName, false);
		if ($cssFile)
			JFactory::getDocument()->addStyleSheet(self::pathToUrl($cssFile));


	}

	function jsEmbedReady($script)
	{
		if (defined('JQUERY'))
		{
			//Using jQuery
			$js = "jQuery(document).ready(function(){" . LN;
			$js .= $this->indent($script, 1);
			$js .= LN. "});";

			return $js;
		}


		//MooTools fallback
		$js = "window.addEvent('domready', function(){" . LN;
		$js .= $this->indent($script, 1);
		$js .= LN. "});";

		return $js;

	}

	function addScriptInline($script, $embedReady = false)
	{
		if ($embedReady)
			$script = $this->jsEmbedReady($script);

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);
	}

	function ajaxHeader(&$html)
	{
		$jinput = new JInput;
		$layout = $jinput->get('layout', null, 'CMD');
		if ($layout == 'ajax')
		{
			$jsScript = $this->ajaxCallbackOnLoad();
			$css = $this->ajaxAttachCss();
			$html = $css . $jsScript . $html;
		}
	}

	function ajaxAttachCss()
	{
		$document = JFactory::getDocument();

		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		$tagEnd = ' />';
		$buffer = '';


		// Generate stylesheet links
		foreach ($document->_styleSheets as $strSrc => $strAttr)
		{
			$buffer .= $tab . '<link rel="stylesheet" href="' . $strSrc . '" type="' . $strAttr['mime'] . '"';
			if (!is_null($strAttr['media']))
			{
				$buffer .= ' media="' . $strAttr['media'] . '" ';
			}
			if ($temp = JArrayHelper::toString($strAttr['attribs']))
			{
				$buffer .= ' ' . $temp;
			}
			$buffer .= $tagEnd . $lnEnd;
		}

		// Generate stylesheet declarations
		foreach ($document->_style as $type => $content)
		{
			$buffer .= $tab . '<style type="' . $type . '">' . $lnEnd;

			// This is for full XHTML support.
			if ($document->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . '<![CDATA[' . $lnEnd;
			}

			$buffer .= $content . $lnEnd;

			// See above note
			if ($document->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . ']]>' . $lnEnd;
			}
			$buffer .= $tab . '</style>' . $lnEnd;
		}

		return $buffer;
	}

	/**
	 * Embed the scripts inside a temporary function called after the domReady event
	 */
	function ajaxCallbackOnLoad()
	{
		$jinput = new JInput;
		$token = $jinput->get('token', null, 'CMD');
		if (!$token)
			return;

		$jsScript = "";

		$document = JFactory::getDocument();

		$type = 'text/javascript';
		$jsScript .= '<script type="' . $type . '">';
		// This is for full XHTML support.
		if ($document->_mime != 'text/html')
		{
			$jsScript .= '<![CDATA[';
		}


	// Generate script file links
		$files = array();
		foreach ($document->_scripts as $strSrc => $strAttr)
			$files[] = $strSrc;

		if (count($files))
		{
			$jsScript .= "ajaxLinkOnLoad['_js_" . $token . "'] = ['"
						.	implode("', '", $files)
						.	"'];" ."\n";
		}


	// Generate script declarations
		$scripts = array();
		foreach ($document->_script as $type => $content)
			$scripts[] = $content;

		//Embed the scripts
		$jsScript .= "ajaxCallbackOnLoad['_" . $token . "'] = function(){"
				.	implode("\n", $scripts)
				.	"};" ."\n";

		// See above note
		if ($document->_mime != 'text/html')
		{
			$jsScript .=  ']]>';
		}
		$jsScript .= '</script>';

		return $jsScript;
	}

	function pathToUrl($path, $raw = false)
	{
		$JPATH_SITE = JPATH_SITE;
		$path = str_replace("\\", "/", $path);
		$JPATH_SITE = str_replace("\\", "/", $JPATH_SITE);

		$escaped = preg_replace("/\//", "\/", $JPATH_SITE);
		$relUrl = preg_replace("/^" . $escaped . "/", "", $path);

		if ($raw)
			return $relUrl;


		return JURI::root(true) . $relUrl;
	}

	function strftime2regex($format)
	{
		$d2 = "(\d{2})";
		$d4 = "([1-9]\d{3})";

		$patterns =
array(	"\\", 	"/", 	"#",	"!", 	"^", "$", "(", ")", "[", "]", "{", "}", "|", "?", "+", "*", ".",
		"%Y", 	"%y",	"%m",	"%d", 	"%H", 	"%M", 	"%S", 	" ");
		$replacements =
array(	"\\", "\/", 	"\#",	"\!", 	"\^", "$", "\(", "\)", "\[", "\]", "\{", "\}", "\|", "\?", "\+", "\*", "\.",
		$d4,	$d2,	$d2,	$d2,	$d2,	$d2,	$d2,	"\s");

		$regex = str_replace($patterns, $replacements, $format);

		return "/^" . $regex . "$/";
	}


	function jVersion($ver, $comp = '>=')
	{
		return version_compare(JVERSION, $ver, $comp);
	}

	function adminTemplate()
	{
		 //TODO

		if ($this->jVersion('1.6'))
			return 'bluestork';
		else
			return 'khepri';
	}

	function systemImagesDir()
	{
		if ($this->jVersion('1.6'))
			$dir = 'templates' .DS. $this->adminTemplate() .DS. 'images' .DS. 'admin';
		else
			$dir = "images";

		if ($this->app->isSite())
			$dir = "administrator" .DS . $dir;

		return $dir;
	}


	function extensionDir()
	{
		return JPATH_ADMINISTRATOR .DS. 'components' .DS. $this->getExtension();
	}

	function domUrl()
	{
		$url = self::pathToUrl($this->extensionDir() . '/dom');
		return $url;
	}

	function assetImage($imageName, $assetName = null)
	{
		if (!$assetName)
			$assetName = $this->getName();

		$urlImage = self::domUrl().'/assets/'. $assetName . '/images/' . $imageName;

		return $urlImage;
	}

	function htmlAssetSpriteImage($urlImage, $d)
	{
		$image = "<div style='background-image: url(" . $urlImage . ");"
			.	"width:" . $d->w . "px;"
			.	"height:" . $d->h . "px;"
			.	"background-position:-" . $d->x . "px -" . $d->y . "px;'>"
			.	"</div>";

		return $image;
	}

	function accessTask($task)
	{
		$aclAccess = $this->getOption('aclAccess');

		if ($aclAccess)
			return $this->access();

		switch ($task)
		{
			case 'new':
				$access = 'core.create';
				break;

			case 'edit':
			case 'save':
			case 'apply':
				$access = 'core.edit';
				break;

			case 'publish':
			case 'unpublish':
			case 'trash':
			case 'default_it':
				$access = 'core.edit.state';
				break;

			case 'delete':
			case 'empty_trash':
				$access = 'core.delete';
				break;

			case 'config':
				$access = 'core.manage';
				break;

			default:
				return true;
				break;

		}

		return $this->access($access);
	}


	function access($aclAccess = null)
	{
		if (!$aclAccess)
			$aclAccess = $this->getOption('aclAccess');

		if (!$aclAccess)
			return true;

		if (!is_array($aclAccess))
			$aclAccess = array($aclAccess);

		$aclAsset = $this->getOption('aclAsset');
		if (!$aclAsset)
			$aclAsset = $this->getExtension();



		$user 	= JFactory::getUser();

		$authorize = false;
		foreach($aclAccess as $acl)
		{
			if (version_compare(JVERSION, '1.6', '<'))
				$auth = $user->authorize($aclAsset, $acl);
			else
				$auth = $user->authorise($acl, $aclAsset);


			if ($auth)
				$authorize = true;

		}



		return $authorize;
	}
}


