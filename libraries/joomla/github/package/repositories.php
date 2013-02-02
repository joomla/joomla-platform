<?php
/**
 * @package     Joomla.Platform
 * @subpackage  GitHub
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * GitHub API Activity class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  GitHub
 * @since       ¿
 *
 * @documentation  http://developer.github.com/v3/activity/
 *
 * @property-read  JGithubPackageRepositoriesComments  $comments  GitHub API object for comments.
 * @property-read  JGithubPackageRepositoriesCommits   $commits   GitHub API object for commits.
 * @property-read  JGithubPackageRepositoriesForks     $forks     GitHub API object for forks.
 * @property-read  JGithubPackageRepositoriesHooks     $hooks     GitHub API object for hooks.
 * @property-read  JGithubPackageRepositoriesKeys      $keys      GitHub API object for keys.
 * @property-read  JGithubPackageRepositoriesMerging   $merging   GitHub API object for merging.
 * @property-read  JGithubPackageRepositoriesStatuses  $statuses  GitHub API object for statuses.
 */
class JGithubPackageRepositories extends JGithubPackage
{
	protected $name = 'Repositories';

	protected $packages = array(
		'comments', 'commits', 'forks', 'hooks', 'keys', 'merging', 'statuses'
	);
}
