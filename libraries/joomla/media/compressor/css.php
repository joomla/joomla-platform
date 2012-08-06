<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Media
 * 
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * CSS Compressor Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Media
 * @since       12.1 
 */
class JMediaCompressorCss extends JMediaCompressor
{
	/**
	 * Object constructor
	 * 
	 * @param   Array  $options  Compression options for CSS Minifier.
	 * 
	 * @since   12.1 
	 */
	public function __construct($options = array())
	{
		$this->_options = array('REMOVE_COMMENTS' => true, 'MIN_COLOR_CODES' => true, 'LIMIT_LINE_LENGTH' => true);
		parent::__construct($options);
	}

	/**
	 * Method to compress the code.
	 * 
	 * @return   Void
	 *
	 * @since  12.1 
	 */
	public function compress()
	{
		if ($this->_uncompressed === null)
		{
			throw new RuntimeException(JText::sprintf('JMEDIA_CSS_COMPRESSION_ERROR_UNCOMPRESSED_NOTSET'));
		}

		$this->_compressed = str_replace("\r\n", "\n", $this->_uncompressed);

		$this->_compressed = $this->_preServe($this->_compressed);

		/* 	Process all valid comments and apply call back, handleComments() function will return relavent replacements
		*	Second argument is to tell call $this->_handleComments() method and get replacement patterns for matches
		*	Delimiter '~' is used because using '/' will make this regex pattern ambigious
		*/
		$this->_compressed = preg_replace_callback('~\\s*/\\*([\\s\\S]*?)\\*/\\s*~', array($this,'_handleComments'), $this->_compressed);

		$this->_compressed = $this->_removeWS($this->_compressed);

		// Handle selectors - match a start of a selector and pass them to $this->_handleSelectors() to get replacements
		// /x is used turn on free-spacing mode in regex patterns
		$this->_compressed = preg_replace_callback('/(?:\\s*[^~>+,\\s]+\\s*[,>+~])+\\s*[^~>+,\\s]+{/', array($this,'_handleSelectors'), $this->_compressed);

		if ($this->_options['MIN_COLOR_CODES'])
		{
			$this->_compressed = $this->_minColorCodes($this->_compressed);
		}

		if ($this->_options['LIMIT_LINE_LENGTH'])
		{
			$this->_compressed = $this->_breakInToLines($this->_compressed);
		}

		$this->_compressed = preg_replace('/:first-l(etter|ine)\\{/', ':first-l$1 {', $this->_compressed);

		$this->_compressed = trim($this->_compressed);

		$this->_compressedSize = strlen($this->_compressed);
	}

	/**
	 * Method to preserve special browser hacks - Will add 'keep' word infront of comments
	 * 
	 * @param   string  $source  source css code
	 * 
	 * @return  string  modified css code  
	 * 
	 * @since  12.1 
	 */
	private function _preServe($source)
	{
		// Preserve empty comment after '>'
		$patterns [] 	= '~>/\\*\\s*\\*/~';
		$replacements[]	= '>/*keep*/';

		// Preserve empty comment between property and value
		$patterns [] 	= '~/\\*\\s*\\*/\\s*:~';
		$replacements[]	= '/*keep*/:';

		$patterns [] 	= '~:\\s*/\\*\\s*\\*/~';
		$replacements[]	= ':/*keep*/';

		return preg_replace($patterns, $replacements, $source);
	}

	/**
	 * Method to detect which replacement patterne to use for identified comments
	 * 
	 * @param   Array  $matches  bacreferences from preg_replace_callback()
	 * 
	 * @return  string  replacements for comments
	 * 
	 * @since   12.1
	 */
	private function _handleComments($matches)
	{
		// Do not replace the preserved and need to keep comments
		if ($matches[1] === 'keep')
		{
			return '/**/';
		}

		// Replacement for css mid pass filters
		if ($matches[1] === '" "')
		{
			return '/*" "*/';
		}

		// Replacement for css mid pass filters
		if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $matches[1]))
		{
			return '/*";}}/* */';
		}

		// Keep any surrounding white space
		if ((trim($matches[0]) !== $matches[1]))
		{
			return ' ';
		}

		return '';
	}

	/**
	 * Method to process css selectors and identify replacements
	 * 
	 * @param   array  $matches  bacreferences from preg_replace_callback()
	 * 
	 * @return  String  replacements for selectors
	 * 
	 * @since   12.1
	*/
	private function _handleSelectors($matches)
	{
		// Remove space around combinators
		return preg_replace('/\\s*([,>+~])\\s*/', '$1', $matches[0]);
	}

	/**
	 * Method to remove unnecessary white spaces
	 * 
	 * @param   string  $source  source css code
	 * 
	 * @return  string  white space removed css code
	 * 
	 * @since  12.1
	 */
	private function _removeWS($source)
	{
		// Remove spaces around ;
		$patterns[] 	= '/\\s*;\\s*/';

		$replacements[] = ';';

		// Remove spaces around {} and final ; inside {}
		$patterns[]		= '/\\s*{\\s*/';

		$replacements[] = '{';

		$patterns[]		= '/;?\\s*}\\s*/';

		$replacements[]	= '}';

		// Remove spaces around urls
		// X is used turn on free-spacing mode in regex patterns
		$patterns[]		= '/url\\(		# url(
			                \\s*
			                ([^\\)]+?)	# match 1 is url
			                \\s*
			                \\)			# )
			           		/x';

		$replacements[]	= 'url($1)';

		$patterns[]		= '/@import\\s+url/';

		$replacements[]	= '@import url';

		// Remove tabs and spaces around a new line

		$patterns[]		= '/[ \\t]*\\n+\\s*/';

		$replacements[]	= "\n";

		// Remove spaces around css rules and colons
		// X is used turn on free-spacing mode in regex patterns
		$patterns[]		= '/\\s*	([{;])				# match 1 = start of block
									\\s*
									([\\*_]?[\\w\\-]+)  # match 2 = property
									\\s* :	\\s*
									(\\b|[#\'"-])		# match 3 = start of value
									/x';

		// Using backreferences 1, 2 and 3
		$replacements[]	= '$1$2:$3';

		$tmp = preg_replace($patterns, $replacements, $source);

		return $tmp;
	}

	/**
	 * Method to minimize colour codes
	 * 
	 * @param   string  $source  Source css code
	 * 
	 * @return  string  modified css code
	 * 
	 * @since   12.1
	 */
	private function _minColorCodes($source)
	{
		return preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i', '$1#$2$3$4$5', $source);
	}

	/**
	 * Method to break minified code in to new lines to limit line lengths (optional)
	 * 
	 * @param   string  $source  Source css code
	 * 
	 * @return  string  modified css code
	 * 
	 * @since  12.1
	*/
	private function _breakInToLines($source)
	{
		// Insert a newline between desendant selectors
		$source = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $source);

		// Insert a new line after 1st numeric value found within a padding, margin, border or outline property
		$source = preg_replace('/
					            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # match 1 = 1st numeric string (eg: 10px)
					            \\s+
					            /x', "$1\n", $source
								);
		return $source;
	}

	/**
	 * Method to clear compressor data
	 *
	 * @return  void
	 *
	 * @since  12.1
	 */
	public function clear()
	{
		parent::clear();
	}

}
