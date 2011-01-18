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
	 * This is the array of settings that can be configured. This is the only
	 * part of this file that should be changed by the user. It can also be
	 * set by the installation tool.
	 */
	$config = array(
		//site settings
		'site' => array(
			//set the name of the site (will be used in page headers)
			'name' => 'defero',
			//set the base url of the site (excluding 'http://' and trailing slash)
			'url' => 'labs.mozhunt.com/affero'
		),
		//database settings
		'db' => array(
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
	
	//==== DO NOT EDIT BELLOW THIS LINE ========================================
	
	/**
	 * The following code runs through the array above and defines all the user 
	 * configurable settings as constants for use in the rest of the application.
	 */
	foreach($config as $key => $settings)
	{
		foreach($settings as $setting => $value)
		{
			define(strtoupper($key.'_'.$setting), $value);
		}
	}
	
	//set that we are in a development environment if the domain provided does not match the current domain.
	define('DEVELOPMENT_ENVIRONMENT', (parse_url($utilities->current_url(), PHP_URL_HOST) == parse_url($config['site']['url'], PHP_URL_HOST)));
	
?>