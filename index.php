<?php
	
	/**
	 * Affero
	 *
	 * This file contains all the url routing and class loading for the entire
	 * application. It is essentially the core of the application.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage core
	 * @copyright MPL 1.1/LGPL 2.1/GPL 2.0 William Duyck
	 */
	
	/**
	 * __autoload
	 *
	 * this function will attempt to automatically load files/classes as and when
	 * they are needed rather than consume lots of memory and loading all classes
	 */
	function __autoload($class)
	{
		$class = strtolower($class);
		if(file_exists(dirname(__FILE__)."/app/libraries/$class.lib.php"))
		{
			include(dirname(__FILE__)."/app/libraries/$class.lib.php");
		}
		elseif(file_exists(dirname(__FILE__)."/app/controllers/frontend/$class.php"))
		{
			include(dirname(__FILE__)."/app/controllers/frontend/$class.php");
		}
		elseif(file_exists(dirname(__FILE__)."/app/controllers/backend/$class.php"))
		{
			include(dirname(__FILE__)."/app/controllers/backend/$class.php");
		}
		else
		{
			header('HTTP/1.0 404 Not Found');
			include(dirname(__FILE__).'/asset/error/404.html');
		}
	}
	
	//setup url mapping
	$urls = array(
		'/affero/((index\.php)?(/?))' => 'Frontend',
		'/affero/((index\.php/)?)backend/(?P<controller>[a-zA-Z0-9_]*)(/?)(?P<method>[a-zA-Z0-9_]*)(/?)(.*)' => 'Backend'
	);
	
	/**
	 * Backend
	 *
	 * This class controls all tasks relating to modifying areas of affero. It 
	 * does some minor url routing tasks, and provides all the key libraries
	 * to the controllers to routes to.
	 */
	class Backend
	{
		//configuration options
		protected $config;
		//database class
		protected $database;
		//utility class
		protected $utility;
		//input class
		protected $input;
		
		/**
		 * __construct
		 *
		 * this function sets up the backend requirements such as the configuration,
		 * database connection, etc...
		 */
		function __construct()
		{
			//fetch the configuration file
			include(dirname(__FILE__).'/app/config.php');
			//create class global for config
			$this->config = $config;
			//create a couple of constants for one or two libraries to use from config
			@define('SITE_URL', $config->site->url);
			//initiate the database connection
			$this->database = new Database($config->db->host, $config->db->name, $config->db->user, $config->db->pass);
			//load the utility and input classes
			$this->utility = new Utility();
			$this->input = new Input();
			//setup the session that will be used for auth and CSRF prevention
			session_set_cookie_params(3600, '/', parse_url($this->config->site->url, PHP_URL_HOST), false, true);
			session_name('affero_session');
			session_start();
		}
		
		/**
		 * GET
		 * 
		 * This function routes all HTTP GET calls to the relevant controllers
		 * based on the url provided
		 */
		function GET($args)
		{
			//ensure we have a controller and method set (defaults if not)
			$controller = (isset($args['controller'])&&($args['controller'] != null))?$args['controller']:'dashboard';
			$method = (isset($args['method'])&&($args['method'] != null))?$args['method']:'index';
			
			//load the controller and its relevant method if posible
			if(class_exists($controller))
			{
				//repurpose $controller into the class object
				$controller = new $controller;
				//check if the method exists
				if(method_exists($controller, $method))
				{
					//okay it does, lets call it and pass it the rest of our args
					$controller->$method($args);
				}
				else
				{
					//nope, not in existance, set http header to 404
					header('HTTP/1.0 404 Not Found');
					//load 404 error file
					include(dirname(__FILE__).'/asset/error/404.html');
				}
			}
			else
			{
				//nope, not in existance, set http header to 404
				header('HTTP/1.0 404 Not Found');
				//load 404 error file
				include(dirname(__FILE__).'/asset/error/404.html');
			}
			print_r($_SESSION); //for debugging issues with sessions
		}
		
		/**
		 * POST
		 *
		 * very similar to GET. So similar in fact that it just sends all its
		 * calls striaght to GET for convinience. This will likely be modified
		 * in the future.
		 */
		function POST($args)
		{
			$this->GET($args);
		}
		
	}
	
	//make the mapping work!
	try
	{
		glue::stick($urls);
	}
	//catch bad method calls and turn them into 405 errors
	catch(BadMethodCallException $e)
	{
		//set http header
		header('HTTP/1.0 405 Method Not Allowed');
		//include the error file
		include(dirname(__FILE__).'/asset/error/405.html');
	}
	//catch exceptions from glue and turn them into 404 errors (essensially what they should be)
	catch(Exception $e)
	{
		//set http header
		header('HTTP/1.0 404 Not Found');
		//include the error file
		include(dirname(__FILE__).'/asset/error/404.html');
	}
	
?>