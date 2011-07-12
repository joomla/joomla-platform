<?php
class JTestConfig
{
	public $dbtype		= 'mysql';
	public $host		= '127.0.0.1';
	public $user		= 'joomla';
	public $password	= 'joomla';
	public $db			= 'joomla_ut';
	public $dbprefix	= 'jos_';
	public $ftp_host	= '127.0.0.1';
	public $ftp_port	= '21';
	public $ftp_user	= '';
	public $ftp_pass	= '';
	public $ftp_root	= '';
	public $ftp_enable	= 0;
	public $tmp_path	= 'B:/TEMP';
	public $log_path	= 'B:/TEMP';
	public $mailer		= 'mail';
	public $mailfrom	= 'serradeil@webmechanic.biz';
	public $fromname	= '';
	public $sendmail	= '/usr/sbin/sendmail';
	public $smtpauth	= '0';
	public $smtpsecure = 'none';
	public $smtpport	= '25';
	public $smtpuser	= '';
	public $smtppass	= '';
	public $smtphost	= 'localhost';
	public $debug		= 0;
	public $caching		= '0';
	public $cachetime	= '900';
	public $language	= 'en-GB';
	public $secret		= null;
	public $editor		= 'none';
	public $offset		= 0;
	public $lifetime	= 15;
}
