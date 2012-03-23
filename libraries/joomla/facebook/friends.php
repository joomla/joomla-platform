<?php

/**
 * @package     Joomla.Platform
 * @subpackage  Facebook
 */


defined('JPATH_PLATFORM') or die();


/**
 * Facebook API Friends class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Facebook
 */

class JFacebookFriends extends JFacebookObject
{
	/**
	 * @var		array	An array of Facebook friends' names.
	 */
	protected $names;
	
	/**
	 * @var		array	An array of Facebook friends' ids.
	 */
	protected $ids;
	
	/**
	 * Method to get the friendlist for the specified user.
	 *
	 * @param   mixed		$user        	Either an integer containing the user ID or a string containing the username.
	 *
	 * @param   string	$access_token	The Facebook access token.
	 */
	public function getFriendList($user, $access_token)
	{
		$username = '/' . $user;
		$token = '?access_token=' . $access_token;
	
	    // Build the request path.
	    $path = $username . '/friends' . $token;
	
	    // Send the request.
	    $response = $this->sendRequest($path);
	    
	    // Save names and ids.
	    for ($i=0; $i<sizeof($response); $i++) {
	    	$this->names[$i] = $response[$i]['name'];
	    	$this->ids[$i] = $response[$i]['id'];
	    }
	}
	
	
	/**
	 * Method to get the array of friends' names or ids.
	 * 
	 * @param string $string It can be either names or ids.
	 * 
	 * @return	array
	 */
	public function __get($string)
	{
		if($string == 'names')
			return $this->names;
		if($string == 'ids')
			return $this->ids;
	}

}