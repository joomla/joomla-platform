#!/usr/bin/php
<?php
/**
 * A "hello world" command line application built on the Joomla Platform.
 *
 * To run this example, adjust the executable path above to suite your operating system,
 * make this file executable and run the file.
 *
 * Alternatively, run the file using:
 *
 * php -f run.php
 *
 * @package    Joomla.Examples
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// We are a valid Joomla entry point.
// This is required to load the Joomla Platform import.php file.
define('_JEXEC', 1);

//-- @todo BUG - The $this->input->get('foo', '', 'PATH'); - given an absolute path ('/foo/bar') returns an empty string :(
$target =(isset($argv[1])) ? $argv[1] : JPATH_BASE.'/docs';
define('PATH_TARGET', $target);

// Setup the base path related constant.
// This is one of the few, mandatory constants needed for the Joomla Platform.
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', JPATH_BASE);

// Bootstrap the application.
require dirname(dirname(dirname(__FILE__))).'/libraries/import.php';


// Import the JCli class from the platform.
jimport('joomla.application.cli');

/**
 * A "hello world" command line application class.
 *
 * Simple command line applications extend the JCli class.
 *
 * @package  Joomla.Examples
 * @since    11.3
 */
class SnifferTestsMakeDoc extends JCli
{
	const S_WAITING  = 0;
	const S_STANDARD = 1;
	const S_SNIFF    = 2;
	const S_COMMENT  = 3;
	const S_CONTENT  = 4;

	protected $showComments = true;//@todo set somewhere else..
	protected $showSniffs = false;//@todo set somewhere else..

	/**
	 * Execute the application.
	 *
	 * The 'execute' method is the entry point for a command line application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function execute()
	{
		//-- @todo: BUG - The $this->input->get('foo', '', 'PATH'); - given an absolute path ('/foo/bar') returns an empty string :(

		// $target = $this->input->get('target', JPATH_BASE.'/docs', 'PATH');

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$this->out('Building the Joomla! CodeSniffer Documentation...');

		$folders = JFolder::folders(JPATH_BASE.'/tests');

		$files = array('good', 'bad');

		$pages = array();

		foreach ($folders as $folder)
		{
			$pages[$folder] = array();

			foreach ($files as $file)
			{
				$pages[$folder][$file] = array();

				$path = 'tests/'.$folder.'/'.$file.'.php';

				if ( ! JFile::exists($path))
				{
					$this->out('File not found: '.$path);

					continue;
				}

				$this->out('Processing: '.$path.'...', false);

				$lines = file($path);

				$state = self::S_WAITING;

				$p = null;

				foreach ($lines as $line)
				{
					$t = trim($line);

					if (self::S_STANDARD == $state)
					{
						if ('*/' == $t)
						{
							// Standard "declaration" ends, content (code) begins
							$state = self::S_CONTENT;

							continue;
						}

						// The "name"/path of the sniff
						$p->sniff = trim($line);

						if (preg_match('/{HL:([0-9,]+)}/', $p->sniff, $matches))
						{
							// Lines to highlight
							$p->highlights = explode(',', $matches[1]);
							$p->sniff = str_replace($matches[0], '', $p->sniff);
						}

						$state = self::S_SNIFF;

						continue;
					}

					if (self::S_SNIFF == $state)
					{
						if ('*/' == $t)
						{
							// Standard "declaration" ends, content (code) begins
							$state = self::S_CONTENT;

							continue;
						}

						// We have a comment
						$p->comment[] = trim($line);
					}

					if ('/* ENDDOC */' == $t)
					{
						// Documentation ends here
						$state = self::S_WAITING;

						continue;
					}

					if (0 === strpos($t, '/* Standard:'))
					{
						// Standard declaration begins

						if ($p)
						{
							// Store previous page
							$pages[$folder][$file][] = $p;
						}

						$p = new SniffStandard;

						$p->standard = str_replace('/* Standard:', '', $t);

						$state = self::S_STANDARD;

						continue;
					}

					if (self::S_CONTENT == $state)
					{
						// Replace tabs with four spaces
						// $line = str_replace("\t", '    ', $line);

						// The "content" (code)
						$p->content .= $line;
					}
				}//foreach

				if ($p)
				{
					// Store previous page
					$pages[$folder][$file][] = $p;
				}

				$this->out('OK');
			}//foreach
		}//foreach

		$this->makeHtml($pages);

		$this->out('Finished =;)');
	}//function

	/**
	 * Generate the documentation pages.
	 *
	 * @param   array  $pages  The pages to format
	 *
	 * @return string
	 */
	protected function makeHtml($pages)
	{
// 		$target = $this->input->get('target', JPATH_BASE.'/docs', 'PATH');
		$target = PATH_TARGET;

		JFolder::create($target);

		$indexLinks = array();

		foreach ($pages as $title => $dPages)
		{
			$indexLinks[] = '<a href="'.$title.'.html">'.$title.'</a>';

			$p = array();

			$p[] = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
			$p[] = '<title>Joomla! Coding Standards - '.$title.'</title>';

			$p[] = '<style>';
			$p[] = 'td.good, th.good {background-color: #e5ff99;}';
			$p[] = 'td.good span {background-color: #99ff00;}';
			$p[] = 'td.bad, th.bad {background-color: #ffb2b2;}';
			$p[] = 'td.bad span {background-color: #ff6666;}';
			$p[] = 'td.std {background-color: #fff; padding: 0.3em; font-weight: bold;}';
			$p[] = 'td a {color: black; text-decoration:none; display: block;}';
			$p[] = 'td a:hover, td:target a {color: orange;}';
			$p[] = 'td:target {border: 2px solid orange;}';
			$p[] = 'b.draft {color: #555; opacity: .3; position: fixed; left: 30%; top: 50%; font-size: 4em; -moz-transform: rotate(-35deg);}';
			$p[] = '</style>';

			$p[] = '</head>';

			$p[] = '<body>';

			$p[] = '<h1>The Joomla! Coding Standards</h1>';
			$p[] = '<a href="index.html">&lArr; Index</a>';
			$p[] = '<h2>'.$title.'</h2>';

			$p[] = '<b class="draft">D R A F T</b>';
			$p[] = '<table>';

			$p[] = '<tr><th class="bad">Don\'t</th><th class="good">Do</th></tr>';

			foreach ($dPages['bad'] as $i => $bad)
			{
				$p[] = '<tr>';

				$p[] = '<td colspan="2" class="std" id="'.$i.'">';
				$p[] = '<a href="#'.$i.'">'.$bad->standard.'</a> ';

				if ($this->showComments && $bad->comment)
				{
					$p[] = '<br /><small>'.implode('<br />', $bad->comment).'</small>';
				}

				if ($this->showSniffs && $bad->sniff)
				{
					$p[] = '<code>'.$bad->sniff.'</code>';
				}

				$p[] = '</td>';
				$p[] = '</tr>';

				$p[] = '<tr style="vertical-align: top;">';

				$p[] = '<td class="bad"><pre>';

				$lines = explode("\n", $bad->content);

				foreach ($lines as $n => $line)
				{
					$line =($line) ? htmlentities($line) : '&nbsp;';
					$p[] =(in_array($n + 1, $bad->highlights)) ? '<span>'.$line.'</span>' : $line;
				}

				$p[] = '</pre></td>';

				if (isset($dPages['good'][$i]))
				{
					$lines = explode("\n", rtrim($dPages['good'][$i]->content));

					$p[] = '<td class="good"><pre>';

					foreach ($lines as $n => $line)
					{
						$line =($line) ? htmlentities($line) : '&nbsp;';
						$p[] =(in_array($n + 1, $dPages['good'][$i]->highlights)) ? '<span>'.$line.'</span>' : $line;
					}//foreach

					$p[] = '</pre></td>';
				}
				else
				{
					$p[] = '<td> ? </td>';
				}

				$p[] = '</tr>';
			}//foreach

			$p[] = '</table>';

			$p[] = '<a href="index.html">&lArr; Index</a>';

			$p[] = '</body></html>';

			JFile::write($target.'/'.$title.'.html', implode("\n", $p));

			$this->out('Page written to: '.$target.'/'.$title.'.html');
		}//foreach

		// Create the index.html

		$p = array();

		$p[] = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>The Joomla! Coding Standards</title></head>';

		$p[] = '<body>';

		$p[] = '<h1>The Joomla! Coding Standards</h1>';
		$p[] = '<h2>Cheat Sheet</h2>';

		$p[] = '<ol>';
		$p[] = '    <li>'.implode("</li>\n    <li>", $indexLinks).'</li>';
		$p[] = '</ol>';

		JFile::write($target.'/index.html', implode("\n", $p));
	}//function
}//class

/**
 * SniffStandard
 *
 * @package  Joomla.Tests
 * @since    11.3
 */
class SniffStandard
{
	public $standard = '';
	public $content = '';
	public $comment = array();
	public $sniff = '';
	public $highlights = array();
}//class

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
try
{
	JCli::getInstance('SnifferTestsMakeDoc')
	->execute();
}
catch (Exception $e)
{
	echo $e->getMessage();

	$code =($e->getCode()) ? $e->getCode() : 1;//@todo PHP 5.3 ternaries

	exit($code);
}//try
