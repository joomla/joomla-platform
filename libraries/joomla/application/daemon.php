<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');

/**
 * Class to turn JCli applications into daemons.  It requires CLI and PCNTL support built into PHP.
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @see         http://www.php.net/manual/en/book.pcntl.php
 * @see         http://php.net/manual/en/features.commandline.php
 * @since       11.1
 */
class JApplicationDaemon extends JApplicationCli
{
	/**
	 * @var    array  The available POSIX signals to be caught by default.
	 * @see    http://php.net/manual/pcntl.constants.php
	 * @since  11.1
	 */
	protected static $signals = array(
		'SIGHUP',
		'SIGINT',
		'SIGQUIT',
		'SIGILL',
		'SIGTRAP',
		'SIGABRT',
		'SIGIOT',
		'SIGBUS',
		'SIGFPE',
		'SIGUSR1',
		'SIGSEGV',
		'SIGUSR2',
		'SIGPIPE',
		'SIGALRM',
		'SIGTERM',
		'SIGSTKFLT',
		'SIGCLD',
		'SIGCHLD',
		'SIGCONT',
		'SIGTSTP',
		'SIGTTIN',
		'SIGTTOU',
		'SIGURG',
		'SIGXCPU',
		'SIGXFSZ',
		'SIGVTALRM',
		'SIGPROF',
		'SIGWINCH',
		'SIGPOLL',
		'SIGIO',
		'SIGPWR',
		'SIGSYS',
		'SIGBABY',
		'SIG_BLOCK',
		'SIG_UNBLOCK',
		'SIG_SETMASK'
	);

	/**
	 * @var    boolean  True if the daemon is in the process of exiting.
	 * @since  11.1
	 */
	protected $exiting = false;

	/**
	 * @var    integer  The parent process id.
	 * @since  12.1
	 */
	protected $parentId = 0;

	/**
	 * @var    integer  The process id of the daemon.
	 * @since  11.1
	 */
	protected $processId = 0;

	/**
	 * @var    boolean  True if the daemon is currently running.
	 * @since  11.1
	 */
	protected $running = false;

	/**
	 * Class constructor.
	 *
	 * @param   mixed  $input       An optional argument to provide dependency injection for the application's
	 *                              input object.  If the argument is a JInputCli object that object will become
	 *                              the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config      An optional argument to provide dependency injection for the application's
	 *                              config object.  If the argument is a JRegistry object that object will become
	 *                              the application's config object, otherwise a default config object is created.
	 * @param   mixed  $dispatcher  An optional argument to provide dependency injection for the application's
	 *                              event dispatcher.  If the argument is a JDispatcher object that object will become
	 *                              the application's event dispatcher, if it is null then the default event dispatcher
	 *                              will be created based on the application's loadDispatcher() method.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function __construct(JInputCli $input = null, JRegistry $config = null, JDispatcher $dispatcher = null)
	{
		// Verify that the process control extension for PHP is available.
		// @codeCoverageIgnoreStart
		if (!defined('SIGHUP'))
		{
			JLog::add('The PCNTL extension for PHP is not available.', JLog::ERROR);
			throw new RuntimeException('The PCNTL extension for PHP is not available.');
		}

		// Verify that POSIX support for PHP is available.
		if (!function_exists('posix_getpid'))
		{
			JLog::add('The POSIX extension for PHP is not available.', JLog::ERROR);
			throw new RuntimeException('The POSIX extension for PHP is not available.');
		}
		// @codeCoverageIgnoreEnd

		// Call the parent constructor.
		parent::__construct($input, $config, $dispatcher);

		// Set some system limits.
		@set_time_limit($this->config->get('max_execution_time', 0));
		if ($this->config->get('max_memory_limit') !== null)
		{
			ini_set('memory_limit', $this->config->get('max_memory_limit', '256M'));
		}

		// Flush content immediately.
		ob_implicit_flush();
	}

	/**
	 * Method to handle POSIX signals.
	 *
	 * @param   integer  $signal  The received POSIX signal.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @see     pcntl_signal()
	 * @throws  RuntimeException
	 */
	public static function signal($signal)
	{
		// Log all signals sent to the daemon.
		JLog::add('Received signal: ' . $signal, JLog::DEBUG);

		// Let's make sure we have an application instance.
		if (!is_subclass_of(static::$instance, 'JApplicationDaemon'))
		{
			JLog::add('Cannot find the application instance.', JLog::EMERGENCY);
			throw new RuntimeException('Cannot find the application instance.');
		}

		// Fire the onReceiveSignal event.
		static::$instance->triggerEvent('onReceiveSignal', array($signal));

		switch ($signal)
		{
			case SIGINT:
			case SIGTERM:
				// Handle shutdown tasks
				if (static::$instance->running && static::$instance->isActive())
				{
					static::$instance->shutdown();
				}
				else
				{
					static::$instance->close();
				}
				break;
			case SIGHUP:
				// Handle restart tasks
				if (static::$instance->running && static::$instance->isActive())
				{
					static::$instance->shutdown(true);
				}
				else
				{
					static::$instance->close();
				}
				break;
			case SIGCHLD:
				// A child process has died
				while (static::$instance->pcntlWait($signal, WNOHANG or WUNTRACED) > 0)
				{
					usleep(1000);
				}
				break;
			case SIGCLD:
				while (static::$instance->pcntlWait($signal, WNOHANG) > 0)
				{
					$signal = static::$instance->pcntlChildExitStatus($signal);
				}
				break;
			default:
				break;
		}
	}

	/**
	 * Check to see if the daemon is active.  This does not assume that $this daemon is active, but
	 * only if an instance of the application is active as a daemon.
	 *
	 * @return  boolean  True if daemon is active.
	 *
	 * @since   11.1
	 */
	public function isActive()
	{
		// Get the process id file location for the application.
		$pidFile = $this->config->get('application_pid_file');

		// If the process id file doesn't exist then the daemon is obviously not running.
		if (!is_file($pidFile))
		{
			return false;
		}

		// Read the contents of the process id file as an integer.
		$fp = fopen($pidFile, 'r');
		$pid = fread($fp, filesize($pidFile));
		$pid = intval($pid);
		fclose($fp);

		// Check to make sure that the process id exists as a positive integer.
		if (!$pid)
		{
			return false;
		}

		// Check to make sure the process is active by pinging it and ensure it responds.
		if (!posix_kill($pid, 0))
		{
			// No response so remove the process id file and log the situation.
			@ unlink($pidFile);
			JLog::add('The process found based on PID file was unresponsive.', JLog::WARNING);

			return false;
		}

		return true;
	}

	/**
	 * Load an object or array into the application configuration object.
	 *
	 * @param   mixed  $data  Either an array or object to be loaded into the configuration object.
	 *
	 * @return  JCli  Instance of $this to allow chaining.
	 *
	 * @since   11.1
	 */
	public function loadConfiguration($data)
	{
		// Execute the parent load method.
		parent::loadConfiguration($data);

		/*
		 * Setup some application metadata options.  This is useful if we ever want to write out startup scripts
		 * or just have some sort of information available to share about things.
		 */

		// The application author name.  This string is used in generating startup scripts and has
		// a maximum of 50 characters.
		$tmp = (string) $this->config->get('author_name', 'Joomla Platform');
		$this->config->set('author_name', (strlen($tmp) > 50) ? substr($tmp, 0, 50) : $tmp);

		// The application author email.  This string is used in generating startup scripts.
		$tmp = (string) $this->config->get('author_email', 'admin@joomla.org');
		$this->config->set('author_email', filter_var($tmp, FILTER_VALIDATE_EMAIL));

		// The application name.  This string is used in generating startup scripts.
		$tmp = (string) $this->config->get('application_name', 'JApplicationDaemon');
		$this->config->set('application_name', (string) preg_replace('/[^A-Z0-9_-]/i', '', $tmp));

		// The application description.  This string is used in generating startup scripts.
		$tmp = (string) $this->config->get('application_description', 'A generic Joomla Platform application.');
		$this->config->set('application_description', filter_var($tmp, FILTER_SANITIZE_STRING));

		/*
		 * Setup the application path options.  This defines the default executable name, executable directory,
		 * and also the path to the daemon process id file.
		 */

		// The application executable daemon.  This string is used in generating startup scripts.
		$tmp = (string) $this->config->get('application_executable', basename($this->input->executable));
		$this->config->set('application_executable', $tmp);

		// The home directory of the daemon.
		$tmp = (string) $this->config->get('application_directory', dirname($this->input->executable));
		$this->config->set('application_directory', $tmp);

		// The pid file location.  This defaults to a path inside the /tmp directory.
		$name = $this->config->get('application_name');
		$tmp = (string) $this->config->get('application_pid_file', strtolower('/tmp/' . $name . '/' . $name . '.pid'));
		$this->config->set('application_pid_file', $tmp);

		/*
		 * Setup the application identity options.  It is important to remember if the default of 0 is set for
		 * either UID or GID then changing that setting will not be attempted as there is no real way to "change"
		 * the identity of a process from some user to root.
		 */

		// The user id under which to run the daemon.
		$tmp = (int) $this->config->get('application_uid', 0);
		$options = array('options' => array('min_range' => 0, 'max_range' => 65000));
		$this->config->set('application_uid', filter_var($tmp, FILTER_VALIDATE_INT, $options));

		// The group id under which to run the daemon.
		$tmp = (int) $this->config->get('application_gid', 0);
		$options = array('options' => array('min_range' => 0, 'max_range' => 65000));
		$this->config->set('application_gid', filter_var($tmp, FILTER_VALIDATE_INT, $options));

		// Option to kill the daemon if it cannot switch to the chosen identity.
		$tmp = (bool) $this->config->get('application_require_identity', 1);
		$this->config->set('application_require_identity', $tmp);

		/*
		 * Setup the application runtime options.  By default our execution time limit is infinite obviously
		 * because a daemon should be constantly running unless told otherwise.  The default limit for memory
		 * usage is 128M, which admittedly is a little high, but remember it is a "limit" and PHP's memory
		 * management leaves a bit to be desired :-)
		 */

		// The maximum execution time of the application in seconds.  Zero is infinite.
		$tmp = $this->config->get('max_execution_time');
		if ($tmp !== null)
		{
			$this->config->set('max_execution_time', (int) $tmp);
		}

		// The maximum amount of memory the application can use.
		$tmp = $this->config->get('max_memory_limit', '256M');
		if ($tmp !== null)
		{
			$this->config->set('max_memory_limit', (string) $tmp);
		}

		return $this;
	}

	/**
	 * Execute the daemon.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function execute()
	{
		// Trigger the onBeforeExecute event.
		$this->triggerEvent('onBeforeExecute');

		// Enable basic garbage collection.  Only available in PHP 5.3+
		if (function_exists('gc_enable'))
		{
			gc_enable();
		}

		JLog::add('Starting ' . $this->name, JLog::INFO);

		// Set off the process for becoming a daemon.
		if ($this->daemonize())
		{
			// Declare ticks to start signal monitoring. When you declare ticks, PCNTL will monitor
			// incoming signals after each tick and call the relevant signal handler automatically.
			declare (ticks = 1);

			// Start the main execution loop.
			while (true)
			{
				// Perform basic garbage collection.
				$this->gc();

				// Don't completely overload the CPU.
				usleep(1000);

				// Execute the main application logic.
				$this->doExecute();
			}
		}
		// We were not able to daemonize the application so log the failure and die gracefully.
		else
		{
			JLog::add('Starting ' . $this->name . ' failed', JLog::INFO);
		}

		// Trigger the onAfterExecute event.
		$this->triggerEvent('onAfterExecute');
	}

	/**
	 * Restart daemon process.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function restart()
	{
		JLog::add('Stopping ' . $this->name, JLog::INFO);
		$this->shutdown(true);
	}

	/**
	 * Stop daemon process.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function stop()
	{
		JLog::add('Stopping ' . $this->name, JLog::INFO);
		$this->shutdown();
	}

	/**
	 * Method to change the identity of the daemon process and resources.
	 *
	 * @return  boolean  True if identity successfully changed
	 *
	 * @since   11.1
	 * @see     posix_setuid()
	 */
	protected function changeIdentity()
	{
		// Get the group and user ids to set for the daemon.
		$uid = (int) $this->config->get('application_uid', 0);
		$gid = (int) $this->config->get('application_gid', 0);

		// Get the application process id file path.
		$file = $this->config->get('application_pid_file');

		// Change the user id for the process id file if necessary.
		if ($uid && (fileowner($file) != $uid) && (!@ chown($file, $uid)))
		{
			JLog::add('Unable to change user ownership of the process id file.', JLog::ERROR);
			return false;
		}

		// Change the group id for the process id file if necessary.
		if ($gid && (filegroup($file) != $gid) && (!@ chgrp($file, $gid)))
		{
			JLog::add('Unable to change group ownership of the process id file.', JLog::ERROR);
			return false;
		}

		// Set the correct home directory for the process.
		if ($uid && ($info = posix_getpwuid($uid)) && is_dir($info['dir']))
		{
			system('export HOME="' . $info['dir'] . '"');
		}

		// Change the user id for the process necessary.
		if ($uid && (posix_getuid($file) != $uid) && (!@ posix_setuid($uid)))
		{
			JLog::add('Unable to change user ownership of the proccess.', JLog::ERROR);
			return false;
		}

		// Change the group id for the process necessary.
		if ($gid && (posix_getgid($file) != $gid) && (!@ posix_setgid($gid)))
		{
			JLog::add('Unable to change group ownership of the proccess.', JLog::ERROR);
			return false;
		}

		// Get the user and group information based on uid and gid.
		$user = posix_getpwuid($uid);
		$group = posix_getgrgid($gid);

		JLog::add('Changed daemon identity to ' . $user['name'] . ':' . $group['name'], JLog::INFO);

		return true;
	}

	/**
	 * Method to put the application into the background.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	protected function daemonize()
	{
		// Is there already an active daemon running?
		if ($this->isActive())
		{
			JLog::add($this->name . ' daemon is still running. Exiting the application.', JLog::EMERGENCY);
			return false;
		}

		// Reset Process Information
		$this->safeMode = !!@ ini_get('safe_mode');
		$this->processId = 0;
		$this->running = false;

		// Detach process!
		try
		{
			// Check if we should run in the foreground.
			if (!$this->input->get('f'))
			{
				// Detach from the terminal.
				$this->detach();
			}
			else
			{
				// Setup running values.
				$this->exiting = false;
				$this->running = true;

				// Set the process id.
				$this->processId = (int) posix_getpid();
				$this->parentId = $this->processId;
			}
		}
		catch (RuntimeException $e)
		{
			JLog::add('Unable to fork.', JLog::EMERGENCY);
			return false;
		}

		// Verify the process id is valid.
		if ($this->processId < 1)
		{
			JLog::add('The process id is invalid; the fork failed.', JLog::EMERGENCY);
			return false;
		}

		// Clear the umask.
		@ umask(0);

		// Write out the process id file for concurrency management.
		if (!$this->writeProcessIdFile())
		{
			JLog::add('Unable to write the pid file at: ' . $this->config->get('application_pid_file'), JLog::EMERGENCY);
			return false;
		}

		// Attempt to change the identity of user running the process.
		if (!$this->changeIdentity())
		{

			// If the identity change was required then we need to return false.
			if ($this->config->get('application_require_identity'))
			{
				JLog::add('Unable to change process owner.', JLog::CRITICAL);
				return false;
			}
			else
			{
				JLog::add('Unable to change process owner.', JLog::WARNING);
			}
		}

		// Setup the signal handlers for the daemon.
		if (!$this->setupSignalHandlers())
		{
			return false;
		}

		// Change the current working directory to the application working directory.
		@ chdir($this->config->get('application_directory'));

		return true;
	}

	/**
	 * This is truly where the magic happens.  This is where we fork the process and kill the parent
	 * process, which is essentially what turns the application into a daemon.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function detach()
	{
		JLog::add('Detaching the ' . $this->name . ' daemon.', JLog::DEBUG);

		// Attempt to fork the process.
		$pid = $this->fork();

		// If the pid is positive then we successfully forked, and can close this application.
		if ($pid)
		{
			// Add the log entry for debugging purposes and exit gracefully.
			JLog::add('Ending ' . $this->name . ' parent process', JLog::DEBUG);
			$this->close();
		}
		// We are in the forked child process.
		else
		{
			// Setup some protected values.
			$this->exiting = false;
			$this->running = true;

			// Set the parent to self.
			$this->parentId = $this->processId;
		}
	}

	/**
	 * Method to fork the process.
	 *
	 * @return  integer  The child process id to the parent process, zero to the child process.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	protected function fork()
	{
		// Attempt to fork the process.
		$pid = $this->pcntlFork();

		// If the fork failed, throw an exception.
		if ($pid === -1)
		{
			throw new RuntimeException('The process could not be forked.');
		}
		// Update the process id for the child.
		elseif ($pid === 0)
		{
			$this->processId = (int) posix_getpid();
		}
		// Log the fork in the parent.
		else
		{
			// Log the fork.
			JLog::add('Process forked ' . $pid, JLog::DEBUG);
		}

		// Trigger the onFork event.
		$this->postFork();

		return $pid;
	}

	/**
	 * Method to perform basic garbage collection and memory management in the sense of clearing the
	 * stat cache.  We will probably call this method pretty regularly in our main loop.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	protected function gc()
	{
		// Perform generic garbage collection.  Only available in PHP 5.3+
		if (function_exists('gc_collect_cycles'))
		{
			gc_collect_cycles();
		}

		// Clear the stat cache so it doesn't blow up memory.
		clearstatcache();
	}

	/**
	 * Method to attach the JApplicationDaemon signal handler to the known signals.  Applications
	 * can override these handlers by using the pcntl_signal() function and attaching a different
	 * callback method.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 * @see     pcntl_signal()
	 */
	protected function setupSignalHandlers()
	{
		// We add the error suppression for the loop because on some platforms some constants are not defined.
		foreach (self::$signals as $signal)
		{
			// Ignore signals that are not defined.
			if (!defined($signal) || !is_int(constant($signal)) || (constant($signal) === 0))
			{
				// Define the signal to avoid notices.
				JLog::add('Signal "' . $signal . '" not defined. Defining it as null.', JLog::DEBUG);
				define($signal, null);

				// Don't listen for signal.
				continue;
			}

			// Attach the signal handler for the signal.
			if (!$this->pcntlSignal(constant($signal), array('JApplicationDaemon', 'signal')))
			{
				JLog::add(sprintf('Unable to reroute signal handler: %s', $signal), JLog::EMERGENCY);
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to shut down the daemon and optionally restart it.
	 *
	 * @param   boolean  $restart  True to restart the daemon on exit.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function shutdown($restart = false)
	{
		// If we are already exiting, chill.
		if ($this->exiting)
		{
			return;
		}
		// If not, now we are.
		else
		{
			$this->exiting = true;
		}

		// If we aren't already daemonized then just kill the application.
		if (!$this->running && !$this->isActive())
		{
			JLog::add('Process was not daemonized yet, just halting current process', JLog::INFO);
			$this->close();
		}

		// Only read the pid for the parent file.
		if ($this->parentId == $this->processId)
		{
			// Read the contents of the process id file as an integer.
			$fp = fopen($this->config->get('application_pid_file'), 'r');
			$pid = fread($fp, filesize($this->config->get('application_pid_file')));
			$pid = intval($pid);
			fclose($fp);

			// Remove the process id file.
			@ unlink($this->config->get('application_pid_file'));

			// If we are supposed to restart the daemon we need to execute the same command.
			if ($restart)
			{
				$this->close(exec(implode(' ', $GLOBALS['argv']) . ' > /dev/null &'));
			}
			// If we are not supposed to restart the daemon let's just kill -9.
			else
			{
				passthru('kill -9 ' . $pid);
				$this->close();
			}
		}
	}

	/**
	 * Method to write the process id file out to disk.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	protected function writeProcessIdFile()
	{
		// Verify the process id is valid.
		if ($this->processId < 1)
		{
			JLog::add('The process id is invalid.', JLog::EMERGENCY);
			return false;
		}

		// Get the application process id file path.
		$file = $this->config->get('application_pid_file');
		if (empty($file))
		{
			JLog::add('The process id file path is empty.', JLog::ERROR);
			return false;
		}

		// Make sure that the folder where we are writing the process id file exists.
		$folder = dirname($file);
		if (!is_dir($folder) && !JFolder::create($folder))
		{
			JLog::add('Unable to create directory: ' . $folder, JLog::ERROR);
			return false;
		}

		// Write the process id file out to disk.
		if (!file_put_contents($file, $this->processId))
		{
			JLog::add('Unable to write proccess id file: ' . $file, JLog::ERROR);
			return false;
		}

		// Make sure the permissions for the proccess id file are accurate.
		if (!chmod($file, 0644))
		{
			JLog::add('Unable to adjust permissions for the proccess id file: ' . $file, JLog::ERROR);
			return false;
		}

		return true;
	}

	/**
	 * Method to handle post-fork triggering of the onFork event.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function postFork()
	{
		// Trigger the onFork event.
		$this->triggerEvent('onFork');
	}

	/**
	 * Method to return the exit code of a terminated child process.
	 *
	 * @param   integer  $status  The status parameter is the status parameter supplied to a successful call to pcntl_waitpid().
	 *
	 * @return  integer  The child process exit code.
	 *
	 * @codeCoverageIgnore
	 * @see     pcntl_wexitstatus()
	 * @since   11.3
	 */
	protected function pcntlChildExitStatus($status)
	{
		return pcntl_wexitstatus($status);
	}

	/**
	 * Method to return the exit code of a terminated child process.
	 *
	 * @return  integer  On success, the PID of the child process is returned in the parent's thread
	 *                   of execution, and a 0 is returned in the child's thread of execution. On
	 *                   failure, a -1 will be returned in the parent's context, no child process
	 *                   will be created, and a PHP error is raised.
	 *
	 * @codeCoverageIgnore
	 * @see     pcntl_fork()
	 * @since   11.3
	 */
	protected function pcntlFork()
	{
		return pcntl_fork();
	}

	/**
	 * Method to install a signal handler.
	 *
	 * @param   integer   $signal   The signal number.
	 * @param   callback  $handler  The signal handler which may be the name of a user created function,
	 *                              or method, or either of the two global constants SIG_IGN or SIG_DFL.
	 * @param   boolean   $restart  Specifies whether system call restarting should be used when this
	 *                              signal arrives.
	 *
	 * @return  boolean  True on success.
	 *
	 * @codeCoverageIgnore
	 * @see     pcntl_signal()
	 * @since   11.3
	 */
	protected function pcntlSignal($signal , $handler, $restart = true)
	{
		return pcntl_signal($signal, $handler, $restart);
	}

	/**
	 * Method to wait on or return the status of a forked child.
	 *
	 * @param   integer  &$status  Status information.
	 * @param   integer  $options  If wait3 is available on your system (mostly BSD-style systems),
	 *                             you can provide the optional options parameter.
	 *
	 * @return  integer  The process ID of the child which exited, -1 on error or zero if WNOHANG
	 *                   was provided as an option (on wait3-available systems) and no child was available.
	 *
	 * @codeCoverageIgnore
	 * @see     pcntl_wait()
	 * @since   11.3
	 */
	protected function pcntlWait(&$status, $options = 0)
	{
		return pcntl_wait($status, $options);
	}
}
