<?php

/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// no direct access
defined('JPATH_PLATFORM') or die;
 
/**
 * Joomla! Application class
 *
 * Provide many supporting API functions
 *
 * @package		Joomla.MyWebApp
 * @subpackage	Application
 */



final class JMyWebApp extends JApplication
{

	
 
	/**
	 * Display the application.
	 */
	public function render()
	{
	
		jimport('joomla.application.web');
		
		echo '<h1>My Web Application</h1>';
 
		echo 'The current URL is '.JUri::current().'<br/>';
		echo 'The date is '. JFactory::getDate('now');
		
		JApplicationWeb::setBody(self::useJGrid());
		
 
	}
	
	public  function useJImage($path)
	{
		
		
		
		$myImage = new JImage;
		
		$myImage->loadFile($path);
		
		$newImage = $myImage->rotate("90");
				
		$newImage->toFile("includes/hiSteve.jpg");
		
	}
	
	public function getBrowser()
	{
		
		$client = new JWebClient;
		
		if ($client->browser == JWebClient::CHROME) {
			return "chrome";		
		}
		else if ($client->browser == JWebClient::FIREFOX) {
			return "firefox";
		}
	}
	
	public function useJGrid() {
	
		
		//import the JGrid class
		jimport('joomla.html.grid');
		
		
		// instantiate new instance of JGrid class
		$myTable = new JGrid;
		
		//create new array variable of HTML table parameters. We send this to the new table as an array
		//$myTableAttr = array("width" => "200px", "border" => "1px solid #000", "cellpadding"=>"2", "cellspacing"=>"2");
	
		//create new table and send it our attributes.	
		$myTable->setTableOptions(array("class"=>"my_table", "border"=>"1px solid #000"),false);
		
		//add two columns
		$myTable->addColumn('name')->addColumn('myuser');
		
		//create table header
		$myTable->addRow(array(), 1)
			->setRowCell('name', "My Name")
			->setRowCell('myuser', "My User");
		
		
		// and now add rows and cells
		$myTable->addRow(array())
			->setRowCell('name', "Chad Windnagle")
			->setRowCell('myuser', "drmmr736");

		// and here we just return the table as a string. That can be called through this function. 	
		return $myTable->toString();
		
	}
}

?>