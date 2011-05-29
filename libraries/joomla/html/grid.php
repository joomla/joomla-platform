<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JGrid class to dynamically generate HTML tables
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JGrid
{
	/*
	 * Column data
	 * 
	 * @var array
	 */
	protected $columns = array();
	
	/**
	 * Order of the columns
	 * 
	 * @var array
	 */
	protected $columnorder = array();
	
	/**
	 * Data for the rows and cells
	 * 
	 * @var array
	 */
	protected $rows = array();
	
	/**
	 * Data for the footer
	 * 
	 * @var array
	 */
	protected $footer = array();
	
	/**
	 * Options for the table element
	 * 
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * Constructor for the JGrid object
	 * 
	 * @param array	Associative array of attributes for the table element
	 */
	function __construct($options = array())
	{
		$this->setTableOptions($options, true);
	}
	
	/**
	 * Set the attributes for the table element
	 * 
	 * @param array	Associative array of attributes for the table object
	 * @param bool	Replace or append the data to the current set of attributes
	 */
	function setTableOptions($options = array(), $replace = false)
	{
		if($replace) {
			$this->options = $options;
		} else {
			$this->options = array_merge($this->options, $options);
		}
		
	}
	
	/**
	 * Get array of attributs for the table element
	 * 
	 * @return array Associative array of attributes for the table element
	 */
	function getTableOptions()
	{
		return $this->options;
	}

	/**
	 * Get all column names
	 * 
	 * @return array Array of column names
	 */
	function getColumns()
	{
		return array_keys($this->columns);
	}
	
	/**
	 * Add a new column to the table object
	 * 
	 * @param string	Name of the new column
	 * @param string	Content of the column header
	 * @param array		Associative array of attributes for the th element
	 */
	function addColumn($name, $content = '', $options = array())
	{
		$column = new stdClass();
		$column->header = $content;
		$column->options = $options;
		$this->columns[$name] = $column;
		$this->columnorder[] = $name;
	}
	
	/**
	 * Get column object by name
	 * 
	 * @param string Name of the column
	 */
	function getColumn($name)
	{
		return $this->columns[$name];
	}
	
	/**
	 * Delete a column from the table object
	 * 
	 * @param string	Name of the column to delete
	 */
	function deleteColumn($name)
	{
		unset($this->columns[$name]);
		$index = array_search($name, $this->columnorder);
		if($index !== false)
		{
			unset($this->columnorder[$index]);
		}
	}
	
	/**
	 * Get column order
	 * 
	 * @return array Array of strings of column names
	 */
	function getColumnOrder()
	{
		return $this->columnorder;
	}
	
	/**
	 * Set column order
	 * 
	 * @param array Array of strings with column names
	 */
	function setColumnOrder($columns)
	{
		$this->columnorder = $columns;
	}
	
	/**
	 * Add a row to the table object
	 * 
	 * @param array	Associative array of attributes to the tr element
	 * @return int 	ID of the new row
	 */
	function addRow($options = array())
	{
		$this->rows[]['_row'] = $options;
		return count($this->rows) - 1;
	}
	
	/**
	 * Fill the specific cell of a row
	 * 
	 * @param int 		ID of the row
	 * @param string	Name of the column
	 * @param string	Content of the cell
	 * @param array		Associative array of attributes for the td element
	 * @param bool		Replace or append the data to existing data in this cell
	 */
	function addRowCell($i, $name, $content, $option = array(), $replace = true)
	{
		if($replace || !isset($this->rows[$i][$name]))
		{
			$cell = new stdClass();
			$cell->options = $option;
			$cell->content = $content;
			$this->rows[$i][$name] = $cell;
		} else {
			$this->rows[$i][$name]->content .= $content;
			$this->rows[$i][$name]->options = array_merge($this->rows[$i][$name]->options, $option);
		}
	}	
	
	/**
	 * Get a row by ID
	 * 
	 * @param int ID of the row to retrieve
	 */
	function getRow($id)
	{
		return $this->rows[$id];
	}
	
	/**
	 * Delete a row from the table object
	 * 
	 * @param int ID of the row to delete
	 */
	function deleteRow($id)
	{
		unset($this->rows[$id]);
	}
	
	/**
	 * Add another footer object
	 * 
	 * @param string Content to be added to the footer cell
	 */
	function addFooter($content)
	{
		$this->footer[] = $content;
	}
	
	/**
	 * Get current footer data
	 * 
	 * @return array Array of strings
	 */
	function getFooter()
	{
		return $this->footer;
	}
	
	/**
	 * Clear the footer data for the table object
	 * 
	 * @return array Previous footer data
	 */
	function deleteFooter()
	{
		$return = $this->footer;
		$this->footer = array();
		return $return;
	}
	
	/**
	 * Render the table
	 * 
	 * This function renders the data set in the object into a table
	 * @return string A complete, valid HTML table
	 */
	function render()
	{
		$output = array();
		$output[] = '<table'.$this->renderAttributes($this->getTableOptions()).">\n";
		
		$output[] = $this->renderHead();
		
		$output[] = $this->renderFooter();
		
		$output[] = $this->renderBody();
		
		$output[] = "</table>\n";
		return implode('', $output);		
	}
	
	/**
	 * Helperfunction to render the head of a table
	 * 
	 * @return string Head of a table
	 */
	protected function renderHead()
	{
		$output = array("<thead>\n\t<tr>\n");
		foreach($this->getColumnOrder() as $name)
		{
			$column = $this->getColumn($name);
			$output[] = "\t\t<th".$this->renderAttributes($column->options).'>'.$column->header."</th>\n";	
		}
		$output[] = "\t</tr>\n</thead>\n";
		return implode('',$output);
	} 

	/**
	 * Helperfunction to render the body of a table
	 * 
	 * @return string Body of the table
	 */
	protected function renderBody()
	{
		$output = array();
		if(count($this->rows) == 0)
		{
			$output = '<tbody><tr><td colspan="'.count($this->getColumnOrder()).'">'
				.JText::_('THERE_ARE_NO_ITEMS_TO_SHOW').'</td></tr></tbody>';
			return $output;
		}
		$output[] = "<tbody>\n";
		foreach($this->rows as $row)
		{
			$output[] = "\t<tr".$this->renderAttributes($row['_row']).">\n";
			foreach($this->getColumnOrder() as $column)
			{
				if(isset($row[$column])) {
					$output[] = "\t\t<td".$this->renderAttributes($row[$column]->options).'>'.$row[$column]->content."</td>\n";
				} else {
					$output[] = "\t\t<td>&nbsp;</td>\n";
				}
			}
			$output[] = "\t</tr>\n";
		}
		$output[] = "</tbody>\n";
		return implode('',$output);
	} 
	
	/**
	 * Helperfunction to render footer of a table
	 * 
	 * @return string The footer of the table
	 */
	protected function renderFooter()
	{
		$output = '';
		if(count($this->footer))
		{
			$output = "<tfoot>\n\t<tr>\n\t\t<td colspan=\"".count($this->getColumnOrder())."\">\n";
			foreach($this->getFooter() as $footer)
			{
				$output .= $footer;
			}
			$output .= "\t\t</td>\n\t</tr>\n</tfoot>\n";
		}
		return $output;
	}
	
	/**
	 * Helperfunction to render HTML tag attributes
	 * 
	 * @param array Associative array of strings
	 */
	protected function renderAttributes($attributes)
	{
		$attributes = (array) $attributes;
		if(count($attributes) == 0) {
			return '';
		}
		$return = array();
		foreach($attributes as $key => $option)
		{
			$return[] = $key.'="'.$option.'"';
		}
		return ' '.implode(' ', $return);
	}
}