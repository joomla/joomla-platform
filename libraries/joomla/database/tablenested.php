<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

jimport('joomla.database.table');

/**
 * Table class supporting modified pre-order tree traversal behavior.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @link        http://docs.joomla.org/JTableNested
 * @since       11.1
 */
class JTableNested extends JTable
{
	/**
	 * Object property holding the primary key of the parent node.  Provides
	 * adjacency list data for nodes.
	 *
	 * @var    integer
	 * @since  11.1
	 */
	public $parent_id;

	/**
	 * Object property holding the depth level of the node in the tree.
	 *
	 * @var    integer
	 * @since  11.1
	 */
	public $level;

	/**
	 * Object property holding the left value of the node for managing its
	 * placement in the nested sets tree.
	 *
	 * @var    integer
	 * @since  11.1
	 */
	public $lft;

	/**
	 * Object property holding the right value of the node for managing its
	 * placement in the nested sets tree.
	 *
	 * @var    integer
	 * @since  11.1
	 */
	public $rgt;

	/**
	 * Object property holding the alias of this node used to constuct the
	 * full text path, forward-slash delimited.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $alias;

	/**
	 * Object property to hold the location type to use when storing the row.
	 * Possible values are: ['before', 'after', 'first-child', 'last-child'].
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_location;

	/**
	 * Object property to hold the primary key of the location reference node to
	 * use when storing the row.  A combination of location type and reference
	 * node describes where to store the current node in the tree.
	 *
	 * @var integer
	 * @since  11.1
	 */
	protected $_location_id;

	/**
	 * An array to cache values in recursive processes.
	 *
	 * @var   array
	 * @since  11.1
	 */
	protected $_cache = array();

	/**
	 * Debug level
	 *
	 * @var    integer
	 * @since  11.1
	 */
	protected $_debug = 0;

	/**
	 * Sets the debug level on or off
	 *
	 * @param   integer  $level  0 = off, 1 = on
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function debug($level)
	{
		$this->_debug = intval($level);
	}

	/**
	 * Method to get an array of nodes from a given node to its root.
	 *
	 * @param   integer  $pk          Primary key of the node for which to get the path.
	 * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
	 *
	 * @return  mixed    Boolean false on failure or array of node objects on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/getPath
	 * @since   11.1
	 */
	public function getPath($pk = null, $diagnostic = false)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		// Get the path from the node to the root.
		$query = $this->_db->getQuery(true);
		$select = ($diagnostic) ? 'p.' . $k . ', p.'.$parentName.', p.'.$levelName.', p.'.$lftName.', p.'.$rgtName : 'p.*';
		$query->select($select);
		$query->from($this->_tbl . ' AS n, ' . $this->_tbl . ' AS p');
		$query->where('n.'.$lftName.' BETWEEN p.'.$lftName.' AND p.'.$rgtName);
		$query->where('n.' . $k . ' = ' . (int) $pk);
		$query->order('p.'.$lftName);

		$this->_db->setQuery($query);
		$path = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GET_PATH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return $path;
	}

	/**
	 * Method to get a node and all its child nodes.
	 *
	 * @param   integer  $pk          Primary key of the node for which to get the tree.
	 * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
	 *
	 * @return  mixed    Boolean false on failure or array of node objects on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/getTree
	 * @since   11.1
	 */
	public function getTree($pk = null, $diagnostic = false)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		// Get the node and children as a tree.
		$query = $this->_db->getQuery(true);
		$select = ($diagnostic) ? 'n.' . $k . ', n.'.$parentName.', n.'.$levelName.', n.'.$lftName.', n.'.$rgtName : 'n.*';
		$query->select($select);
		$query->from($this->_tbl . ' AS n, ' . $this->_tbl . ' AS p');
		$query->where('n.'.$lftName.' BETWEEN p.'.$lftName.' AND p.'.$rgtName);
		$query->where('p.' . $k . ' = ' . (int) $pk);
		$query->order('n.'.$lftName);
		$this->_db->setQuery($query);
		$tree = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GET_TREE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return $tree;
	}

	/**
	 * Method to determine if a node is a leaf node in the tree (has no children).
	 *
	 * @param   integer  $pk  Primary key of the node to check.
	 *
	 * @return  boolean  True if a leaf node.
	 *
	 * @link    http://docs.joomla.org/JTableNested/isLeaf
	 * @since   11.1
	 */
	public function isLeaf($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk))
		{
			// Error message set in getNode method.
			return false;
		}

		// The node is a leaf node.
		return (($node->$rgtName - $node->$lftName) == 1);
	}

	/**
	 * Method to set the location of a node in the tree object.  This method does not
	 * save the new location to the database, but will set it in the object so
	 * that when the node is stored it will be stored in the new location.
	 *
	 * @param   integer  $referenceId  The primary key of the node to reference new location by.
	 * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/setLocation
	 * @since   11.1
	 */
	public function setLocation($referenceId, $position = 'after')
	{
		// Make sure the location is valid.
		if (($position != 'before') && ($position != 'after') && ($position != 'first-child') && ($position != 'last-child'))
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_LOCATION', get_class($this)));
			$this->setError($e);
			return false;
		}

		// Set the location properties.
		$this->_location = $position;
		$this->_location_id = $referenceId;

		return true;
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the
	 * ordering values.
	 *
	 * @return  mixed    Boolean true on success.
	 *
	 * @link    http://docs.joomla.org/JTable/move
	 * @since   11.1
	 */
	public function move($delta, $where = '')
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = $this->$k;
		$parentName = $this->getColumnAlias('parent_id');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		$query = $this->_db->getQuery(true);
		$query->select($k);
		$query->from($this->_tbl);
		$query->where($parentName.' = ' . $this->$parentName);
		if ($where)
		{
			$query->where($where);
		}
		$position = 'after';
		if ($delta > 0)
		{
			$query->where($rgtName.' > ' . $this->$rgtName);
			$query->order($rgtName.' ASC');
			$position = 'after';
		}
		else
		{
			$query->where($lftName.' < ' . $this->$lftName);
			$query->order($lftName.' DESC');
			$position = 'before';
		}

		$this->_db->setQuery($query);
		$referenceId = $this->_db->loadResult();

		if ($referenceId)
		{
			return $this->moveByReference($referenceId, $position, $pk);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to move a node and its children to a new location in the tree.
	 *
	 * @param   integer  $referenceId  The primary key of the node to reference new location by.
	 * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
	 * @param   integer  $pk           The primary key of the node to move.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/moveByReference
	 * @since   11.1
	 */

	public function moveByReference($referenceId, $position = 'after', $pk = null)
	{
		if ($this->_debug)
		{
			echo "\nMoving ReferenceId:$referenceId, Position:$position, PK:$pk";
		}

		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		$titleName  = $this->getColumnAlias('title');
		$aliasName  = $this->getColumnAlias('alias');

		// Get the node by id.
		if (!$node = $this->_getNode($pk))
		{
			// Error message set in getNode method.
			return false;
		}

		// Get the ids of child nodes.
		$query = $this->_db->getQuery(true);
		$query->select($k);
		$query->from($this->_tbl);
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);
		$children = $this->_db->loadColumn();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		if ($this->_debug)
		{
			$this->_logtable(false);
		}

		// Cannot move the node to be a child of itself.
		if (in_array($referenceId, $children))
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_NODE_RECURSION', get_class($this)));
			$this->setError($e);
			return false;
		}

		// Lock the table for writing.
		if (!$this->_lock())
		{
			return false;
		}

		/*
		 * Move the sub-tree out of the nested sets by negating its left and right values.
		 */
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' * (-1), '.$rgtName.' = '.$rgtName.' * (-1)');
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		/*
		 * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
		 */
		// Compress the left values.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' - ' . (int) $node->width);
		$query->where($lftName.' > ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		// Compress the right values.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($rgtName.' = '.$rgtName.' - ' . (int) $node->width);
		$query->where($rgtName.' > ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		// We are moving the tree relative to a reference node.
		if ($referenceId)
		{
			// Get the reference node by primary key.
			if (!$reference = $this->_getNode($referenceId))
			{
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position))
			{
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}
		}
		// We are moving the tree to be the last child of the root node
		else
		{
			// Get the last root node as the reference node.
			$query = $this->_db->getQuery(true);
			$query->select($this->_tbl_key . ', '.$parentName.', '.$levelName.', '.$lftName.', '.$rgtName);
			$query->from($this->_tbl);
			$query->where($parentName.' = 0');
			$query->order($lftName.' DESC');
			$this->_db->setQuery($query, 0, 1);
			$reference = $this->_db->loadObject();

			// Check for a database error.
			if ($this->_db->getErrorNum())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
				$this->setError($e);
				$this->_unlock();
				return false;
			}
			if ($this->_debug)
			{
				$this->_logtable(false);
			}

			// Get the reposition data for re-inserting the node after the found root.
			if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, 'last-child'))
			{
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}
		}

		/*
		 * Create space in the nested sets at the new location for the moved sub-tree.
		 */
		// Shift left values.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' + ' . (int) $node->width);
		$query->where($repositionData->left_where);
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		// Shift right values.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($rgtName.' = '.$rgtName.' + ' . (int) $node->width);
		$query->where($repositionData->right_where);
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		/*
		 * Calculate the offset between where the node used to be in the tree and
		 * where it needs to be in the tree for left ids (also works for right ids).
		 */
		$offset = $repositionData->new_lft - $node->$lftName;
		$levelOffset = $repositionData->new_level - $node->$levelName;

		// Move the nodes back into position in the tree using the calculated offsets.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($rgtName.' = ' . (int) $offset . ' - '.$rgtName);
		$query->set($lftName.' = ' . (int) $offset . ' - '.$lftName);
		$query->set($levelName.' = '.$levelName.' + ' . (int) $levelOffset);
		$query->where($lftName.' < 0');
		$this->_db->setQuery($query);

		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

		// Set the correct parent id for the moved node if required.
		if ($node->$parentName != $repositionData->new_parent_id)
		{
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);

			// Update the title and alias fields if they exist for the table.
			if (property_exists($this, $titleName) && $this->$titleName !== null)
			{
				$query->set($titleName.' = ' . $this->_db->Quote($this->$titleName));
			}
			if (property_exists($this, $aliasName) && $this->$aliasName !== null)
			{
				$query->set($aliasName.' = ' . $this->_db->Quote($this->$aliasName));
			}

			$query->set($parentName.' = ' . (int) $repositionData->new_parent_id);
			$query->where($this->_tbl_key . ' = ' . (int) $node->$k);
			$this->_db->setQuery($query);

			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');
		}

		// Unlock the table for writing.
		$this->_unlock();

		// Set the object values.
		$this->$parentName = $repositionData->new_parent_id;
		$this->$levelName  = $repositionData->new_level;
		$this->$lftName    = $repositionData->new_lft;
		$this->$rgtName    = $repositionData->new_rgt;

		return true;
	}

	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/delete
	 * @since   11.1
	 */
	public function delete($pk = null, $children = true)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');	

		// Lock the table for writing.
		if (!$this->_lock())
		{
			// Error message set in lock method.
			return false;
		}

		// If tracking assets, remove the asset first.
		if ($this->_trackAssets)
		{
			$name = $this->_getAssetName();
			$asset = JTable::getInstance('Asset');

			// Lock the table for writing.
			if (!$asset->_lock())
			{
				// Error message set in lock method.
				return false;
			}

			if ($asset->loadByName($name))
			{
				// Delete the node in assets table.
				if (!$asset->delete(null, $children))
				{
					$this->setError($asset->getError());
					$asset->_unlock();
					return false;
				}
				$asset->_unlock();
			}
			else
			{
				$this->setError($asset->getError());
				$asset->_unlock();
				return false;
			}
		}

		// Get the node by id.
		if (!$node = $this->_getNode($pk))
		{
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Should we delete all children along with the node?
		if ($children)
		{
			// Delete the node and all of its children.
			$query = $this->_db->getQuery(true);
			$query->delete();
			$query->from($this->_tbl);
			$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Compress the left values.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($lftName.' = '.$lftName.' - ' . (int) $node->width);
			$query->where($lftName.' > ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Compress the right values.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($rgtName.' = '.$rgtName.' - ' . (int) $node->width);
			$query->where($rgtName.' > ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		}
		// Leave the children and move them up a level.
		else
		{
			// Delete the node.
			$query = $this->_db->getQuery(true);
			$query->delete();
			$query->from($this->_tbl);
			$query->where($lftName.' = ' . (int) $node->$lftName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Shift all node's children up a level.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($lftName.' = '.$lftName.' - 1');
			$query->set($rgtName.' = '.$rgtName.' - 1');
			$query->set($levelName.' = '.$levelName.' - 1');
			$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Adjust all the parent values for direct children of the deleted node.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($parentName.' = ' . (int) $node->$parentName);
			$query->where($parentName.' = ' . (int) $node->$k);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Shift all of the left values that are right of the node.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($lftName.' = '.$lftName.' - 2');
			$query->where($lftName.' > ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

			// Shift all of the right values that are right of the node.
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set($rgtName.' = '.$rgtName.' - 2');
			$query->where($rgtName.' > ' . (int) $node->$rgtName);
			$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Asset that the nested set data is valid.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @link    http://docs.joomla.org/JTable/check
	 * @since   11.1
	 */
	public function check()
	{
		$parentName = $this->getColumnAlias('parent_id');
		$this->$parentName = (int) $this->$parentName;
		if ($this->$parentName > 0)
		{
			$query = $this->_db->getQuery(true);
			$query->select('COUNT(' . $this->_tbl_key . ')');
			$query->from($this->_tbl);
			$query->where($this->_tbl_key . ' = ' . $this->$parentName);
			$this->_db->setQuery($query);

			if ($this->_db->loadResult())
			{
				return true;
			}
			else
			{
				if ($this->_db->getErrorNum())
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CHECK_FAILED', get_class($this), $this->_db->getErrorMsg()));
					$this->setError($e);
				}
				else
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_PARENT_ID', get_class($this)));
					$this->setError($e);
				}
			}
		}
		else
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_PARENT_ID', get_class($this)));
			$this->setError($e);
		}

		return false;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/store
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		
		if ($this->_debug)
		{
			echo "\n" . get_class($this) . "::store\n";
			$this->_logtable(true, false);
		}
		/*
		 * If the primary key is empty, then we assume we are inserting a new node into the
		 * tree.  From this point we would need to determine where in the tree to insert it.
		 */
		if (empty($this->$k))
		{
			/*
			 * We are inserting a node somewhere in the tree with a known reference
			 * node.  We have to make room for the new node and set the left and right
			 * values before we insert the row.
			 */
			if ($this->_location_id >= 0)
			{
				// Lock the table for writing.
				if (!$this->_lock())
				{
					// Error message set in lock method.
					return false;
				}

				// We are inserting a node relative to the last root node.
				if ($this->_location_id == 0)
				{
					// Get the last root node as the reference node.
					$query = $this->_db->getQuery(true);
					$query->select($this->_tbl_key . ', '.$parentName.', '.$levelName.', '.$lftName.', '.$rgtName);
					$query->from($this->_tbl);
					$query->where($parentName.' = 0');
					$query->order($lftName.' DESC');
					$this->_db->setQuery($query, 0, 1);
					$reference = $this->_db->loadObject();

					// Check for a database error.
					if ($this->_db->getErrorNum())
					{
						$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
						$this->setError($e);
						$this->_unlock();
						return false;
					}
					if ($this->_debug)
					{
						$this->_logtable(false);
					}
				}
				// We have a real node set as a location reference.
				else
				{
					// Get the reference node by primary key.
					if (!$reference = $this->_getNode($this->_location_id))
					{
						// Error message set in getNode method.
						$this->_unlock();
						return false;
					}
				}

				// Get the reposition data for shifting the tree and re-inserting the node.
				if (!($repositionData = $this->_getTreeRepositionData($reference, 2, $this->_location)))
				{
					// Error message set in getNode method.
					$this->_unlock();
					return false;
				}

				// Create space in the tree at the new location for the new node in left ids.
				$query = $this->_db->getQuery(true);
				$query->update($this->_tbl);
				$query->set($lftName.' = '.$lftName.' + 2');
				$query->where($repositionData->left_where);
				$this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

				// Create space in the tree at the new location for the new node in right ids.
				$query = $this->_db->getQuery(true);
				$query->update($this->_tbl);
				$query->set($rgtName.' = '.$rgtName.' + 2');
				$query->where($repositionData->right_where);
				$this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

				// Set the object values.
				$this->$parentName = $repositionData->new_parent_id;
				$this->$levelName  = $repositionData->new_level;
				$this->$lftName    = $repositionData->new_lft;
				$this->$rgtName    = $repositionData->new_rgt;
			}
			else
			{
				// Negative parent ids are invalid
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_INVALID_PARENT_ID'));
				$this->setError($e);
				return false;
			}
		}
		/*
		 * If we have a given primary key then we assume we are simply updating this
		 * node in the tree.  We should assess whether or not we are moving the node
		 * or just updating its data fields.
		 */
		else
		{
			// If the location has been set, move the node to its new location.
			if ($this->_location_id > 0)
			{
				if (!$this->moveByReference($this->_location_id, $this->_location, $this->$k))
				{
					// Error message set in move method.
					return false;
				}
			}

			// Lock the table for writing.
			if (!$this->_lock())
			{
				// Error message set in lock method.
				return false;
			}
		}

		// Store the row to the database.
		if (!parent::store($updateNulls))
		{
			$this->_unlock();
			return false;
		}
		if ($this->_debug)
		{
			$this->_logtable();
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to set the publishing state for a node or list of nodes in the database
	 * table.  The method respects rows checked out by other users and will attempt
	 * to checkin rows that it can after adjustments are made. The method will not
	 * allow you to set a publishing state higher than any ancestor node and will
	 * not allow you to set a publishing state on a node with a checked out child.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not
	 * set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/publish
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		$checkName  = $this->getColumnAlias('checked_out');
		$checkTimeName  = $this->getColumnAlias('checked_out_time');
		$publishName = $this->getColumnAlias('published');

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;
		// If $state > 1, then we allow state changes even if an ancestor has lower state
		// (for example, can change a child state to Archived (2) if an ancestor is Published (1)
		$compareState = ($state > 1) ? 1 : $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = explode(',', $this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED', get_class($this)));
				$this->setError($e);
				return false;
			}
		}

		// Determine if there is checkout support for the table.
		$checkoutSupport = (property_exists($this, $checkName) || property_exists($this, $checkTimeName));

		// Iterate over the primary keys to execute the publish action if possible.
		foreach ($pks as $pk)
		{
			// Get the node by primary key.
			if (!$node = $this->_getNode($pk))
			{
				// Error message set in getNode method.
				return false;
			}

			// If the table has checkout support, verify no children are checked out.
			if ($checkoutSupport)
			{
				// Ensure that children are not checked out.
				$query = $this->_db->getQuery(true);
				$query->select('COUNT(' . $k . ')');
				$query->from($this->_tbl);
				$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
				$query->where('('.$checkName.' <> 0 AND '.$checkName.' <> ' . (int) $userId . ')');
				$this->_db->setQuery($query);

				// Check for checked out children.
				if ($this->_db->loadResult())
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CHILD_ROWS_CHECKED_OUT', get_class($this)));
					$this->setError($e);
					return false;
				}
			}

			// If any parent nodes have lower published state values, we cannot continue.
			if ($node->$parentName)
			{
				// Get any ancestor nodes that have a lower publishing state.
				$query = $this->_db->getQuery(true)
							  ->select('n.' . $k)
							  ->from($this->_db->quoteName($this->_tbl) . ' AS n')
							  ->where('n.'.$lftName.' < ' . (int) $node->$lftName)
							  ->where('n.'.$rgtName.' > ' . (int) $node->$rgtName)
							  ->where('n.'.$parentName.' > 0')
							  ->where('n.'.$publishName.' < ' . (int) $compareState);

				// Just fetch one row (one is one too many).
				$this->_db->setQuery($query, 0, 1);

				$rows = $this->_db->loadColumn();

				// Check for a database error.
				if ($this->_db->getErrorNum())
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
					$this->setError($e);
					return false;
				}

				if (!empty($rows))
				{
					$e = new JException(JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'));
					$this->setError($e);
					return false;
				}
			}

			// Update and cascade the publishing state.
			$query = $this->_db->getQuery(true)
						  ->update($this->_db->quoteName($this->_tbl) .' AS n')
						  ->set('n.'.$publishName.' = '. (int) $state)
						  ->where('(n.'.$lftName.' > '. (int) $this->$lftName.' AND n.'.$rgtName.' < ' . (int) $this->$rgtName.') OR n.'.$k.' = '. (int) $pk);
			$this->_db->setQuery($query);

			// Check for a database error.
			if (!$this->_db->query())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}

			// If checkout support exists for the object, check the row in.
			if ($checkoutSupport)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->$publishName = $state;
		}

		$this->setError('');
		return true;
	}

	/**
	 * Method to move a node one position to the left in the same level.
	 *
	 * @param   integer  $pk  Primary key of the node to move.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/orderUp
	 * @since   11.1
	 */
	public function orderUp($pk)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		// Lock the table for writing.
		if (!$this->_lock())
		{
			// Error message set in lock method.
			return false;
		}

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk))
		{
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the left sibling node.
		if (!$sibling = $this->_getNode($node->$lftName - 1, 'right'))
		{
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the primary keys of child nodes.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_key);
		$query->from($this->_tbl);
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);
		$children = $this->_db->loadColumn();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the node and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' - ' . (int) $sibling->width);
		$query->set($rgtName.' = '.$rgtName.' - ' . (int) $sibling->width);
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' + ' . (int) $node->width);
		$query->set($rgtName.' = '.$rgtName.' + ' . (int) $node->width);
		$query->where($lftName.' BETWEEN ' . (int) $sibling->$lftName . ' AND ' . (int) $sibling->$rgtName);
		$query->where($this->_tbl_key . ' NOT IN (' . implode(',', $children) . ')');
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to move a node one position to the right in the same level.
	 *
	 * @param   integer  $pk  Primary key of the node to move.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/orderDown
	 * @since   11.1
	 */
	public function orderDown($pk)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		
		// Lock the table for writing.
		if (!$this->_lock())
		{
			// Error message set in lock method.
			return false;
		}

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk))
		{
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the right sibling node.
		if (!$sibling = $this->_getNode($node->$rgtName + 1, 'left'))
		{
			// Error message set in getNode method.
			$query->unlock($this->_db);
			$this->_locked = false;
			return false;
		}

		// Get the primary keys of child nodes.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_key);
		$query->from($this->_tbl);
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);
		$children = $this->_db->loadColumn();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the node and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' + ' . (int) $sibling->width);
		$query->set($rgtName.' = '.$rgtName.' + ' . (int) $sibling->width);
		$query->where($lftName.' BETWEEN ' . (int) $node->$lftName . ' AND ' . (int) $node->$rgtName);
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = '.$lftName.' - ' . (int) $node->width);
		$query->set($rgtName.' = '.$rgtName.' - ' . (int) $node->width);
		$query->where($lftName.' BETWEEN ' . (int) $sibling->$lftName . ' AND ' . (int) $sibling->$rgtName);
		$query->where($this->_tbl_key . ' NOT IN (' . implode(',', $children) . ')');
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Gets the ID of the root item in the tree
	 *
	 * @return  mixed    The ID of the root row, or false and the internal error is set.
	 *
	 * @since   11.1
	 */
	public function getRootId()
	{
		// Get the root item.
		$k = $this->_tbl_key;
		$parentName = $this->getColumnAlias('parent_id');
		$aliasName  = $this->getColumnAlias('alias');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');

		// Test for a unique record with parent_id = 0
		$query = $this->_db->getQuery(true);
		$query->select($k);
		$query->from($this->_tbl);
		$query->where($parentName.' = 0');
		$this->_db->setQuery($query);

		$result = $this->_db->loadColumn();

		if ($this->_db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		if (count($result) == 1)
		{
			$parentId = $result[0];
		}
		else
		{
			// Test for a unique record with lft = 0
			$query = $this->_db->getQuery(true);
			$query->select($k);
			$query->from($this->_tbl);
			$query->where($lftName.' = 0');
			$this->_db->setQuery($query);

			$result = $this->_db->loadColumn();
			if ($this->_db->getErrorNum())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}

			if (count($result) == 1)
			{
				$parentId = $result[0];
			}
			elseif (property_exists($this, $aliasName))
			{
				// Test for a unique record alias = root
				$query = $this->_db->getQuery(true);
				$query->select($k);
				$query->from($this->_tbl);
				$query->where($aliasName.' = ' . $this->_db->quote('root'));
				$this->_db->setQuery($query);

				$result = $this->_db->loadColumn();
				if ($this->_db->getErrorNum())
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
					$this->setError($e);
					return false;
				}

				if (count($result) == 1)
				{
					$parentId = $result[0];
				}
				else
				{
					$e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
					$this->setError($e);
					return false;
				}
			}
			else
			{
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
				$this->setError($e);
				return false;
			}
		}

		return $parentId;
	}

	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure
	 *
	 * @link    http://docs.joomla.org/JTableNested/rebuild
	 * @since   11.1
	 */
	public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '')
	{
		// Initialise variables.
		$parentName = $this->getColumnAlias('parent_id');
		$aliasName  = $this->getColumnAlias('alias');
		$levelName  = $this->getColumnAlias('level');
		$orderName  = $this->getColumnAlias('ordering');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		$pathName	= $this->getColumnAlias('path');
		
		// If no parent is provided, try to find it.
		if ($parentId === null)
		{
			// Get the root item.
			$parentId = $this->getRootId();
			if ($parentId === false)
			{
				return false;
			}

		}

		// Build the structure of the recursive query.
		if (!isset($this->_cache['rebuild.sql']))
		{
			$query = $this->_db->getQuery(true);
			$query->select($this->_tbl_key . ', '.$aliasName);
			$query->from($this->_tbl);
			$query->where($parentName.' = %d');

			// If the table has an ordering field, use that for ordering.
			if (property_exists($this, $orderName))
			{
				$query->order($parentNme.', '.$orderName.', '.$lftName);
			}
			else
			{
				$query->order($parentName.', '.$lftName);
			}
			$this->_cache['rebuild.sql'] = (string) $query;
		}

		// Make a shortcut to database object.

		// Assemble the query to find all children of this node.
		$this->_db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));
		$children = $this->_db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuild($node->{$this->_tbl_key}, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->$aliasName);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($lftName.' = ' . (int) $leftId);
		$query->set($rgtName.' = ' . (int) $rightId);
		$query->set($levelName.' = ' . (int) $level);
		$query->set($pathName.' = ' . $this->_db->quote($path));
		$query->where($this->_tbl_key . ' = ' . (int) $parentId);
		$this->_db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	/**
	 * Method to rebuild the node's path field from the alias values of the
	 * nodes from the current node to the root node of the tree.
	 *
	 * @param   integer  $pk  Primary key of the node for which to get the path.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTableNested/rebuildPath
	 * @since   11.1
	 */
	public function rebuildPath($pk = null)
	{
		$aliasName  = $this->getColumnAlias('alias');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		$pathName	= $this->getColumnAlias('path');
		
		// If there is no alias or path field, just return true.
		if (!property_exists($this, $aliasName) || !property_exists($this, $pathName))
		{
			return true;
		}

		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the aliases for the path from the node to the root node.
		$query = $this->_db->getQuery(true);
		$query->select('p.'.$aliasName);
		$query->from($this->_tbl . ' AS n, ' . $this->_tbl . ' AS p');
		$query->where('n.'.$lftName.' BETWEEN p.'.$lftName.' AND p.'.$rgtName);
		$query->where('n.' . $this->_tbl_key . ' = ' . (int) $pk);
		$query->order('p.'.$lftName);
		$this->_db->setQuery($query);

		$segments = $this->_db->loadColumn();

		// Make sure to remove the root path if it exists in the list.
		if ($segments[0] == 'root')
		{
			array_shift($segments);
		}

		// Build the path.
		$path = trim(implode('/', $segments), ' /\\');

		// Update the path field for the node.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($pathName.' = ' . $this->_db->quote($path));
		$query->where($this->_tbl_key . ' = ' . (int) $pk);
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILDPATH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// Update the current record's path to the new one:
		$this->$pathName = $path;

		return true;
	}

	/**
	 * Method to update order of table rows
	 *
	 * @param   array  $idArray    id numbers of rows to be reordered.
	 * @param   array  $lft_array  lft values of rows to be reordered.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Validate arguments
		if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array))
		{
			$lftName    = $this->getColumnAlias('lft');

			for ($i = 0, $count = count($idArray); $i < $count; $i++)
			{
				// Do an update to change the lft values in the table for each id
				$query = $this->_db->getQuery(true);
				$query->update($this->_tbl);
				$query->where($this->_tbl_key . ' = ' . (int) $idArray[$i]);
				$query->set($lftName.' = ' . (int) $lft_array[$i]);
				$this->_db->setQuery($query);

				// Check for a database error.
				if (!$this->_db->query())
				{
					$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REORDER_FAILED', get_class($this), $this->_db->getErrorMsg()));
					$this->setError($e);
					$this->_unlock();
					return false;
				}

				if ($this->_debug)
				{
					$this->_logtable();
				}

			}

			return $this->rebuild();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get nested set properties for a node in the tree.
	 *
	 * @param   integer  $id   Value to look up the node by.
	 * @param   string   $key  Key to look up the node by.
	 *
	 * @return  mixed    Boolean false on failure or node object on success.
	 *
	 * @since   11.1
	 */
	protected function _getNode($id, $key = null)
	{
		// Determine which key to get the node base on.
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		
		switch ($key)
		{
			case 'parent':
				$k = $parentName;
				break;
			case 'left':
				$k = $lftName;
				break;
			case 'right':
				$k = $rgtName;
				break;
			default:
				$k = $this->_tbl_key;
				break;
		}

		// Get the node data.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_key . ', '.$parentName.', '.$levelName.', '.$lftName.', '.$rgtName);
		$query->from($this->_tbl);
		$query->where($k . ' = ' . (int) $id);
		$this->_db->setQuery($query, 0, 1);

		$row = $this->_db->loadObject();

		// Check for a database error or no $row returned
		if ((!$row) || ($this->_db->getErrorNum()))
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETNODE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// Do some simple calculations.
		$row->numChildren = (int) ($row->$rgtName - $row->$lftName - 1) / 2;
		$row->width = (int) $row->$rgtName - $row->$lftName + 1;

		return $row;
	}

	/**
	 * Method to get various data necessary to make room in the tree at a location
	 * for a node and its children.  The returned data object includes conditions
	 * for SQL WHERE clauses for updating left and right id values to make room for
	 * the node as well as the new left and right ids for the node.
	 *
	 * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
	 * 									 which to make room in the tree around for a new node.
	 * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
	 * @param   string   $position       The position relative to the reference node where the room
	 * should be made.
	 *
	 * @return  mixed    Boolean false on failure or data object on success.
	 *
	 * @since   11.1
	 */
	protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
	{
		// Make sure the reference an object with a left and right id.
		$parentName = $this->getColumnAlias('parent_id');
		$levelName  = $this->getColumnAlias('level');
		$lftName    = $this->getColumnAlias('lft');
		$rgtName    = $this->getColumnAlias('rgt');
		if (!is_object($referenceNode) && isset($referenceNode->$lftName) && isset($referenceNode->$rgtName))
		{
			return false;
		}

		// A valid node cannot have a width less than 2.
		if ($nodeWidth < 2)
		{
			return false;
		}

		// Initialise variables.
		$k = $this->_tbl_key;
		$data = new stdClass();

		// Run the calculations and build the data object by reference position.
		switch ($position)
		{
			case 'first-child':
				$data->left_where  = $lftName.' > ' . $referenceNode->$lftName;
				$data->right_where = $rgtName.' >= ' . $referenceNode->$lftName;

				$data->new_lft = $referenceNode->$lftName + 1;
				$data->new_rgt = $referenceNode->$lftName + $nodeWidth;
				$data->new_parent_id = $referenceNode->$k;
				$data->new_level = $referenceNode->$levelName + 1;
				break;

			case 'last-child':
				$data->left_where  = $lftName.' > ' . ($referenceNode->$rgtName);
				$data->right_where = $rgtName.' >= ' . ($referenceNode->$rgtName);

				$data->new_lft = $referenceNode->$rgtName;
				$data->new_rgt = $referenceNode->$rgtName + $nodeWidth - 1;
				$data->new_parent_id = $referenceNode->$k;
				$data->new_level = $referenceNode->$levelName + 1;
				break;

			case 'before':
				$data->left_where  = $lftName.' >= ' . $referenceNode->$lftName;
				$data->right_where = $rgtName.' >= ' . $referenceNode->$lftName;

				$data->new_lft = $referenceNode->$lftName;
				$data->new_rgt = $referenceNode->$lftName + $nodeWidth - 1;
				$data->new_parent_id = $referenceNode->$parentName;
				$data->new_level = $referenceNode->$levelName;
				break;

			default:
			case 'after':
				$data->left_where  = $lftName.' > ' . $referenceNode->$rgtName;
				$data->right_where = $rgtName.' > ' . $referenceNode->$rgtName;

				$data->new_lft = $referenceNode->$rgtName + 1;
				$data->new_rgt = $referenceNode->$rgtName + $nodeWidth;
				$data->new_parent_id = $referenceNode->$parentName;
				$data->new_level = $referenceNode->$levelName;
				break;
		}

		if ($this->_debug)
		{
			echo "\nRepositioning Data for $position" . "\n-----------------------------------" . "\nLeft Where:    $data->left_where"
				. "\nRight Where:   $data->right_where" . "\nNew Lft:       $data->new_lft" . "\nNew Rgt:       $data->new_rgt"
				. "\nNew Parent ID: $data->new_parent_id" . "\nNew Level:     $data->new_level" . "\n";
		}

		return $data;
	}

	/**
	 * Method to create a log table in the buffer optionally showing the query and/or data.
	 *
	 * @param   boolean  $showData   True to show data
	 * @param   boolean  $showQuery  True to show query
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function _logtable($showData = true, $showQuery = true)
	{
		$sep = "\n" . str_pad('', 40, '-');
		$buffer = '';
		if ($showQuery)
		{
			$buffer .= "\n" . $this->_db->getQuery() . $sep;
		}

		if ($showData)
		{
			$parentName = $this->getColumnAlias('parent_id');
			$levelName  = $this->getColumnAlias('level');
			$lftName    = $this->getColumnAlias('lft');
			$rgtName    = $this->getColumnAlias('rgt');
		
			$query = $this->_db->getQuery(true);
			$query->select($this->_tbl_key . ', '.$parentName.', '.$lftName.', '.$rgtName.', '.$levelName);
			$query->from($this->_tbl);
			$query->order($this->_tbl_key);
			$this->_db->setQuery($query);

			$rows = $this->_db->loadRowList();
			$buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $this->_tbl_key, 'par', 'lft', 'rgt');
			$buffer .= $sep;

			foreach ($rows as $row)
			{
				$buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $row[0], $row[1], $row[2], $row[3]);
			}
			$buffer .= $sep;
		}
		echo $buffer;
	}

	/**
	 * Method to run an update query and check for a database error
	 *
	 * @param   string  $query         The query.
	 * @param   string  $errorMessage  Unused.
	 *
	 * @return  boolean  False on exception
	 *
	 * @since   11.1
	 */
	protected function _runQuery($query, $errorMessage)
	{
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('$errorMessage', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			$this->_unlock();
			return false;
		}
		if ($this->_debug)
		{
			$this->_logtable();
		}
	}

}
