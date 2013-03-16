<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

trigger_error('JXMLElement is deprecated. Use SimpleXMLElement.', E_USER_DEPRECATED);

/**
 * Wrapper class for php SimpleXMLElement.
 *
 * @package     Joomla.Legacy
 * @subpackage  Utilities
 * @since       11.1
 * @deprecated  13.3 Use SimpleXMLElement instead.
 */
class JXMLElement extends SimpleXMLElement
{
	/**
	 * Get the name of the element.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated 13.3  Use SimpleXMLElement::getName() instead.
	 */
	public function name()
	{
		trigger_error('JXMLElement::name() is deprecated.
			Use SimpleXMLElement::getName() instead.',
			E_USER_DEPRECATED
		);

		return (string) $this->getName();
	}

	/**
	 * Return a well-formed XML string based on SimpleXML element
	 *
	 * @param   boolean  $compressed  Should we use indentation and newlines ?
	 * @param   string   $indent      Indention character.
	 * @param   integer  $level       The level within the document which informs the indentation.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated 13.3  Use SimpleXMLElement::asXML() instead.
	 */
	public function asFormattedXML($compressed = false, $indent = "\t", $level = 0)
	{
		trigger_error('JXMLElement::asFormattedXML() is deprecated.
			Use SimpleXMLElement::asXML() instead.',
			E_USER_DEPRECATED
		);

		$out = '';

		// Start a new line, indent by the number indicated in $level
		$out .= ($compressed) ? '' : "\n" . str_repeat($indent, $level);

		// Add a <, and add the name of the tag
		$out .= '<' . $this->getName();

		// For each attribute, add attr="value"
		foreach ($this->attributes() as $attr)
		{
			$out .= ' ' . $attr->getName() . '="' . htmlspecialchars((string) $attr, ENT_COMPAT, 'UTF-8') . '"';
		}

		// If there are no children and it contains no data, end it off with a />
		if (!count($this->children()) && !(string) $this)
		{
			$out .= " />";
		}
		else
		{
			// If there are children
			if (count($this->children()))
			{
				// Close off the start tag
				$out .= '>';

				$level++;

				// For each child, call the asFormattedXML function (this will ensure that all children are added recursively)
				foreach ($this->children() as $child)
				{
					$out .= $child->asFormattedXML($compressed, $indent, $level);
				}

				$level--;

				// Add the newline and indentation to go along with the close tag
				$out .= ($compressed) ? '' : "\n" . str_repeat($indent, $level);

			}
			elseif ((string) $this)
			{
				// If there is data, close off the start tag and add the data
				$out .= '>' . htmlspecialchars((string) $this, ENT_COMPAT, 'UTF-8');
			}

			// Add the end tag
			$out .= '</' . $this->getName() . '>';
		}

		return $out;
	}
}
