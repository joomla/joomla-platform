<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.object');

/**
 * JOrmClass Option
 *
 * Options Class based on Mootools Class.Extras Options
 *
 * @package     Joomla.Platform
 * @subpackage  Class
 * @since       11.1
 * @tutorial	Joomla.Platform/jclassoption.cls
 * @link		http://docs.joomla.org/JClassOption
 */
class JOrmClassOptions extends JObject
{
	private $_default = array();
	
	/**
	 * Constructor
	 * 
	 * Create recived options array properties and set by default settings
	 * 
	 * @param array $options
	 * 
	 * @since 11.1
	 */
	public function __construct(array $options = array())
	{
		//set default config
		$this->_default = $options;
	}
	
	/**
	 * Merge options with default array
	 * 
	 * @param array $options
	 * 
	 * @param array $comparedata array to compare, by default will get default settings when instance is created
	 * 
	 * @return array
	 * 
	 * @since 11.1
	 */
	public function santizeOptions($options,$comparedata = null)
	{
		if ( empty($this->_default) )
		{
			return $options;
		}
			
		if ( is_null($comparedata) )
		{
			$comparedata = $this->_default;
		}
		
		foreach ($comparedata as $default_key => $default_value)
		{
			if ( array_key_exists($default_key, $options) === false )
			{
				$options[$default_key] = $default_value;
			}
			
			if ( is_array($comparedata[$default_key]) && !empty($options[$default_key]) )
			{
				$options[$default_key] = $this->santizeOptions($options[$default_key],$this->_default[$default_key]);
			}
		}
		
		return $options;
	}
	
	/**
	 * Set options array
	 * 
	 * @param array $options 
	 * 
	 * @return self instance
	 * 
	 * @since 11.1
	 */
	public function setOptions(array $options)
	{
		$options = $this->santizeOptions($options);
		
		foreach ($options as $option_key => $option_value)
		{
			$this->set($option_key,$option_value);
		}
		
		return $this;
	}
	
	/**
	 * Check if property exists
	 * 
	 * @param $property
	 * 
	 * @return Returns TRUE if the property exists, FALSE if it doesn't exist or NULL in case of an error. 
	 */
	function hasProperty($property)
	{
		return property_exists($this,$property);
	}
}