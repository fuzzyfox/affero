<?php
	
	//get the glue library
	require_once(dirname(__FILE__).'/app/libraries/glue.lib.php');
	
	//setup url mapping with the help of glue
	$urls = array(
		'/affero/((index\.php/)?)' => 'Frontend',
		'/affero/((index\.php/)?)backend/(?P<contoller>[a-zA-Z0-9_]*)(/?)(?P<method>[a-zA-Z0-9_]*)(/?)(.*)' => 'Backend'
	);
	
	/**
	 * Affero
	 *
	 * This is the main allication class. It is responsible for loading all the
	 * libraries, setting configuration variables, etc...
	 */
	class Affero
	{
		//contains all configuration details for affero
		protected $config;
		
		/**
		 * __construct
		 *
		 * create the configuration object, creates aliases for libraries,
		 */
		function __construct()
		{
			//get affero configuration
			$this->get_config();
			//get libraries
			$this->get_libraries();
		}
		
		/**
		 * get_libraries
		 *
		 * this function grabs all the libraries need/requested from the config
		 * file
		 *
		 * @access private
		 */
		private function get_libraries()
		{
			//load all libraries selected in the configuration
			foreach($this->config->libraries as $library)
			{
				//check that the library exists
				if(file_exists(dirname(__FILE__)."/app/libraries/$library.lib.php"))
				{
					//library file exists lets load it
					include(dirname(__FILE__)."/app/libraries/$library.lib.php");
					//now lets attempt to give it an alias, does the class exist?
					$library = ucwords($library);
					if(class_exists($library))
					{
						$this->$library = new $library();
					}
					else
					{
						//error handling here
					}
				}
				else
				{
					//error handling here
				}
			}
		}
		
		/**
		 * get_config
		 *
		 * this function does all the leg work on loading the user set config for
		 * affero
		 *
		 * @access private
		 */
		private function get_config()
		{
			//check to see if configuration file is set
			if(file_exists(dirname(__FILE__).'/app/config.php'))
			{
				//it is lets create that object
				include(dirname(__FILE__).'/app/config.php');
				//set the information for affero like for like from the config file
				$this->config = $config;
			}
			//file not set check for sample config file
			elseif(file_exists(dirname(__FILE__)."/app/$class.sample.lib.php"))
			{
				//hmmm.... affero not configured yet, lets load the install script
				include(dirname(__FILE__).'/install/index.php');
			}
			//neither config nor sample config exists. Kill application with message
			else
			{
				die('Affero could not find a configuration file and is not able run installation');
			}
		}
		
		private function __autoload($class)
		{
			$class = strtolower($class);
			if(file_exists(dirname(__FILE__)."/app/$class.lib.php"))
			{
				include(dirname(__FILE__)."/app/$class.lib.php");
			}
			else
			{
				//some error handling here
			}
		}
	}
	
	/**
	 * Frontend
	 *
	 * this is the main affero class for the frontend of the application
	 */
	class Frontend extends Affero
	{
		function GET($args)
		{
			//front end processing
		}
	}
	
	/**
	 * Backend
	 *
	 * this is the main affero class for the backend of the application
	 */
	class Backend extends Affero
	{
		function GET($args)
		{
			//ensure we have a controller and method set (defaults if not)
			$controller = ($args['controller'] != '')?$args['controller']:'default';
			$method = ($args['method'] != '')?$args['method']:'index';
			//attempt to load the provided controller
			if(file_exists(dirname(__FILE__).'/app/controllers/backend/'.$args['controller']))
			{
				//include the contoller file
				include(dirname(__FILE__).'/app/controllers/backend/'.$args['controller']);
				//check the class exists
				if(class_exists($args['controller']))
				{
					//create the controller object
					$controller = ucwords($controller);
					$controller = new $controller;
					//check the method exists
					if(method_exists($controller, $method))
					{
						//call the controller and its method and pass it the arguments recieved
						$controller->$method($args);
					}
					else
					{
						header('HTTP/1.0 404 Not Found');
						include(dirname(__FILE__).'/asset/error/404.html');
					}
				}
				else
				{
					header('HTTP/1.0 404 Not Found');
					include(dirname(__FILE__).'/asset/error/404.html');
				}
			}
			else
			{
				header('HTTP/1.0 404 Not Found');
				include(dirname(__FILE__).'/asset/error/404.html');
			}
			print_r($args);
		}
	}
	
	//make the mapping work!
	glue::stick($urls);
?>