<?php

if (file_exists(dirname(__FILE__) . '/defines.php'))
{
	include_once dirname(__FILE__) . '/defines.php';
}
 
// Define some things. Doing it here instead of a file because this 
// is a super simple application.
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_PLATFORM', JPATH_BASE . '/libraries');
define('JPATH_MYWEBAPP',JPATH_BASE);
 
// Usually this will be in the framework.php file in the 
// includes folder.
require_once JPATH_PLATFORM.'/import.php';
 
// Now that you have it, use jimport to get the specific packages your application needs.
jimport('joomla.environment.uri');
jimport('joomla.utilities.date');
jimport('joomla.image.image'); 
jimport('joomla.image.filter'); 
jimport('joomla.application.web.webclient');
jimport('joomla.application.web');

 
//It's an application, so let's get the application helper. 
jimport('joomla.application.helper'); 
$client = new stdClass;
$client->name = 'mywebapp';
$client->path = JPATH_MYWEBAPP;
 
JApplicationHelper::addClientInfo($client);
 
// Instantiate the application.
// We're setting session to false because we aren't using a database 
// so there is no where to store it.
$config = Array ('session'=>false);
 
$app = JFactory::getApplication('mywebapp', $config);
 
// Render the application. This is just the name of a method you 
// create in your application.php


echo "<br />";

echo "useJimage";

?>


<h4>my current image</h4>
<img src="includes/passion-fruit-d.jpg" />
<?php $app->useJImage("includes/passion-fruit-d.jpg"); ?>
<h4>my new image</h4>
<img src="includes/hiSteve.jpg" />


<?php

//echo $app->getBrowser();

//echo $app->useJGrid();

$site = new JApplicationWeb();

$site->execute();


?>