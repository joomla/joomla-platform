<?php
/**
 * @package     Joomla.Platform
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.path');

/**
 * Joomla Platform HTML View Class
 *
 * @package     Joomla.Platform
 * @subpackage  View
 * @since       12.1
 */
abstract class JViewHtml extends JViewBase
{
	/**
	 * The view layout.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var    SplPriorityQueue
	 * @since  12.1
	 */
	protected $paths;

	/**
	 * Method to instantiate the view.
	 *
	 * @param   JModel            $model  The model object.
	 * @param   SplPriorityQueue  $paths  The paths queue.
	 *
	 * @since   12.1
	 */
	public function __construct(JModel $model, SplPriorityQueue $paths = null)
	{
		parent::__construct($model);

		// Setup dependencies.
		$this->paths = isset($paths) ? $paths : $this->loadPaths();
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     JView::escape()
	 * @since   12.1
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 *
	 * @since   12.1
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to get the layout path.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  mixed  The layout file name if found, false otherwise.
	 *
	 * @since   12.1
	 */
	public function getPath($layout)
	{
		// Get the layout file name.
		$file = JPath::clean($layout . '.php');

		// Find the layout file path.
		$path = JPath::find(clone($this->paths), $file);

		return $path;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return  SplPriorityQueue  The paths queue.
	 *
	 * @since   12.1
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function render()
	{
		// Get the layout path.
		$path = $this->getPath($this->getLayout());

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException('Layout Path Not Found');
		}

		// Start an output buffer.
		ob_start();

		// Load the layout.
		include $path;

		// Get the layout contents.
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   SplPriorityQueue  $paths  The paths queue.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setPaths(SplPriorityQueue $paths)
	{
		$this->paths = $paths;
	}

	/**
	 * Method to load the paths queue.
	 *
	 * @return  SplPriorityQueue  The paths queue.
	 *
	 * @since   12.1
	 */
	protected function loadPaths()
	{
		return new SplPriorityQueue;
	}
}
