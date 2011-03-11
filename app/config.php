<?php
	
	/**
	 * Affero Configuration File
	 *
	 * This file is used to set all the configurable settings of defero.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @copyright 2011 Willaim Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * config
	 *
	 * This is the object of settings that can be configured. This is the only
	 * part of this file that should be changed by the user. It can also be
	 * set by the installation tool.
	 */
	$config = (object)array(
		//site settings
		'site' => (object)array(
			//set the name of the site (will be used in page headers)
			'name' => 'affero',
			//set the base url of the site (excluding 'http://' and trailing slash)
			'url' => $_SERVER['HTTP_HOST'].'/affero',
			//set the footer information (must be in html)
			'footer' => '<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">cba</a> 2010 - <a href="http://fuzzyfox.mozhunt.com/">William Duyck</a></p>'
		),
		//database settings
		'db' => (object)array(
			//set the database host (normally localhost)
			'host' => 'localhost',
			//set the database name
			'name' => 'ccw_dev',
			//set the database username
			'user' => 'root',
			//set the database password
			'pass' => ''
		)
	);
	$config->invite = (object)array(
		'replyto' => 'noreply@mozhunt.com',
		'subject' => 'You\'ve been invited to use Affero',
		'template' => '{sender} has invited you to join Affero. To create your account visit:

http://'.$config->site->url.'/user/create?token={token}

Look foward to seeing you soon!'
	);
	
?>