<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDate is a class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 *
 * @package     Joomla.Platform
 * @subpackage  Utilities
 * @since       11.1
 */
class JDate extends DateTime
{
	const DAY_ABBR = "\x021\x03";
	const DAY_NAME = "\x022\x03";
	const MONTH_ABBR = "\x023\x03";
	const MONTH_NAME = "\x024\x03";

	/**
	 * The format string to be applied when using the __toString() magic method.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public static $format = 'Y-m-d H:i:s';

	/**
	 * Placeholder for a DateTimeZone object with GMT as the time zone.
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected static $gmt;

	/**
	 * Placeholder for a DateTimeZone object with the default server
	 * time zone as the time zone.
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected static $stz;

	/**
	 * An array of offsets and time zone strings representing the available
	 * options from Joomla! CMS 1.5 and below.
	 *
	 * @deprecated    12.1
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $offsets = array('-12' => 'Etc/GMT-12', '-11' => 'Pacific/Midway', '-10' => 'Pacific/Honolulu', '-9.5' => 'Pacific/Marquesas',
		'-9' => 'US/Alaska', '-8' => 'US/Pacific', '-7' => 'US/Mountain', '-6' => 'US/Central', '-5' => 'US/Eastern', '-4.5' => 'America/Caracas',
		'-4' => 'America/Barbados', '-3.5' => 'Canada/Newfoundland', '-3' => 'America/Buenos_Aires', '-2' => 'Atlantic/South_Georgia',
		'-1' => 'Atlantic/Azores', '0' => 'Europe/London', '1' => 'Europe/Amsterdam', '2' => 'Europe/Istanbul', '3' => 'Asia/Riyadh',
		'3.5' => 'Asia/Tehran', '4' => 'Asia/Muscat', '4.5' => 'Asia/Kabul', '5' => 'Asia/Karachi', '5.5' => 'Asia/Calcutta',
		'5.75' => 'Asia/Katmandu', '6' => 'Asia/Dhaka', '6.5' => 'Indian/Cocos', '7' => 'Asia/Bangkok', '8' => 'Australia/Perth',
		'8.75' => 'Australia/West', '9' => 'Asia/Tokyo', '9.5' => 'Australia/Adelaide', '10' => 'Australia/Brisbane',
		'10.5' => 'Australia/Lord_Howe', '11' => 'Pacific/Kosrae', '11.5' => 'Pacific/Norfolk', '12' => 'Pacific/Auckland',
		'12.75' => 'Pacific/Chatham', '13' => 'Pacific/Tongatapu', '14' => 'Pacific/Kiritimati');

	/**
	 * The DateTimeZone object for usage in rending dates as strings.
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected $_tz;

	/**
	 * Constructor.
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 *
	 * @since   11.1
	 *
	 * @throws  JException
	 */
	public function __construct($date = 'now', $tz = null)
	{
		// Create the base GMT and server time zone objects.
		if (empty(self::$gmt) || empty(self::$stz))
		{
			self::$gmt = new DateTimeZone('GMT');
			self::$stz = new DateTimeZone(@date_default_timezone_get());
		}

		// If the time zone object is not set, attempt to build it.
		if (!($tz instanceof DateTimeZone))
		{
			if ($tz === null)
			{
				$tz = self::$gmt;
			}
			elseif (is_numeric($tz))
			{
				// Translate from offset.
				$tz = new DateTimeZone(self::$offsets[(string) $tz]);
			}
			elseif (is_string($tz))
			{
				$tz = new DateTimeZone($tz);
			}
		}

		// If the date is numeric assume a unix timestamp and convert it.
		date_default_timezone_set('UTC');
		$date = is_numeric($date) ? date('c', $date) : $date;

		// Call the DateTime constructor.
		parent::__construct($date, $tz);

		// reset the timezone for 3rd party libraries/extension that does not use JDate
		date_default_timezone_set(self::$stz->getName());

		// Set the timezone object for access later.
		$this->_tz = $tz;
	}

	/**
	 * Magic method to access properties of the date given by class to the format method.
	 *
	 * @param   string  $name  The name of the property.
	 *
	 * @return  mixed   A value if the property name is valid, null otherwise.
	 *
	 * @since   11.1
	 */
	public function __get($name)
	{
		$value = null;

		switch ($name)
		{
			case 'daysinmonth':
				$value = $this->format('t', true);
				break;

			case 'dayofweek':
				$value = $this->format('N', true);
				break;

			case 'dayofyear':
				$value = $this->format('z', true);
				break;

			case 'isleapyear':
				$value = (boolean) $this->format('L', true);
				break;

			case 'day':
				$value = $this->format('d', true);
				break;

			case 'hour':
				$value = $this->format('H', true);
				break;

			case 'minute':
				$value = $this->format('i', true);
				break;

			case 'second':
				$value = $this->format('s', true);
				break;

			case 'month':
				$value = $this->format('m', true);
				break;

			case 'ordinal':
				$value = $this->format('S', true);
				break;

			case 'week':
				$value = $this->format('W', true);
				break;

			case 'year':
				$value = $this->format('Y', true);
				break;

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE
				);
		}

		return $value;
	}

	/**
	 * Magic method to render the date object in the format specified in the public
	 * static member JDate::$format.
	 *
	 * @return  string  The date as a formatted string.
	 *
	 * @since   11.1
	 */
	public function __toString()
	{
		return (string) parent::format(self::$format);
	}

	/**
	 * Proxy for new JDate().
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 *
	 * @return  JDate
	 *
	 * @since   11.3
	 * @throws  JException
	 */
	public static function getInstance($date = 'now', $tz = null)
	{
		return new JDate($date, $tz);
	}

	/**
	 * Translates day of week number to a string.
	 *
	 * @param   integer  $day   The numeric day of the week.
	 * @param   boolean  $abbr  Return the abbreviated day string?
	 *
	 * @return  string  The day of the week.
	 *
	 * @since   11.1
	 */
	public function dayToString($day, $abbr = false)
	{
		switch ($day)
		{
			case 0:
				return $abbr ? JText::_('SUN') : JText::_('SUNDAY');
			case 1:
				return $abbr ? JText::_('MON') : JText::_('MONDAY');
			case 2:
				return $abbr ? JText::_('TUE') : JText::_('TUESDAY');
			case 3:
				return $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
			case 4:
				return $abbr ? JText::_('THU') : JText::_('THURSDAY');
			case 5:
				return $abbr ? JText::_('FRI') : JText::_('FRIDAY');
			case 6:
				return $abbr ? JText::_('SAT') : JText::_('SATURDAY');
		}
	}

	/**
	 * Gets the date as a formatted string in a local calendar.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   11.1
	 */
	public function calendar($format, $local = false, $translate = true)
	{
		return $this->format($format, $local, $translate);
	}

	/**
	 * Gets the date as a formatted string.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   11.1
	 */
	public function format($format, $local = false, $translate = true)
	{
		if ($translate)
		{
			// Do string replacements for date format options that can be translated.
			$format = preg_replace('/(^|[^\\\])D/', "\\1" . self::DAY_ABBR, $format);
			$format = preg_replace('/(^|[^\\\])l/', "\\1" . self::DAY_NAME, $format);
			$format = preg_replace('/(^|[^\\\])M/', "\\1" . self::MONTH_ABBR, $format);
			$format = preg_replace('/(^|[^\\\])F/', "\\1" . self::MONTH_NAME, $format);
		}

		// If the returned time should not be local use GMT.
		if ($local == false)
		{
			parent::setTimezone(self::$gmt);
		}

		// Format the date.
		$return = parent::format($format);

		if ($translate)
		{
			// Manually modify the month and day strings in the formatted time.
			if (strpos($return, self::DAY_ABBR) !== false)
			{
				$return = str_replace(self::DAY_ABBR, $this->dayToString(parent::format('w'), true), $return);
			}

			if (strpos($return, self::DAY_NAME) !== false)
			{
				$return = str_replace(self::DAY_NAME, $this->dayToString(parent::format('w')), $return);
			}

			if (strpos($return, self::MONTH_ABBR) !== false)
			{
				$return = str_replace(self::MONTH_ABBR, $this->monthToString(parent::format('n'), true), $return);
			}

			if (strpos($return, self::MONTH_NAME) !== false)
			{
				$return = str_replace(self::MONTH_NAME, $this->monthToString(parent::format('n')), $return);
			}
		}

		if ($local == false)
		{
			parent::setTimezone($this->_tz);
		}

		return $return;
	}

	/**
	 * Get the time offset from GMT in hours or seconds.
	 *
	 * @param   boolean  $hours  True to return the value in hours.
	 *
	 * @return  float  The time offset from GMT either in hours or in seconds.
	 *
	 * @since   11.1
	 */
	public function getOffsetFromGMT($hours = false)
	{
		return (float) $hours ? ($this->_tz->getOffset($this) / 3600) : $this->_tz->getOffset($this);
	}

	/**
	 * Translates month number to a string.
	 *
	 * @param   integer  $month  The numeric month of the year.
	 * @param   boolean  $abbr   If true, return the abbreviated month string
	 *
	 * @return  string  The month of the year.
	 *
	 * @since   11.1
	 */
	public function monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:
				return $abbr ? JText::_('JANUARY_SHORT') : JText::_('JANUARY');
			case 2:
				return $abbr ? JText::_('FEBRUARY_SHORT') : JText::_('FEBRUARY');
			case 3:
				return $abbr ? JText::_('MARCH_SHORT') : JText::_('MARCH');
			case 4:
				return $abbr ? JText::_('APRIL_SHORT') : JText::_('APRIL');
			case 5:
				return $abbr ? JText::_('MAY_SHORT') : JText::_('MAY');
			case 6:
				return $abbr ? JText::_('JUNE_SHORT') : JText::_('JUNE');
			case 7:
				return $abbr ? JText::_('JULY_SHORT') : JText::_('JULY');
			case 8:
				return $abbr ? JText::_('AUGUST_SHORT') : JText::_('AUGUST');
			case 9:
				return $abbr ? JText::_('SEPTEMBER_SHORT') : JText::_('SEPTEMBER');
			case 10:
				return $abbr ? JText::_('OCTOBER_SHORT') : JText::_('OCTOBER');
			case 11:
				return $abbr ? JText::_('NOVEMBER_SHORT') : JText::_('NOVEMBER');
			case 12:
				return $abbr ? JText::_('DECEMBER_SHORT') : JText::_('DECEMBER');
		}
	}

	/**
	 * Set the date offset (in hours).
	 *
	 * @param   float  $offset  The offset in hours.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1  Use setTimezone instead.
	 */
	public function setOffset($offset)
	{
		// Deprecation warning.
		JLog::add('JDate::setOffset() is deprecated.', JLog::WARNING, 'deprecated');

		// Only set the timezone if the offset exists.
		if (isset(self::$offsets[(string) $offset]))
		{
			$this->_tz = new DateTimeZone(self::$offsets[(string) $offset]);
			$this->setTimezone($this->_tz);
			return true;
		}

		return false;
	}

	/**
	 * Method to wrap the setTimezone() function and set the internal
	 * time zone object.
	 *
	 * @param   object  $tz  The new DateTimeZone object.
	 *
	 * @return  DateTimeZone  The old DateTimeZone object.
	 *
	 * @since   11.1
	 */
	public function setTimezone($tz)
	{
		$this->_tz = $tz;
		return parent::setTimezone($tz);
	}

	/**
	 * Gets the date in a specific format
	 *
	 * Returns a string formatted according to the given format. Month and weekday names and
	 * other language dependent strings respect the current locale
	 *
	 * @param   string   $format  The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @param   boolean  $local   True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string   The date as a formatted string.
	 *
	 * @deprecated  Use JDate::format() instead.
	 *
	 * @deprecated  12.1 Use JDate::format() instead.
	 */
	public function toFormat($format = '%Y-%m-%d %H:%M:%S', $local = false)
	{
		// Deprecation warning.
		JLog::add('JDate::toFormat() is deprecated.', JLog::WARNING, 'deprecated');

		// Set time zone to GMT as strftime formats according locale setting.
		date_default_timezone_set('GMT');

		// Generate the timestamp.
		$time = (int) parent::format('U');

		// If the returned time should be local add the GMT offset.
		if ($local)
		{
			$time += $this->getOffsetFromGMT();
		}

		// Manually modify the month and day strings in the format.
		if (strpos($format, '%a') !== false)
		{
			$format = str_replace('%a', $this->dayToString(date('w', $time), true), $format);
		}
		if (strpos($format, '%A') !== false)
		{
			$format = str_replace('%A', $this->dayToString(date('w', $time)), $format);
		}
		if (strpos($format, '%b') !== false)
		{
			$format = str_replace('%b', $this->monthToString(date('n', $time), true), $format);
		}
		if (strpos($format, '%B') !== false)
		{
			$format = str_replace('%B', $this->monthToString(date('n', $time)), $format);
		}

		// Generate the formatted string.
		$date = strftime($format, $time);

		// reset the timezone for 3rd party libraries/extension that does not use JDate
		date_default_timezone_set(self::$stz->getName());

		return $date;
	}

	/**
	 * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
	 * and it can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string  The date string in ISO 8601 format.
	 *
	 * @link    http://www.ietf.org/rfc/rfc3339.txt
	 * @since   11.1
	 */
	public function toISO8601($local = false)
	{
		return $this->format(DateTime::RFC3339, $local, false);
	}

	/**
	 * Gets the date as an MySQL datetime string.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string   The date string in MySQL datetime format.
	 *
	 * @link http://dev.mysql.com/doc/refman/5.0/en/datetime.html
	 * @since   11.1
	 * @deprecated 12.1 Use JDate::toSql()
	 */
	public function toMySQL($local = false)
	{
		JLog::add('JDate::toMySQL() is deprecated. Use JDate::toSql() instead.', JLog::WARNING, 'deprecated');
		return $this->format('Y-m-d H:i:s', $local, false);
	}

	/**
	 * Gets the date as an SQL datetime string.
	 *
	 * @param   boolean    $local  True to return the date string in the local time zone, false to return it in GMT.
	 * @param   JDatabase  $dbo    The database driver or null to use JFactory::getDbo()
	 *
	 * @return  string     The date string in SQL datetime format.
	 *
	 * @link http://dev.mysql.com/doc/refman/5.0/en/datetime.html
	 * @since   11.4
	 */
	public function toSql($local = false, JDatabase $dbo = null)
	{
		if ($dbo === null)
		{
			$dbo = JFactory::getDbo();
		}
		return $this->format($dbo->getDateFormat(), $local, false);
	}

	/**
	 * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
	 * can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string   The date string in RFC 822 format.
	 *
	 * @link    http://www.ietf.org/rfc/rfc2822.txt
	 * @since   11.1
	 */
	public function toRFC822($local = false)
	{
		return $this->format(DateTime::RFC2822, $local, false);
	}

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @return  integer  The date as a UNIX timestamp.
	 *
	 * @since   11.1
	 */
	public function toUnix()
	{
		return (int) parent::format('U');
	}
}
