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

class JDomHtml extends JDom
{
	var $level = 1; 			//Namespace position
	var $fallback = 'view';		//Used for default

	var $canEmbed = false;

	var $classes = array();
	var $styles = array();
	var $selectors = array();

	var $submitEventName;

	var $domClass;
	var $domId;
	var $route;


	var $allowWrapLink = true;	// Allow embedding for links (default: true)


	/*
	 * Constuctor
	 *
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 */
	function __construct($args)
	{
		parent::__construct($args);

		$this->arg('styles', 		null, $args);
		$this->arg('domClass', 		null, $args);
		$this->arg('domId', 		null, $args);
		$this->arg('selectors', 	null, $args);
		$this->arg('linkSelectors', null, $args);
		$this->arg('route', 		null, $args);
		$this->arg('submitEventName', 	null, $args);


		if ($this->submitEventName)
		{
			$this->addSelector($this->submitEventName, $this->getSubmitAction());
		}
	}



	function addStyle($property, $value)
	{
		$this->styles[$property] = $value;
	}

	function getStyles()
	{
		$styleStr = "";

		foreach($this->styles as $property => $value)
			$styleStr .= $property . ':' . $value . ';';

		return $styleStr;
	}

	function addSelector($key, $value)
	{
		$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		
		if (!isset($this->selectors))
			$this->selectors = array();

		if (!in_array($key, array_keys($this->selectors)))
			$this->selectors[$key] = "";

		$this->selectors[$key] .= $value;
	}

	function buildDomStyles()
	{
		$styleStr = $this->getStyles();
		if (trim($styleStr) == "")
			return '';

		return ' style="' . $styleStr . '"';
	}

	function addClass($class)
	{
		if (!in_array($class, $this->classes))
			$this->classes[] = $class;
	}

	function getDomClass()
	{
		//Reformat css classes
		/*
		 * Accepts
		 * 	[my_class,myclass]
		 * 	"my_class,myclass"
		 * 	"my_class, myclass"
		 * 	"my_class myclass"
		 * 	"my_class  myclass"
		 */


		$classes = $this->domClass;
		if (!is_array($classes))
		{
			//Trim spaces
			$classes = preg_replace("/\s+/", " ", $classes);

			$classes = explode(' ', $classes);
		}

		if (count($classes))
		foreach($classes as $class)
			$this->addClass($class);


		return implode(' ', $this->classes);


	}

	function buildDomClass()
	{
		$class = $this->getDomClass();
		if (!$class)
			return '';

		return ' class="' . $class . '"';
	}


	function buildSelectors($selectors = null)
	{

		if (!$selectors && isset($this->selectors))
			$selectors = $this->selectors;
		else
			return;

		if (is_string($selectors))
			return ' ' . $selectors;

		$html = "";

		if ($selectors)
			foreach($selectors as $key => $value)
			{
				$html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
			}

		return $html;
	}

	function getSubmitAction()
	{
		return "submitbutton();";
	}

	function createRoute()
	{
		if (!isset($this->route))
			return;

		$jinput = new JInput;
		
		
		$vars = array('option', 'view', 'layout', 'cid[]');
		$followers = array('lang', 'Itemid', 'tmpl');

		$queryVars = array();

		foreach($vars as $var)
		{
			if (isset($this->route[$var]))
				$queryVars[$var] = $this->route[$var];
			else
			{
				$value = $jinput->get($var, null, 'STRING');
				if ($value !== null)
					$queryVars[$var] = $value;
			}
		}

		foreach($followers as $follower)
		{
			$value = $jinput->get($follower, null, 'CMD');
			if ($value !== null)
				$queryVars[$follower] = $value;
		}

		$parts = array();

		if (count($queryVars))
		foreach($queryVars as $key => $value)
			$parts[] = $key . '=' . $value;

		$this->href = JRoute::_("index.php?" . implode("&", $parts), false);


	}

	function embedLink($contents)
	{
		if (!$this->allowWrapLink)
			return $contents;

		$this->createRoute();

		$html = "";

		//Automaticaly create the title
		if (isset($this->titleKey) && isset($this->dataObject) && $this->dataObject)
			$this->link_title = $this->parseKeys($this->dataObject, $this->titleKey);
		

		if ((isset($this->href) || isset($this->target) ||  isset($this->task)) && (isset($this->dataValue)) && (basename($this->dataValue) != ""))
		{
			$html .= JDom::_("html.link", array(
								'href' => (isset($this->href)?$this->href:null),
								'task' => (isset($this->task)?$this->task:null),
								'num' => (isset($this->num)?$this->num:null),
								'link_title' => (isset($this->link_title)?$this->link_title:null),
								'selectors' => (isset($this->linkSelectors)?$this->linkSelectors:null),
								'content' => $contents,
								'target' => (isset($this->target)?$this->target:null),
								'handler' => (isset($this->handler)?$this->handler:null),
								));

			return $html;
		}

		return $contents;
	}


}