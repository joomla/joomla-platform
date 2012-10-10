<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Google
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Google Picasa data class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Google
 * @since       1234
 */
class JGoogleDataPicasaAlbum extends JGoogleData
{
	/**
	 * @var    SimpleXMLElement  The album's XML
	 * @since  1234
	 */
	protected $xml;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry    $options  Google options object
	 * @param   JGoogleAuth  $auth     Google data http client object
	 *
	 * @since   1234
	 */
	public function __construct(SimpleXMLElement $xml, JRegistry $options = null, JGoogleAuth $auth = null)
	{
		$this->xml = $xml;

		$options = isset($options) ? $options : new JRegistry;
		if (!$options->get('scope'))
		{
			$options->set('scope', 'https://picasaweb.google.com/data/');
		}
		if (isset($auth) && !$auth->getOption('scope'))
		{
			$auth->setOption('scope', 'https://picasaweb.google.com/data/');
		}

		parent::__construct($options, $auth);
	}

	/**
	 * Method to delete a Picasa album
	 *
	 * @param   mixed  $match  Check for most up to date album
	 *
	 * @return  bool  Success or failure.
	 *
	 * @since   1234
	 * @throws UnexpectedValueException
	 */
	public function delete($match = '*')
	{
		if ($this->authenticated())
		{
			$url = $this->getLink();
			if ($match === true)
			{
				$match = $this->xml->xpath('./@gd:etag');
                $match = $match[0];
			}

			try
			{
				$jdata = $this->auth->query($url, null, array('GData-Version' => 2, 'If-Match' => $match), 'delete');
			}
			catch (Exception $e)
			{
				if ($jdata->code == 412)
				{
					throw new RuntimeException("Etag match failed: `$match`.");
				}
				throw $e;
			}

            if ($jdata->body  != '')
			{
				throw new UnexpectedValueException("Unexpected data received from Google: `{$jdata->body}`.");
			}
			$this->xml = null;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get the album link
	 *
	 * @param   string  $type  Type of link to return
	 *
	 * @return  string  Link or false on failure
	 *
	 * @since   1234
	 */
	public function getLink($type = 'edit')
	{
		$links = $this->xml->link;
		foreach($links as $link)
		{
			if ($link->attributes()->rel == $type)
			{
				return (string) $link->attributes()->href;
			}
		}
		return false;
	}

	/**
	 * Method to get the title of the album
	 *
	 * @return  string  Album title
	 *
	 * @since   1234
	 */
	public function getTitle()
	{
		return (string) $this->xml->children()->title;
	}

	/**
	 * Method to get the summary of the album
	 *
	 * @return  string  Album summary
	 *
	 * @since   1234
	 */
	public function getSummary()
	{
		return (string) $this->xml->children()->summary;
	}

	/**
	 * Method to get the location of the album
	 *
	 * @return  string  Album location
	 *
	 * @since   1234
	 */
	public function getLocation()
	{
		return (string) $this->xml->children('gphoto')->location;
	}

	/**
	 * Method to get the access level of the album
	 *
	 * @return  string  Album access level
	 *
	 * @since   1234
	 */
	public function getAccess()
	{
		return (string) $this->xml->children('gphoto')->access;
	}

	/**
	 * Method to get the time of the album
	 *
	 * @return  int  Album time
	 *
	 * @since   1234
	 */
	public function getTime()
	{
		return (int) $this->xml->children('gphoto')->timestamp;
		return $this;
	}

	/**
	 * Method to get the title of the album
	 *
	 * @param   string  $title  New album title
	 *
	 * @return  JGoogleDataPicasaAlbum  The object for method chaining
	 *
	 * @since   1234
	 */
	public function setTitle($title)
	{
		$this->xml->children()->title = $title;
		return $this;
	}

	/**
	 * Method to get the summary of the album
	 *
	 * @param   string  $summary  New album summary
	 *
	 * @return  JGoogleDataPicasaAlbum  The object for method chaining
	 *
	 * @since   1234
	 */
	public function setSummary($summary)
	{
		$this->xml->children()->summary = $summary;
		return $this;
	}

	/**
	 * Method to get the location of the album
	 *
	 * @param   string  $location  New album location
	 *
	 * @return  JGoogleDataPicasaAlbum  The object for method chaining
	 *
	 * @since   1234
	 */
	public function setLocation($location)
	{
		$this->xml->children('gphoto')->location = $location;
		return $this;
	}

	/**
	 * Method to get the access level of the album
	 *
	 * @param   string  $access  New album access
	 *
	 * @return  JGoogleDataPicasaAlbum  The object for method chaining
	 *
	 * @since   1234
	 */
	public function setAccess($access)
	{
		$this->xml->children('gphoto')->access = $access;
		return $this;
	}

	/**
	 * Method to get the time of the album
	 *
	 * @param   int  $title  New album time
	 *
	 * @return  JGoogleDataPicasaAlbum  The object for method chaining
	 *
	 * @since   1234
	 */
	public function setTime($time)
	{
		$this->xml->children('gphoto')->timestamp = $time;
		return $this;
	}

	/**
	 * Method to modify a Picasa Album
	 *
	 * @param   string  $url      URL of album to delete
	 * @param   array   $options  Album settings
	 * @param   string  $match    Optional eTag matching parameter
	 *
	 * @return  mixed  Data from Google.
	 *
	 * @since   1234
	 */
	public function save($match = '*')
	{
		if ($this->authenticated())
		{
			$url = $this->getLink();
			if ($match === true)
			{
				$match = $this->xml->xpath('./@gd:etag');
                $match = $match[0];
			}

			try
			{
                sleep(25);
				$jdata = $this->auth->query($url, $this->xml->asXML(), array('GData-Version' => 2, 'Content-type' => 'application/atom+xml', 'If-Match' => $match), 'put');
			}
			catch (Exception $e)
			{
				if (strpos($e->getMessage(), 'Error code 412 received requesting data: Mismatch: etags') === 0)
				{
					throw new RuntimeException("Etag match failed: `$match`.");
				}
				throw $e;
			}

			$this->xml = $this->safeXML($jdata->body);
			return $this;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get Picasa Album
	 *
	 * @param   string  $url  URL of album to get
	 *
	 * @return  mixed  Data from Google
	 *
	 * @since   1234
	 * @throws UnexpectedValueException
	 */
	public function refresh()
	{
		if ($this->authenticated())
		{
			$url = $this->getLink();
			$jdata = $this->auth->query($url, null, array('GData-Version' => 2));
			$this->xml = $this->safeXML($jdata->body);echo $jdata->body;
			return $this;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to retrieve a list of Picasa Photos
	 *
	 * @return  mixed  Data from Google
	 *
	 * @since   1234
	 * @throws UnexpectedValueException
	 */
	public function listPhotos()
	{
		if ($this->authenticated())
		{
			$url = $this->getLink('http://schemas.google.com/g/2005#feed');
			$jdata = $this->auth->query($url, null, array('GData-Version' => 2));
			$xml = $this->safeXML($jdata->body);
			if (isset($xml->children()->entry))
			{
				$items = array();
				foreach ($xml->children()->entry as $item)
				{
					$items[] = new JGoogleDataPicasaPhoto($item, $this->options, $this->auth);
				}
				return $items;
			}
			else
			{
				throw new UnexpectedValueException("Unexpected data received from Google: `{$jdata->body}`.");
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Add photo
	 *
	 * @param   string  $url  URL of album to get
	 *
	 * @return  mixed  Data from Google
	 *
	 * @since   1234
	 * @throws UnexpectedValueException
	 */
	public function upload($file, $title = '', $summary = '')
	{
		if ($this->authenticated())
		{
			$title = $title != '' ? $title : JFile::getName($file);
			if (!($type = $this->getMIME($file)))
			{
				throw new RuntimeException("Inappropriate file type.");
			}
			if (!($data = JFile::read($file)))
			{
				throw new RuntimeException("Cannot access file: `$file`");
			}

			$xml = new SimpleXMLElement('<entry></entry>');
			$xml->addAttribute('xmlns', 'http://www.w3.org/2005/Atom');
			$xml->addChild('title', $title);
			$xml->addChild('summary', $summary);
			$cat = $xml->addChild('category', '');
			$cat->addAttribute('scheme', 'http://schemas.google.com/g/2005#kind');
			$cat->addAttribute('term', 'http://schemas.google.com/photos/2007#photo');

			$post = "Media multipart posting\n";
            $post .= "--END_OF_PART\n";
            $post .= "Content-Type: application/atom+xml\n\n";
            $post .= $xml->asXML()."\n";
            $post .= "--END_OF_PART\n";
            $post .= "Content-Type: {$type}\n\n";
            $post .= $data;

			$jdata = $this->auth->query($this->getLink(), $data, array('GData-Version' => 2, 'Content-Type: multipart/related'), 'post');
			return $this->safeXML($jdata->body);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Add photo
	 *
	 * @param   string  $url  URL of album to get
	 *
	 * @return  mixed  Data from Google
	 *
	 * @since   1234
	 * @throws UnexpectedValueException
	 */
	protected function getMIME($file)
	{
		switch (strtolower(JFile::getExt($file)))
		{
			case 'bmp':
			case 'bm':
			return 'image/bmp';
			case 'gif':
			return 'image/gif';
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'jif':
			case 'jfif':
			case 'jfi':
			return 'image/jpeg';
			case 'png':
			return 'image/png';
			case '3gp':
			return 'video/3gpp';
			case 'avi':
			return 'video/avi';
			case 'mov':
			case 'moov':
			case 'qt':
			return 'video/quicktime';
			case 'mp4':
			case 'm4a':
			case 'm4p':
			case 'm4b':
			case 'm4r':
			case 'm4v':
			return 'video/mp4';
			case 'mpg':
			case 'mpeg':
			case 'mp1':
			case 'mp2':
			case 'mp3':
			case 'm1v':
			case 'm1a':
			case 'm2a':
			case 'mpa':
			case 'mpv':
			return 'video/mpeg';
			case 'asf':
			return 'video/x-ms-asf';
			case 'wmv':
			return 'video/x-ms-wmv';
			default:
			return false;
        }
	}
}
