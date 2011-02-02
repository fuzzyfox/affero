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
			'url' => 'labs.mozhunt.com/affero'
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
		),
		//libraries for affero to automatically load format: ('lib1', 'lib2', 'lib3')
		'libraries' => array()
	);
	
	//==== DO NOT EDIT BELLOW THIS LINE ========================================
	
	/**
	 * set required libraries for affero to load
	 */
	array_push($config->libraries, 'database', 'input', 'utility');
	
?>