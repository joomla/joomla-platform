<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Register the session storage class with the loader
JLoader::register('JButton', __DIR__ . '/toolbar/button.php');

/**
 * ToolBar handler
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JToolBar
{
	/**
	 * Toolbar name
	 *
	 * @var    string
	 */
	protected $name = array();

	/**
	 * Toolbar array
	 *
	 * @var    array
	 */
	protected $bar = array();

	/**
	 * Loaded buttons
	 *
	 * @var    array
	 */
	protected $buttons = array();

	/**
	 * Directories, where button types can be stored.
	 *
	 * @var    array
	 */
	protected $buttonPath = array();

	/**
	 * Constructor
	 *
	 * @param   string  $name  The toolbar name.
	 *
	 * @since   11.1
	 */
	public function __construct($name = 'toolbar')
	{
		$this->name = $name;

		// Set base path to find buttons.
		$this->buttonPath[] = __DIR__ . '/toolbar/button';
	}

	/**
	 * magic get method
	 *
	 * @param   $propertyName  Property name
	 *
	 * @return  mixed  the property value
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __get($propertyName)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			return $this->$newPropertyName;
		}
		else
		{
			// Trigger an error
			return $this->$propertyName;
		}
	}

	/**
	 * magic set method
	 *
	 * @param   $propertyName  Property name
	 * @param   $value         Property name
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __set($propertyName, $value)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			$this->$newPropertyName = $value;
		}
		else
		{
			$this->$propertyName = $value;
		}
	}

	/**
	 * Stores the singleton instances of various toolbar.
	 *
	 * @var JToolbar
	 * @since 11.3
	 */
	protected static $instances = array();

	/**
	 * Returns the global JToolBar object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param   string  $name  The name of the toolbar.
	 *
	 * @return  JToolBar  The JToolBar object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($name = 'toolbar')
	{
		if (empty(self::$instances[$name]))
		{
			self::$instances[$name] = new JToolBar($name);
		}

		return self::$instances[$name];
	}

	/**
	 * Set a value
	 *
	 * @return  string  The set value.
	 *
	 * @since   11.1
	 */
	public function appendButton()
	{
		// Push button onto the end of the toolbar array.
		$btn = func_get_args();
		array_push($this->bar, $btn);
		return true;
	}

	/**
	 * Get the list of toolbar links.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		return $this->bar;
	}

	/**
	 * Get the name of the toolbar.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get a value.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function prependButton()
	{
		// Insert button into the front of the toolbar array.
		$btn = func_get_args();
		array_unshift($this->bar, $btn);
		return true;
	}

	/**
	 * Render a tool bar.
	 *
	 * @return  string  HTML for the toolbar.
	 *
	 * @since   11.1
	 */
	public function render()
	{
		$html = array();

		// Start toolbar div.
		$html[] = '<div class="toolbar-list" id="' . $this->name . '">';
		$html[] = '<ul>';

		// Render each button in the toolbar.
		foreach ($this->bar as $button)
		{
			$html[] = $this->renderButton($button);
		}

		// End toolbar div.
		$html[] = '</ul>';
		$html[] = '<div class="clr"></div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Render a button.
	 *
	 * @param   object  &$node  A toolbar node.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function renderButton(&$node)
	{
		// Get the button type.
		$type = $node[0];

		$button = $this->loadButtonType($type);

		// Check for error.
		if ($button === false)
		{
			return JText::sprintf('JLIB_HTML_BUTTON_NOT_DEFINED', $type);
		}
		return $button->render($node);
	}

	/**
	 * Loads a button type.
	 *
	 * @param   string   $type  Button Type
	 * @param   boolean  $new   False by default
	 *
	 * @return  object
	 *
	 * @since   11.1
	 */
	public function loadButtonType($type, $new = false)
	{
		$signature = md5($type);
		if (isset($this->buttons[$signature]) && $new === false)
		{
			return $this->buttons[$signature];
		}

		if (!class_exists('JButton'))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_HTML_BUTTON_BASE_CLASS'));
			return false;
		}

		$buttonClass = 'JButton' . $type;
		if (!class_exists($buttonClass))
		{
			if (isset($this->buttonPath))
			{
				$dirs = $this->buttonPath;
			}
			else
			{
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DIRECTORY_SEPARATOR, strtolower($type)) . '.php', 'path');

			jimport('joomla.filesystem.path');
			if ($buttonFile = JPath::find($dirs, $file))
			{
				include_once $buttonFile;
			}
			else
			{
				JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('JLIB_HTML_BUTTON_NO_LOAD', $buttonClass, $buttonFile));
				return false;
			}
		}

		if (!class_exists($buttonClass))
		{
			// @todo remove code: return	JError::raiseError('SOME_ERROR_CODE', "Module file $buttonFile does not contain class $buttonClass.");
			return false;
		}
		$this->buttons[$signature] = new $buttonClass($this);

		return $this->buttons[$signature];
	}

	/**
	 * Add a directory where JToolBar should search for button types in LIFO order.
	 *
	 * You may either pass a string or an array of directories.
	 *
	 * JToolbar will be searching for an element type in the same order you
	 * added them. If the parameter type cannot be found in the custom folders,
	 * it will look in libraries/joomla/html/toolbar/button.
	 *
	 * @param   mixed  $path  Directory or directories to search.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @see JToolbar
	 */
	public function addButtonPath($path)
	{
		// Just force path to array.
		settype($path, 'array');

		// Loop through the path directories.
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed.
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs.
			array_unshift($this->buttonPath, $dir);
		}

	}
}
