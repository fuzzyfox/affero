<?php
	
	/**
	 * View
	 *
	 * This file contains a collection of helper functions to make view creation 
	 * easier and to remove redundancy. It also allows for easier theming of the
	 * system.
	 *
	 * This library is slightly different to the others as it relies on another
	 * class called Controller
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	
	class View extends Utility
	{
		function load($name, $data = null)
		{
			//temp store the view name so that it does not interfere with the view
			$this->_name = $name;
			
			//check if there is data to pass to the view
			if($data !== null)
			{
				//lets start to convert any data in the array into variables
				foreach($data as $variable => $value)
				{
					$this->_vars[$variable] = $value;
				}
			}
			
			//remove all the newly created variables so there is nothing in scope
			unset($name, $variable, $value, $data);
			
			//reload the libraries for the view to use
			$this->input = new Input;
			
			//extract out all the variable for the view into scope if needed
			if(isset($this->_vars))
			{
				extract($this->_vars);
			}
			//check if file exists that contains the view
			if(file_exists(dirname(__FILE__).'/../views/'.$this->_name.'.php'))
			{
				//it does lets load it up already! (with the view helpers)
				include(dirname(__FILE__).'/../views/'.$this->_name.'.php');
			}
			else
			{
				//Oops! It doth notuth existuth
				header('HTTP/1.0 404 Not Found');
				include(dirname(__FILE__).'/../../asset/error/404.html');
			}
		}
		
		/**
		 * head
		 *
		 * This creates and prints out the default html for the <head> tags, for
		 * all views.
		 *
		 * @param string $title The title of the page to display in the browser
		 * tab/title bar
		 */
		function head($title = 'untitled')
		{
			echo "<!-- default head content -->\r\n<meta http-equiv='Content-type' content='text/html; charset=utf-8'>\r\n";
			$this->stylesheet('generic');
			echo "<title>$title</title>";
		}
		
		function navigation()
		{
			
		}
		
		/**
		 * stylesheet
		 *
		 * This prints the html for loading stylesheets within the affero directory
		 */
		function stylesheet($name)
		{
			echo '<link rel="stylesheet" href="'.$this->site_url('asset/css/'.$name.'.css').'" type="text/css">'."\r\n";
		}
		
		function script()
		{
			
		}
		
		function footer()
		{
			
		}
	}
	
?>