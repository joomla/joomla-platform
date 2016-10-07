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
 * Openstreetmap API Elements class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
class JOpenstreetmapElements extends JOpenstreetmapObject
{
	/**
	 * Method to create a node
	 * 
	 * @param   int    $changeset  change set id
	 * @param   float  $latitude   latitude of the node
	 * @param   float  $longitude  longitude of the node
	 * @param   arary  $tags       array of tags for a node
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 */
	public function createNode($changeset,$latitude,$longitude,$tags)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'node/create';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$tag_list = "";

		// Create XML node
		if (!empty($tags))
		{
			foreach ($tags as $key => $value)
			{
				$tag_list .= '<tag k="' . $key . '" v="' . $value . '"/>';
			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<node changeset="' . $changeset . '" lat="' . $latitude . '" lon="' . $longitude . '">'
				. $tag_list .
				'</node>
				</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Method to create a way
	 * 
	 * @param   int    $changeset  change set id
	 * @param   array  $tags       array of tags for a way
	 * @param   array  $nds        node ids to refer
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 */
	public function createWay($changeset,$tags,$nds)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'way/create';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$tag_list = "";

		// Create XML node
		if (!empty($tags))
		{
			foreach ($tags as $key => $value)
			{
				$tag_list .= '<tag k="' . $key . '" v="' . $value . '"/>';
			}
		}

		$nd_list = "";

		if (!empty($nds))
		{
			foreach ($nds as $value)
			{
				$nd_list .= '<nd ref="' . $value . '"/>';
			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<way changeset="' . $changeset . '">'
					. $tag_list
					. $nd_list .
				'</way>
			</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Method to create a relation
	 * 
	 * @param   int    $changeset  change set id
	 * @param   array  $tags       array of tags for a relation
	 * @param   array  $members    array of members for a relation 
	 *                             eg:$members=array(array("type"=>"node","role"=>"stop","ref"=>"123"),array("type"=>"way","ref"=>"123"))
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 */
	public function createRelation($changeset,$tags,$members)
	{
		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = 'relation/create';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$tag_list = "";

		// Create XML node
		if (!empty($tags))
		{
			foreach ($tags as $key => $value)
			{
				$tag_list .= '<tag k="' . $key . '" v="' . $value . '"/>';
			}
		}

		// Members
		$member_list = "";

		if (!empty($members))
		{
			foreach ($members as $member)
			{
				if ($member['type'] == "node")
				{
					$member_list .= '<member type="' . $member['type'] . '" role="' . $member['role'] . '" ref="' . $member['ref'] . '"/>';
				}
				elseif ($member['type'] == "way")
				{
					$member_list .= '<member type="' . $member['type'] . '" ref="' . $member['ref'] . '"/>';
				}
			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<relation relation="' . $changeset . '" >'
					. $tag_list
					. $member_list .
				'</relation>
			</osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Method to read an Element [node|way|relation]
	 *
	 * @param   string  $element  [node|way|relation]
	 * @param   int     $id       element identifier
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function readElement($element, $id)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		// Set the API base
		$base = $element . '/' . $id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->$element;
	}

	/**
	 * Method to update an Element [node|way|relation]
	 *
	 * @param   string  $element  [node|way|relation]
	 * @param   string  $xml      full reperentation of the element with a version number
	 * @param   int     $id       element identifier
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function updateElement($element, $xml, $id)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = $element . '/' . $id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Method to delete an element [node|way|relation]
	 * 
	 * @param   string  $element    [node|way|relation]
	 * @param   int     $id         element identifier
	 * @param   int     $version    element versioln
	 * @param   int     $changeset  changeset identifier
	 * @param   float   $latitude   latitude of the element
	 * @param   float   $longitude  longitude of the element
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function deleteElement($element, $id, $version, $changeset, $latitude=null, $longitude=null)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = $element . '/' . $id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Create xml
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<osm version="0.6" generator="JOpenstreetmap">
				<' . $element . ' id="' . $id . '" version="' . $version . '" changeset="' . $changeset . '"';

		if (!empty($latitude) && !empty($longitude))
		{
			$xml .= ' lat="' . $latitude . '" lon="' . $longitude . '"';
		}
		$xml .= '/></osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'DELETE', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Method to get history of an element [node|way|relation]
	 *
	 * @param   string  $element  [node|way|relation]
	 * @param   int     $id       element identifier
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function historyOfElement($element, $id)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		// Set the API base
		$base = $element . '/' . $id . '/history';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->$element;
	}

	/**
	 * Method to get details about a version of an element [node|way|relation]
	 *
	 * @param   string  $element  [node|way|relation]
	 * @param   int     $id       element identifier
	 * @param   int     $version  element version
	 * 
	 * @return  array    The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function versionOfElement($element, $id ,$version)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		// Set the API base
		$base = $element . '/' . $id . '/' . $version;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->$element;
	}

	/**
	 * Method to get data about multiple ids of an element [node|way|relation]
	 *
	 * @param   string  $element  [nodes|ways|relations] - use plural word
	 * @param   string  $params   Comma separated list ids belongto type $element
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function multiFetchElements($element, $params)
	{
		if ($element != 'nodes' && $element != 'ways' && $element != 'relations')
		{
			throw new DomainException("Element should be nodes, ways or relations");
		}

		// Get singular word
		$single_element = substr($element, 0, strlen($element) - 1);

		// Set the API base, $params is a string with comma seperated values
		$base = $element . '?' . $element . "=" . $params;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->$single_element;
	}

	/**
	 * Method to get relations for an Element [node|way|relation]
	 *
	 * @param   string  $element  [node|way|relation]
	 * @param   int     $id       element identifier
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function relationsForElement($element, $id)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		// Set the API base
		$base = $element . '/' . $id . '/relations';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->$element;
	}

	/**
	 * Method to get ways for a Node element
	 *
	 * @param   int  $id  node identifier
	 * 
	 * @return  array    The xml response
	 * 
	 * @since   12.3
	 */
	public function waysForNode($id)
	{
		// Set the API base
		$base = 'node/' . $id . '/ways';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->way;
	}

	/**
	 * Method to get full information about an element [way|relation]
	 *
	 * @param   string  $element  [way|relation]
	 * @param   int     $id       identifier
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function fullElement($element, $id)
	{
		if ($element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a way or a relation");
		}

		// Set the API base
		$base = $element . '/' . $id . '/full';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$xml_string = $this->sendRequest($path);

		return $xml_string->node;
	}

	/**
	 * Method used by the DWG to hide old versions of elements containing data privacy or copyright infringements
	 *  
	 * @param   string  $element       [node|way|relation]
	 * @param   int     $id            element identifier
	 * @param   int     $version       element version
	 * @param   int     $redaction_id  redaction id
	 * 
	 * @return  array   The xml response
	 * 
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function redaction($element, $id, $version, $redaction_id)
	{
		if ($element != 'node' && $element != 'way' && $element != 'relation')
		{
			throw new DomainException("Element should be a node, a way or a relation");
		}

		$token = $this->oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = $element . '/' . $id . '/' . $version . '/redact?redaction=' . $redaction_id;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $this->oauth->oauthRequest($path, 'PUT', $parameters);

		$xml_string = simplexml_load_string($response->body);

		return $xml_string;
	}
}
