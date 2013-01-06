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


class JDomHtmlGrid extends JDomHtml
{
	var $level = 2;				//Namespace position
	var $fallback = 'input';	//Used for default

	var $num;
	var $dataKey;
	var $dataObject;
	var $dataValue;
	protected $task;
	protected $ctrl;
	


	/*
	 * Constuctor
	 * 	@namespace 	: requested class
	 *  @options	: Configuration
	 *
	 * 	@dataKey	: database column name
	 * 	@dataObject	: object row (stdClass)
	 * 	@dataValue	: value  default = dataObject->dataKey
	 * 	@task		: Task to process
	 * 	@num		: Num position in list
	 * 	@ctrl		: Controller for processing tasks
	 *
	 */
	function __construct($args)
	{

		parent::__construct($args);
		$this->arg('dataKey'	, null, $args);
		$this->arg('dataObject'	, null, $args);
		$key = $this->dataKey;
		$this->arg('dataValue'	, null, $args, (($this->dataObject && $key)?(isset($this->dataObject->$key)?$this->dataObject->$key:null):null));
		$this->arg('task'		, null, $args);
		$this->arg('num'		, null, $args);
		$this->arg('ctrl'		, null, $args);

	}

	function parseVars($vars = array())
	{
		return array_merge(array(
				'STYLE'			=> $this->buildDomStyles(),
				'CLASS'			=> $this->buildDomClass(),		//With attrib name
				'CLASSES'		=> $this->getDomClass(),		// Only classes
				'SELECTORS'		=> $this->buildSelectors(),
				), $vars);
	}


}