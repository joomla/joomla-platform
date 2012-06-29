<?php
/**
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * MediaWiki API Pages class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiPages extends JMediawikiObject
{
	/**
     * Method to edit a page.
	 *
	 * @param   string  $title         Page title.
	 * @param   int     $section       Section number.
	 * @param   string  $sectiontitle  The title for a new section.
	 * @param   string  $text          Page content.
	 * @param   string  $summary       Title of the page you want to delete.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function editPage($title, $section = null, $sectiontitle = null, $text = null, $summary = null) // @TODO add support to all methods
	{
		// Get the token.
		$token = $this->getToken($title, 'edit');

		// Build the request path.
		$path = '?action=edit';

		// Build the request data.
		$data = array(
			'title' => $title,
			'token' => $token,
			'section' => $section,
			'sectiontitle' => $section,
			'text' => $text,
			'summary' => $summary
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
	 * Method to delete a page.
	 *
	 * @param   string  $title      Title of the page you want to delete.
	 * @param   string  $reason     Reason for the deletion.
	 * @param   string  $watchlist  Unconditionally add or remove the page from your watchlis.
	 * @param   string  $oldimage   The name of the old image to delete.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function deletePageByName($title, $reason = null, $watchlist = null, $oldimage = null)
	{
		// Get the token.
		$token = $this->getToken($title, 'delete');

		// Build the request path.
		$path = '?action=delete';

		// Build the request data.
		$data = array(
			'title' => $title,
			'token' => $token,
			'reason' => $reason,
			'watchlist' => $watchlist,
			'oldimage' => $oldimage
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
	 * Method to delete a page.
	 *
	 * @param   string  $pageid     Page ID of the page you want to delete.
	 * @param   string  $reason     Reason for the deletion.
	 * @param   string  $watchlist  Unconditionally add or remove the page from your watchlis.
	 * @param   string  $oldimage   The name of the old image to delete.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function deletePageByID($pageid,  $reason = null, $watchlist = null, $oldimage = null)
	{
		// Get the token.
		$token = $this->getToken($pageid, 'delete');

		// Build the request path.
		$path = '?action=delete';

		// Build the request data.
		$data = array(
			'pageid' => $pageid,
			'token' => $token,
			'reason' => $reason,
			'watchlist' => $watchlist,
			'oldimage' => $oldimage
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
     * Method to restore certain revisions of a deleted page.
	 *
	 * @param   string  $title      Title of the page you want to restore.
	 * @param   string  $reason     Reason for restoring (optional).
	 * @param   string  $timestamp  Timestamps of the revisions to restore.
	 * @param   string  $watchlist  Unconditionally add or remove the page from your watchlist.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function undeletePage($title, $reason = null, $timestamp = null, $watchlist = null)
	{
		// Get the token.
		$token = $this->getToken($title, 'undelete');

		// Build the request path.
		$path = '?action=undelete';

		// Build the request data.
		$data = array(
			'title' => $title,
			'token' => $token,
			'reason' => $reason,
			'timestamp' => $timestamp,
			'watchlist' => $watchlist,
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
     * Method to move a page.
	 *
	 * @param   string   $from            Title of the page you want to move.
	 * @param   string   $to              Title you want to rename the page to.
	 * @param   string   $reason          Reason for the move (optional).
	 * @param   string   $movetalk        Move the talk page, if it exists.
	 * @param   string   $movesubpages    Move subpages, if applicable.
	 * @param   boolean  $noredirect      Don't create a redirect.
	 * @param   string   $watchlist       Unconditionally add or remove the page from your watchlist.
	 * @param   boolean  $ignorewarnings  Ignore any warnings.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function movePageByName($from, $to, $reason = null, $movetalk = null, $movesubpages = null, $noredirect = null, $watchlist =null, $ignorewarnings = null)
	{
		// Get the token.
		$token = $this->getToken($from, 'move');

		// Build the request path.
		$path = '?action=move';

		// Build the request data.
		$data = array(
			'from' => $from,
			'to' => $reason,
			'token' => $token,
			'reason' => $reason,
			'movetalk' => $movetalk,
			'movesubpages' => $movesubpages,
			'noredirect' => $noredirect,
			'watchlist' => $watchlist,
			'ignorewarnings' => $ignorewarnings
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
	 * Method to move a page.
	 *
	 * @param   int      $fromid          Page ID of the page you want to move.
	 * @param   string   $to              Title you want to rename the page to.
	 * @param   string   $reason          Reason for the move (optional).
	 * @param   string   $movetalk        Move the talk page, if it exists.
	 * @param   string   $movesubpages    Move subpages, if applicable.
	 * @param   boolean  $noredirect      Don't create a redirect.
	 * @param   string   $watchlist       Unconditionally add or remove the page from your watchlist.
	 * @param   boolean  $ignorewarnings  Ignore any warnings.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function movePageByID($fromid, $to, $reason = null, $movetalk = null, $movesubpages = null, $noredirect = null, $watchlist =null, $ignorewarnings = null)
	{
		// Get the token.
		$token = $this->getToken($fromid, 'move');

		// Build the request path.
		$path = '?action=move';

		// Build the request data.
		$data = array(
			'fromid' => $fromid,
			'to' => $reason,
			'token' => $token,
			'reason' => $reason,
			'movetalk' => $movetalk,
			'movesubpages' => $movesubpages,
			'noredirect' => $noredirect,
			'watchlist' => $watchlist,
			'ignorewarnings' => $ignorewarnings
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
     * Method to undo the last edit to the page.
	 *
	 * @param   string  $title      Title of the page you want to rollback.
	 * @param   string  $user       Name of the user whose edits are to be rolled back.
	 * @param   string  $summary    Custom edit summary. If not set, default summary will be used.
	 * @param   string  $markbot    Mark the reverted edits and the revert as bot edits.
	 * @param   string  $watchlist  Unconditionally add or remove the page from your watchlist.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function rollback($title, $user, $summary = null, $markbot = null, $watchlist = null)
	{
		// Get the token.
		$token = $this->getToken($title, 'rollback');

		// Build the request path.
		$path = '?action=rollback';

		// Build the request data.
		$data = array(
			'title' => $title,
			'token' => $token,
			'user' => $user,
			'expiry' => $summary,
			'markbot' => $markbot,
			'watchlist' => $watchlist
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
     * Method to change the protection level of a page.
	 *
	 * @param   string  $title        Title of the page you want to (un)protect.
	 * @param   string  $protections  Pipe-separated list of protection levels.
	 * @param   string  $expiry       Expiry timestamps.
	 * @param   string  $reason       Reason for (un)protecting (optional).
	 * @param   string  $cascade      Enable cascading protection.
	 * @param   string  $watchlist    Unconditionally add or remove the page from your watchlist.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function changeProtection($title, $protections, $expiry = null, $reason = null, $cascade = null, $watchlist = null)
	{
		// Get the token.
		$token = $this->getToken($title, 'unblock');

		// Build the request path.
		$path = '?action=protect';

		// Build the request data.
		$data = array(
			'title' => $title,
			'token' => $token,
			'protections' => $protections,
			'expiry' => $expiry,
			'reason' => $reason,
			'cascade' => $cascade,
			'watchlist' => $watchlist
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		return $this->validateResponse($response);
	}

	/**
     * Method to get basic page information.
     *
     * @param   array    $titles      Page titles to retrieve info.
     * @param   array    $inprop      Which additional properties to get.
     * @param   array    $intoken     Request a token to perform a data-modifying action on a page
     * @param   boolean  $incontinue  When more results are available, use this to continue.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getPageInfo(array $titles, array $inprop = null, array $intoken = null, $incontinue = null)
	{
		// Build the request
		$path = '?action=query&prop=info';

		// Append titles to the request.
		$path .= '&titles=' . $this->buildParameter($titles);

		if (isset($inprop))
		{
			$path .= '&inprop=' . $this->buildParameter($inprop);
		}

		if (isset($intoken))
		{
			$path .= '&intoken=' . $this->buildParameter($intoken);
		}

		if ($incontinue)
		{
			$path .= '&incontinue=';
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to get various properties defined in the page content.
     *
     * @param   array    $titles      Page titles to retrieve properties.
     * @param   boolean  $ppcontinue  When more results are available, use this to continue.
     * @param   string   $ppprop      Page prop to look on the page for.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getPageProperties(array $titles, $ppcontinue = null, $ppprop = null)
	{
		// Build the request
		$path = '?action=query&prop=pageprops';

		// Append titles to the request.
		$path .= '&titles=' . $this->buildParameter($titles);

		if ($ppcontinue)
		{
			$path .= '&ppcontinue=';
		}

		if (isset($ppprop))
		{
			$path .= '&ppprop=' . $ppprop;
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to get a list of revisions.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getRevisions()
	{
		// TODO hold this at the moment. too many parameters
	}

	/**
     * Method to get a list of deleted revisions.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getDeletedRevisions()
	{
		// @TODO hold to synce with getRevisions
	}

	/**
     * Method to get all page templates from the given page.
     *
     * @param   array    $titles       Page titles to retrieve templates.
     * @param   array    $tlnamespace  Show templates in this namespace(s) only.
     * @param   integer  $tllimit      How many templates to return.
     * @param   boolean  $tlcontinue   When more results are available, use this to continue.
     * @param   string   $tltemplates  Only list these templates.
     * @param   string   $tldir        The direction in which to list.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getPageTemplates(array $titles, array $tlnamespace = null, $tllimit = null, $tlcontinue = null, $tltemplates = null, $tldir = null)
	{
		// Build the request.
		$path = '?action=query&prop=templates';

		// Append titles to the request.
		$path .= '&titles=' . $this->buildParameter($titles);

		if (isset($tlnamespace))
		{
			$path .= '&tlnamespace=' . $this->buildParameter($tlnamespace);
		}

		if (isset($tllimit))
		{
			$path .= '&tllimit=' . $tllimit;
		}

		if ($tlcontinue)
		{
			$path .= '&tlcontinue=';
		}

		if (isset($tltemplates))
		{
			$path .= '&tltemplates=' . $tltemplates;
		}

		if (isset($tldir))
		{
			$path .= '&tldir=' . $tldir;
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to get all pages that link to the given page.
     *
     * @param   string   $bltitle           Title to search.
     * @param   integer  $blpageid          Pageid to search.
     * @param   boolean  $blcontinue        When more results are available, use this to continue.
     * @param   array    $blnamespace       The namespace to enumerate.
     * @param   string   $blfilterredirect  How to filter for redirects..
     * @param   integer  $bllimit           How many total pages to return.
     * @param   boolean  $blredirect        If linking page is a redirect, find all pages that link to that redirect as well.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getBackLinks($bltitle, $blpageid = null, $blcontinue = null, array $blnamespace, $blfilterredirect = null, $bllimit = null, $blredirect = null)
	{
		// Build the request.
		$path = '?action=query&list=backlinks';

		if (isset($bltitle))
		{
			$path .= '&bltitle=' . $bltitle;
		}

		if (isset($blpageid))
		{
			$path .= '&blpageid=' . $blpageid;
		}

		if ($blcontinue)
		{
			$path .= '&blcontinue=';
		}

		if (isset($blnamespace))
		{
			$path .= '&blnamespace=' . $this->buildParameter($blnamespace);
		}

		if (isset($blfilterredirect))
		{
			$path .= '&blfilterredirect=' . $blfilterredirect;
		}

		if (isset($bllimit))
		{
			$path .= '&bllimit=' . $bllimit;
		}

		if ($blredirect)
		{
			$path .= '&blredirect=';
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to get all pages that link to the given interwiki link.
     *
     * @param   string   $iwbltitle     Interwiki link to search for. Must be used with iwblprefix.
     * @param   string   $iwblprefix    Prefix for the interwiki.
     * @param   boolean  $iwblcontinue  When more results are available, use this to continue.
     * @param   integer  $iwbllimit     How many total pages to return.
     * @param   array    $iwblprop      Which properties to get.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getIWBackLinks($iwbltitle, $iwblprefix, $iwblcontinue = null, $iwbllimit = null, array $iwblprop = null)
	{
		// Build the request
		$path = '?action=query&list=iwbacklinks';

		if (isset($iwbltitle))
		{
			$path .= '&iwbltitle=' . $iwbltitle;
		}

		if (isset($iwblprefix))
		{
			$path .= '&iwblprefix=' . $iwblprefix;
		}

		if ($iwblcontinue)
		{
			$path .= '&iwblcontinue=';
		}

		if (isset($iwbllimit))
		{
			$path .= '&bllimit=' . $iwbllimit;
		}

		if (isset($iwblprop))
		{
			$path .= '&iwblprop=' . $this->buildParameter($iwblprop);
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to get all pages that link to the given language link.
	 *
	 * @param   string   $lblang      Language for the language link.
	 * @param   string   $lbtitle     Language link to search for.
	 * @param   boolean  $lbcontinue  When more results are available, use this to continue.
	 * @param   int      $lblimit     How many total pages to return.
	 * @param   array    $lbprop      Which properties to get.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getLangBackLinks($lblang, $lbtitle, $lbcontinue = null, $lblimit = null, $lbprop = null)
	{
	}

	/**
     * Method to get all pages in a given category by page name.
	 *
	 * @param   string  $cmtitle               Which category to enumerate.
	 * @param   array   $cmprop                What pieces of information to include.
	 * @param   array   $cmnamespace           Only include pages in these namespaces.
	 * @param   array   $cmtype                What type of category members to include.
	 * @param   string  $cmcontinue            For large categories, give the value retured from previous query.
	 * @param   int     $cmlimit               The maximum number of pages to return.
	 * @param   string  $cmsort                Property to sort by.
	 * @param   string  $cmdir                 In which direction to sort.
	 * @param   string  $cmstart               Timestamp to start listing from.
	 * @param   string  $cmend                 Timestamp to end listing at.
	 * @param   string  $cmstartsortkey        Sortkey to start listing from.
	 * @param   string  $cmendsortkey          Sortkey to end listing at.
	 * @param   string  $cmstartsortkeyprefix  Sortkey prefix to start listing from.
	 * @param   string  $cmendsortkeyprefix    Sortkey prefix to end listing BEFORE.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getCategoryPagesByname($cmtitle, array $cmprop = null, array $cmnamespace = null, array $cmtype = null, $cmcontinue = null, $cmlimit = null, $cmsort = null, $cmdir = null, $cmstart = null, $cmend = null, $cmstartsortkey = null, $cmendsortkey = null, $cmstartsortkeyprefix = null, $cmendsortkeyprefix = null)
	{
	}

	/**
	 * Method to get all pages in a given category by page id.
	 *
	 * @param   string  $cmpageid              Page ID of the category to enumerate.
	 * @param   array   $cmprop                What pieces of information to include.
	 * @param   array   $cmnamespace           Only include pages in these namespaces.
	 * @param   array   $cmtype                What type of category members to include.
	 * @param   string  $cmcontinue            For large categories, give the value retured from previous query.
	 * @param   int     $cmlimit               The maximum number of pages to return.
	 * @param   string  $cmsort                Property to sort by.
	 * @param   string  $cmdir                 In which direction to sort.
	 * @param   string  $cmstart               Timestamp to start listing from.
	 * @param   string  $cmend                 Timestamp to end listing at.
	 * @param   string  $cmstartsortkey        Sortkey to start listing from.
	 * @param   string  $cmendsortkey          Sortkey to end listing at.
	 * @param   string  $cmstartsortkeyprefix  Sortkey prefix to start listing from.
	 * @param   string  $cmendsortkeyprefix    Sortkey prefix to end listing BEFORE.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function getCategoryPagesByID($cmpageid, array $cmprop = null, array $cmnamespace = null, array $cmtype = null, $cmcontinue = null, $cmlimit = null, $cmsort = null, $cmdir = null, $cmstart = null, $cmend = null, $cmstartsortkey = null, $cmendsortkey = null, $cmstartsortkeyprefix = null, $cmendsortkeyprefix = null)
	{
	}

	/**
     * Method to get all pages  that use the given image title.
	 *
	 * @param   string   $bltitle        Title to search.
	 * @param   boolean  $blcontinue     When more results are available, use this to continue.
	 * @param   array    $blnamespace    The namespace to enumerate.
	 * @param   string   $blfilterredir  How to filter for redirects.
	 * @param   int      $bllimit        How many total pages to return.
	 * @param   boolean  $blredirect     If linking page is a redirect.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getImageUsage($bltitle, $blcontinue = null, array $blnamespace = null, $blfilterredir, $bllimit = null, $blredirect = null)
	{
	}

	/**
	 * Method to get access token.
	 *
	 * @param   string  $user     The User to get token.
	 * @param   string  $intoken  The type of token.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function getToken($user, $intoken)
	{
		// Build the request path.
		$path = '?action=query&prop=info&intoken=' . $intoken . '&titles=User:' . $user;

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), null);

		return (string) $this->validateResponse($response)->query->pages->page[$intoken . 'token'];
	}
}
