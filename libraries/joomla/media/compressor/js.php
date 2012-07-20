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
 * Javascript Compressor Class.
 *
 * @package     Joomla.Platform  
 * @subpackage  Media
 * 
 * @since       12.1
 */
class JMediaCompressorJs extends JMediaCompressor
{

	/**
	 * Used to track the index
	 * 
	 * @var    String
	 * @since  12.1
	 */
	private  $_a = "\n";

	/**
	 * Used to track the index.
	 * 
	 *@var  String
	 *@since  12.1 
	 */
	private $_b = '';

	/**
	 * Next available Index to process.
	 * 
	 * @var    int   
	 * @since  12.1 
	 */
	private $_nextIndex = 0;

	/**
	 * length of uncompressed code after CR and LF chars are replaced with CR
	 * 
	 * @var    int
	 * @since  12.1 
	 */
	private $_startLength = 0;

	/**
	 * to hold a preloaded char to peek nextindex
	 * 
	 * @var    char
	 * @since  12.1 
	 */
	private $_preLoaded = null;

	/**
	 * last preocessed char used to identify keywords
	 * 
	 * @var    char
	 * @since  12.1 
	 */
	private $_previousChar = null;

	/**
	 * Object Constructor one parameters.
	 *
	 * @param   Array  $options  Compression options for CSS Minifier.
	 *
	 * @since  12.1
	 */
	public function __construct($options = array())
	{
		$this->_options = array('REMOVE_COMMENTS' => true, 'CHANGE_ENCODING' => true);

		parent::__construct($options);
	}

	/**
	 * Method to compress the code.
	 * 
	 * @return  void
	 * 
	 * @since  12.1
	 */
	public function compress()
	{
		if ($this->_uncompressed === null)
		{
			throw new RuntimeException(JText::sprintf('JMEDIA_JS_COMPRESSION_ERROR_UNCOMPRESSED_NOTSET'));
		}
		$encoding = $this->_changeCharEncoding();

		$this->_uncompressed = str_replace("\r\n", "\n", $this->_uncompressed);
		$this->_startLength = strlen($this->_uncompressed);

		/*	Commands to determine start point of switch in _executeCommand()
		*	Command 1	: Keep  A
		*	Command 2	: Delete A
		*	Command 3	: Delete A to B
		*/
		$this->_executeCommand(3);

		while ($this->_a !== null)
		{
			$cmd = 1;

			if ($this->_a === ' ')
			{
				if (($this->_previousChar === '+' || $this->_previousChar === '-') && ($this->_b === $this->_previousChar))
				{
					// Do nothing
				}
				elseif (!$this->_checkAlphaNum($this->_b))
				{
					$cmd = 2;
				}
			}
			elseif ($this->_a === "\n")
			{
				if ($this->_b === ' ')
				{
					$cmd = 3;
				}
				elseif ( $this->_b === null || (strpos('{[(+-', $this->_b) === false && !$this->_checkAlphaNum($this->_b)))
				{
					$cmd = 2;
				}
			}
			elseif (!$this->_checkAlphaNum($this->_a))
			{
				if ($this->_b === ' ' || ($this->_b === "\n" && (false === strpos('}])+-"\'', $this->_a))))
				{
					$cmd = 3;
				}
			}

			$this->_executeCommand($cmd);

		}// End While

		// Resets multibyte encoding type
		if ($encoding !== null)
		{
			mb_internal_encoding($encoding);
		}

	}

	/**
	 * Method to execute commands
	 * 
	 * @param   int  $cmd  command number to execute   
	 * 
	 * @return  void
	 * 
	 * @throws  JMediaException
	 * 
	 * @since   12.1
	 */
	private function _executeCommand($cmd)
	{
		// Prevent + + or - - becomes ++ or --
		if ($cmd === 3 && ($this->_a === '+' || $this->_a === '-') && $this->_b === ' ' )
		{
			if ($this->_uncompressed[$this->_nextIndex] === $this->_a)
			{
				$cmd = 1;
			}
		}

		switch ($cmd)
		{
			case 1	:
				$this->_compressed 	.= $this->_a;
				$this->_previousChar = $this->_a;

			case 2	:
				$this->_a = $this->_b;

				if ($this->_a === "'" || $this->_a === '"')
				{
					while (true)
					{
						$this->_compressed 	.= $this->_a;
						$this->_previousChar = $this->_a;

						$this->_a = $this->_next();

						if ($this->_a === $this->_b)
						{
							break;
						}

						if ($this->_a === '\n')
						{
							throw  new JMediaException("Unterminated string at index " . $this->_nextIndex);
						}

						if ($this->_a === '\\')
						{
							$this->_compressed 	.= $this->_a;
							$this->_previousChar = $this->_a;

							$this->_a = $this->_next();
						}
					}
				}

			case 3	:
				$this->_b = $this->_getB();

				if ($this->_b === '/' && $this->_checkRegExp())
				{
					$this->_compressed .= $this->_a . $this->_b;

					while (true)
					{
						$this->_a = $this->_next();

						if ($this->_a === '/')
						{
							break;
						}
						elseif ($this->_a === '\\')
						{
							$this->_compressed .= $this->_a;
							$this->_a       	= $this->_next();
						}
						elseif (ord($this->_a) <= 10)
						{
							throw new JMediaException("Unterminated Regular expression at index" . $this->_nextIndex);
						}

						$this->_compressed 	.= $this->_a;
						$this->_previousChar = $this->_a;
					}

					$this->_b = $this->_getB();
				}
		}// End switch
	}

	/**
	 * Method to check whether a char is alpha numeric
	 * 
	 * @param   char  $char  char to be checked
	 * 
	 * @return  boolean  true if char is alpha numeric
	 * 
	 * @since   12.1
	 */
	private function _checkAlphaNum($char)
	{
		if (preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $char) || ord($char) > 126)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to check a regular expression
	 * 
	 * @return  boolean  true if that part is a regexp
	 * 
	 * @since   12.1
	 */
	private function _checkRegExp()
	{
		if (strpos("\n:[!&|?{;(,=", $this->_a) !== false)
		{
			return true;
		}

		if ($this->_a === ' ')
		{
			if (strlen($this->_compressed) < 2)
			{
				return true;
			}

			$matches = array();

			if (preg_match('/(?:case|else|in|return|typeof)$/', $this->_compressed, $matches))
			{
				if ($this->_compressed === $matches[0])
				{
					return true;
				}
				// Assure it is a keyword
				$previousChar = substr($this->_compressed, strlen($this->_compressed) - strlen($matches[0]) - 1, 1);

				if (!$this->_checkAlphaNum($previousChar))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Method to change multi-byte encoding
	 * 
	 * @return string  current multibyte encoding type
	 * 
	 * @since   12.1
	 */
	private function _changeCharEncoding()
	{
		$encoding = mb_internal_encoding();

		if (function_exists('mb_strlen') && (ini_get('mbstring.func_overload') == 2) && $this->_options['CHANGE_ENCODING'])
		{
			mb_internal_encoding('8bit');
		}

		return $encoding;
	}

	/**
	 * Method to get char at next index
	 * 
	 * @return  char   next available char
	 * 
	 * @since   12.1
	 */
	private function _next()
	{
		$char = null;

		if ($this->_nextIndex < $this->_startLength)
		{
			$char = $this->_uncompressed[$this->_nextIndex];
			$this->_nextIndex++;
		}
		else
		{
			return null;
		}

		if ($char === "\r" || $char === "\n")
		{
			return "\n";
		}

		// Control chars are replaced with space
		if (ord($char) < 32)
		{
			return ' ';
		}

		return $char;
	}

	/**
	 * Method to get next available char for point B
	 * 
	 * @return  char   next B
	 * 
	 * @since   12.1
	 */
	private function _getB()
	{
		$nextB = $this->_next();

		if ($nextB !== '/')
		{
			return $nextB;
		}

		$this->_preLoaded = $this->_uncompressed[$this->_nextIndex];

		if ($this->_preLoaded === '/' || $this->_preLoaded === '*')
		{
			return $this->_handleComments();
		}
		else
		{
			return $nextB;
		}

	}

	/**
	 * Method to handle comments when getting next B
	 * 
	 * @return  string  Immediate char on newline after single line comment or space if a multiline comment or the whole comment in some conditions
	 *                  
	 * @since   12.1
	 */
	private function  _handleComments()
	{
		$comment = '';

		if ($this->_preLoaded === '/')
		{
			while (true)
			{
				$tmp 		= $this->_next();
				$comment   .= $tmp;

				if (ord($tmp) <= 10)// End of line
				{
					// Keep IE conditional comment
					if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment))
					{
						$comment = "/{$comment}";
					}
					if ($this->_options['REMOVE_COMMENTS'])
					{
						return $tmp;
					}
					else
					{
						return $comment;
					}
				}
			}
		}
		elseif ($this->_preLoaded === '*')
		{
			$this->_next();

			while (true)
			{
				$tmp = $this->_next();

				if ($tmp === '*')
				{
					if ($this->_uncompressed[$this->_nextIndex] === '/')// End of comment
					{
						$this->_next();

						// Keep comments preserved by YUI Compressor
						if (0 === strpos($comment, '!'))
						{
							return "\n/*!" . substr($comment, 1) . "*/\n";
						}

						// Keep IE conditional comment
						if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment))
						{
							return "/*{$comment}*/";
						}

						return ' ';
					}
				}
				elseif ($tmp === null)
				{
					throw new JMediaException("Unterminated multiline comment at index" . $this->_nextIndex);
				}

				$comment .= $tmp;
			}
		}

		return $comment;
	}
}
