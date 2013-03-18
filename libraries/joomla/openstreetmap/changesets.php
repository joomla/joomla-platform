<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Openstreetmap API Changesets class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @since       13.1
 */
class JOpenstreetmapChangesets extends JOpenstreetmapObject
{
	/**
	 * Method to create a changeset
	 * 
	 * @param   array  $changesets  array which contains changeset data
	 * 
	 * @return  array  The xml response
	 * 
	 * @since   13.1
	 */
	public function createChangeset($changesets=array())
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key'],
				'oauth_token_secret' => $token['secret']
		);

		// Set the API base
		$base = 'changeset/create';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
			<osm version="0.6" generator="JOpenstreetmap">';

		if (!empty($changesets))
		{
			// Create Changeset element for every changeset
			foreach ($changesets as $tags)
			{
				$xml .= '<changeset>';
				$tag_list = '';

				if (!empty($tags))
				{
					// Create a list of tags for each changeset
					foreach ($tags as $key => $value)
					{

						$xml .= '<tag k="' . $key . '" v="' . $value . '"/>';

					}
				}
				$xml .= '</changeset>';
			}
		}

		$xml .= '</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;

	}

	/**
	 * Method to read a changeset
	 * 
	 * @param   int  $id  identifier of the changeset
	 * 
	 * @return  array    The xml response about a changeset
	 *  
	 * @since   13.1
	 */
	public function readChangeset($id)
	{

		// Set the API base
		$base = 'changeset/' . $id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->changeset;

	}

	/**
	 * Method to update a changeset
	 * 
	 * @param   int    $id    identifier of the changeset
	 * @param   array  $tags  array of tags to update
	 * 
	 * @return  array    The xml response of updated changeset
	 * 
	 * @since   13.1 
	 */
	public function updateChangeset($id, $tags=array() )
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'changeset/' . $id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Create a list of tags to update changeset
		$tag_list = '';

		if (!empty($tags))
		{
			foreach ($tags as $key => $value)
			{

				$tag_list .= '<tag k="' . $key . '" v="' . $value . '"/>';

			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<changeset>'
				. $tag_list .
				'</changeset>  
				</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		$xml_string = simplexml_load_string($response->body);

		return $xml_string->changeset;
	}

	/**
	 * Method to close a changeset
	 * 
	 * @param   int  $id  identifier of the changeset
	 * 
	 * @return  No value returns
	 * 
	 * @since   13.1
	 */
	public function closeChangeset($id)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'changeset/' . $id . '/close';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$header['format'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $header);

	}

	/**
	 * Method to download a changeset
	 * 
	 * @param   int  $id  identifier of the changeset
	 * 
	 * @return  array	The xml response of requested changeset
	 * 
	 * @since   13.1
	 */
	public function downloadChangeset($id)
	{

		// Set the API base
		$base = 'changeset/' . $id . '/download';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->create;
	}

	/**
	 * Method to expand the bounding box of a changeset
	 * 
	 * @param   int    $id     identifier of the changeset
	 * @param   array  $nodes  list of lat lon about nodes
	 * 
	 * @return  array    The xml response of changed changeset
	 * 
	 * @since   13.1
	 */
	public function expandBBoxChangeset($id, $nodes)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'changeset/' . $id . '/expand_bbox';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Create a list of tags to update changeset
		$node_list = '';

		if (!empty($nodes))
		{
			foreach ($nodes as $node)
			{

				$node_list .= '<node lat="' . $node[0] . '" lon="' . $node[1] . '"/>';

			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<changeset>'
				. $node_list .
				'</changeset>
			</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'POST', $parameters, $xml, $header);

		$xml_string = simplexml_load_string($response->body);

		return $xml_string->changeset;
	}

	/**
	 * Method to Query on changesets
	 *  
	 * @param   string  $param  parameters for query
	 * 
	 * @return  array    The xml response
	 * 
	 * @since   13.1
	 */
	public function queryChangeset($param)
	{
		// Set the API base
		$base = 'changesets/' . $param;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->osm;
	}

	/**
	 * Method to upload a diff to a changeset
	 * 
	 * @param   string  $xml  diff data to upload
	 * @param   int     $id   identifier of the changeset
	 * 
	 * @return  array    The xml response of result
	 * 
	 * @since   13.1
	 */
	public function diffUploadChangeset($xml, $id)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'changeset/' . $id . '/upload';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'POST', $parameters, $xml, $header);

		$xml_string = simplexml_load_string($response->body);

		return $xml_string->diffResult;
	}
}
