<?php
/**
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * A Directory handling class
 *
 * @property-read  RecursiveIteratorIterator  $files        Iterator on files.
 * @property-read  RecursiveIteratorIterator  $directories  Iterator on directories.
 *
 * @method                    boolean create(integer $permissions)               create the directory
 * @method                    boolean delete()                                   delete the directory
 * @method                      mixed copy(JFilesystemElementDirectory $dest)    copy the directory
 * @method                      mixed copyFromFile(JFilesystemElementFile $src)  copy from a file
 * @method  RecursiveIteratorIterator files(array $options)                      iterate on files
 * @method  RecursiveIteratorIterator directories(array $options)                iterate on directories
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 * @since       12.2
 */
class JFilesystemElementDirectory extends JFilesystemElement
{
	/**
	 * @var  integer  Default creation permissions
	 */
	public static $permissions = 0755;

	/**
	 * Constructor
	 *
	 * @param   string       $path       Element path.
	 * @param   JFilesystem  $system     Element file system
	 * @param   string       $signature  Signature
	 *
	 * @since   12.2
	 */
	protected function __construct($path, JFilesystem $system, $signature)
	{
		$fullpath = $system->prefix . $path;
		if (file_exists($fullpath) && is_file($fullpath))
		{
			throw new RuntimeException(
				sprintf('%s is already a file', $fullpath)
			);
		}
		parent::__construct($path, $fullpath, $system, $signature);
	}

	/**
	 * Magic getter.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  mixed  The property value.
	 *
	 * @throw   Exception
	 *
	 * @since   12.2
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'directories':
				return $this->callHandleError('items', array('directories', RecursiveIteratorIterator::SELF_FIRST));
				break;
			case 'files':
				return $this->callHandleError('items', array('files', RecursiveIteratorIterator::LEAVES_ONLY));
				break;
			default:
				return parent::__get($property);
				break;
		}
	}

	/**
	 * Magic call.
	 *
	 * @param   string  $method  The called method.
	 * @param   array   $args    The array of arguments passed to the method.
	 *
	 * @return  mixed  The result returned by the called method.
	 *
	 * @throw   Exception
	 *
	 * @since   12.2
	 */
	public function __call($method, $args)
	{
		switch ($method)
		{
			case 'copy':
			case 'copyFromFile':
			case 'create':
			case 'delete':
				return $this->callHandleError($method, $args);
				break;
			case 'directories':
				if (isset($args[0]['mode']) && $args[0]['mode'] == JFilesystemElementDirectoryContents::DEPTH_FIRST)
				{
					$mode = RecursiveIteratorIterator::CHILD_FIRST;
				}
				else
				{
					$mode = RecursiveIteratorIterator::SELF_FIRST;
				}
				return $this->callHandleError('items', array($method, $mode, $args[0]));
				break;
			case 'files':
				return $this->callHandleError('items', array($method, RecursiveIteratorIterator::LEAVES_ONLY, $args[0]));
				break;
			default:
				return parent::__call($method, $args);
				break;
		}
	}

	/**
	 * Makes directory.
	 *
	 * @param   mixed  $permissions  The permissions.
	 *
	 * @return  boolean  Returns TRUE on success or FALSE on failure.
	 *
	 * @throw   RuntimeException
	 *
	 * @link    http://php.net/manual/en/function.mkdir.php	 
	 *
	 * @since   12.2
	 */
	protected function create($permissions = null)
	{
		if ($permissions === null)
		{
			$permissions = self::$permissions;
		}

		if (!$this->exists && !mkdir($this->fullpath, $permissions, true, $this->system->context))
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException(sprintf('Could not create "%s" directory', $this->fullpath));

			// @codeCoverageIgnoreEnd
		}
		return $this;
	}

	/** List items.
	 *
	 * @param   string   $type     'files' or 'directories'.
	 * @param   integer  $mode     RecursiveIteratorIterator::LEAVES_ONLY|CHILD_FIRST|SELF_FIRST
	 * @param   array    $options  Array of options.
	 *
	 * @return  RecursiveIteratorIterator  Iterator on files.
	 *
	 * @since   12.2
	 */
	protected function items($type, $mode, array $options = array())
	{
		$class = 'JFilesystemElementDirectory' . ucfirst($type);
		$iterator = new RecursiveIteratorIterator(
			new $class(new JFilesystemElementDirectoryContents($this->path, '', $this->system, $options), $options), $mode
		);

		if (isset($options['recurse']))
		{
			if (!is_bool($options['recurse']) || !$options['recurse'])
			{
				$iterator->setMaxDepth((int) $options['recurse']);
			}
		}
		else
		{
			$iterator->setMaxDepth(0);
		}
		return $iterator;
	}

	/**
	 * Deletes a directory.
	 *
	 * @return  boolean  Returns TRUE on success or FALSE on failure.
	 *
	 * @throw   RuntimeException
	 *
	 * @link    http://php.net/manual/en/function.rmdir.php	 
	 * @link    http://php.net/manual/en/function.unlink.php	 
	 *
	 * @since   12.2
	 */
	protected function delete()
	{
		$iterator = new RecursiveIteratorIterator(
			new JFilesystemElementDirectoryContents($this->path, '', $this->system),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ($iterator as $relative => $basename)
		{
			$fullpath = $this->fullpath . '/' . $relative;
			if (is_dir($fullpath))
			{
				rmdir($fullpath);
			}
			else
			{
				unlink($fullpath);
			}
		}
		rmdir($this->fullpath);
		return $this;
	}

	/**
	 * Sets the element permissions
	 *
	 * @param   mixed  $permissions  The new permissions
	 *
	 * @return  boolean  TRUE on success, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileperms.php
	 * @link    http://php.net/manual/en/function.chmod.php
	 *
	 * @since   12.2
	 */
	protected function setPermissions($permissions)
	{
		if (is_int($permissions))
		{
			return parent::affectPermissions($permissions);
		}
		else
		{
			$operations = explode(',', $permissions);
			foreach ($operations as $operation)
			{
				if (strpos($operation, ':') === false)
				{
					if ($this->affectPermissions($operation) === false)
					{
						// @codeCoverageIgnoreStart
						return false;

						// @codeCoverageIgnoreEnd
					}
				}
				else
				{
					if (preg_match('#^(d(\[(\\d+)\])?)?(f(\[(\\d+)\])?)?:(.*)#', $operation, $matches))
					{
						// Match d
						if (!empty($matches[1]))
						{
							foreach ($this->directories(array('recurse' => $matches[3] == '' ? true : (int) $matches[3])) as $directory)
							{
								if ($directory->affectPermissions($matches[7]) === false)
								{
									// @codeCoverageIgnoreStart
									return false;

									// @codeCoverageIgnoreEnd
								}
							}
						}

						// Match f
						if (!empty($matches[4]))
						{
							foreach ($this->files(array('recurse' => $matches[6] == '' ? true : (int) $matches[6])) as $file)
							{
								if ($file->affectPermissions($matches[7]) === false)
								{
									// @codeCoverageIgnoreStart
									return false;

									// @codeCoverageIgnoreEnd
								}
							}
						}
					}
					else
					{
						throw new InvalidArgumentException(sprintf('The permissions %s are not correct', $operation));
					}
				}
			}
		}
		return true;
	}

	/**
	 * Copy from a file
	 *
	 * @param   JFilesystemElementFile  $src  The source file.
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	protected function copyFromFile(JFilesystemElementFile $src)
	{
		if (!$this->exists)
		{
			$this->create($src->directory->permissions);
		}
		return $src->copy($this->system->getFile($this->path . '/' . $src->name));
	}

	/**
	 * Copy to a directory
	 *
	 * @param   JFilesystemElementDirectory  $dest  The destination directory.
	 *
	 * @return  mixed  The number of bytes that were written to the files, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	protected function copy(JFilesystemElementDirectory $dest)
	{
		$return = 0;
		foreach ($this->directories(array('recurse' => true)) as $relative => $directory)
		{
			$this->system->getDirectory($dest->path . '/' . $relative)->create($directory->permissions);
		}
		foreach ($this->files(array('recurse' => true)) as $relative => $file)
		{
			$copy = $this->system->getFile($dest->path . '/' . $relative);
			$return = $return + $file->copy($copy);
		}
		return $return;
	}
}
