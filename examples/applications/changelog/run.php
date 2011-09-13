#!/usr/bin/php
<?php
/**
 * An example command line application built on the Joomla Platform.
 *
 * To run this example, adjust the executable path above to suite your operating system,
 * make this file executable and run the file.
 *
 * Alternatively, run the file using:
 *
 * php -f run.php
 *
 * @package     Joomla.Examples
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Setup the path related constants.
define('JPATH_BASE', dirname(__FILE__));

// Bootstrap the application.
require dirname(dirname(dirname(dirname(__FILE__)))).'/libraries/import.php';

jimport('joomla.application.cli');

// Register the markdown parser class so it's loaded when needed.
JLoader::register('ElephantMarkdown', __DIR__.'/includes/markdown.php');

/**
 * An example command line application class.
 *
 * This application builds the HTML version of the Joomla Platform change log from the Github API
 * that is used in news annoucements.
 *
 * @package		NewLifeInIT
 * @subpackage	cron
 */
class Changelog extends JCli
{
	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function execute()
	{
		// Import the JHttp class that will connect with the Github API.
		jimport('joomla.client.http');

		// Get a list of the merged pull requests.
		$merged = $this->getMergedPulls();

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Joomla Platform pull request log</title>
		</head>
	<body>';

		// Set the maximum number of pages (and runaway failsafe).
		$cutoff = 10;
		$page = 1;

		while ($cutoff--)
		{
			// Get a page of issues.
			$issues = $this->getIssues($page++);

			// Check if we've gone past the last page.
			if (empty($issues))
			{
				break;
			}

			$html .= PHP_EOL.'	<ul>';

			// Loop through each pull.
			foreach ($issues as $issue)
			{
				// Check if the issue has been merged.
				if (empty($issue->pull_request->html_url))
				{
					continue;
				}

				// Check if the pull has been merged.
				if (!in_array($issue->number, $merged))
				{
					continue;
				}

				$html .= PHP_EOL.'		<li>';

				$html .= PHP_EOL.'			<p>';

				// Prepare the link to the pull.
				$html .= '[<a href="'.$issue->html_url.'" title="Closed '.$issue->closed_at.'">';
				$html .= '#'.$issue->number;
				$html .= '</a>] <strong>'.$issue->title.'</strong>';
				$html .= ' (<a href="https://github.com/'.$issue->user->login.'">'.$issue->user->login.'</a>)';

				if (trim($issue->body))
				{
					// Parse the markdown formatted description of the pull.
					// Note, this doesn't account for all the Github flavoured markdown, but it's close.
					$html .= ElephantMarkdown::parse($issue->body);
				}

				$html .= PHP_EOL.'	</li>';
			}

			$html .= PHP_EOL.'	</ul>';
		}

		$html .= PHP_EOL.'	</body>';
		$html .= PHP_EOL.'</html>';

		// Check if the output folder exists.
		if (!is_dir('./docs'))
		{
			mkdir('./docs');
		}

		// Write the file.
		file_put_contents('./docs/changelog.html', $html);

		// Close normally.
		$this->close();
	}

	/**
	 * Get a page of issue data.
	 *
	 * @param   integer  $page  The page number.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	protected function getIssues($page)
	{
		$http = new JHttp;
		$r = $http->get(
			'https://api.github.com/repos/joomla/joomla-platform/issues?state=closed&sort=updated&direction=desc&page='.$page.'&per_page=100'
		);

		return json_decode($r->body);
	}

	/**
	 * Gets a list of the pull request numbers that have been merged.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	protected function getMergedPulls()
	{
		$cutoff = 10;
		$page = 1;
		$merged = array();

		while ($cutoff--)
		{
			$http = new JHttp;

			$r = $http->get(
				'https://api.github.com/repos/joomla/joomla-platform/pulls?state=closed&page='.$page++.'&per_page=100'
			);

			$pulls = json_decode($r->body);

			// Check if we've gone past the last page.
			if (empty($pulls))
			{
				break;
			}

			// Loop through each of the pull requests.
			foreach ($pulls as $pull)
			{
				// If merged, add to the white list.
				if ($pull->merged_at)
				{
					$merged[] = $pull->number;
				}
			}
		}

		return $merged;
	}
}

// Catch any exceptions thrown.
try
{
	JCli::getInstance('Changelog')->execute();
}
catch (Exception $e)
{
	$this->out($e->getMessage());
	$this->close($e->getCode());
}
