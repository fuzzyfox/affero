<?php
	
	/**
	 * View
	 *
	 * This file contains a collection of helper functions to make view creation 
	 * easier and to remove redundancy. It also allows for easier theming of the
	 * system.
	 *
	 * This library is slightly different to the others as it relies on the
	 * utility library
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * View
	 *
	 * A collection of helper functions for use in views to make life a little
	 * easier and development quicker
	 */
	class View extends Utility
	{
		/**
		 * load
		 *
		 * This function gets and loads a view file and passes it data it receives
		 * via an associative array, and makes it available to the view.
		 *
		 * @param string $view the name of the view file to load
		 * @param assoc_array $data the data to pass to the view file
		 * @return void
		 */
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
			
			//load the libraries for the view to use
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
			//meta data
			echo "<!-- start default head content -->\r\n<meta http-equiv='Content-type' content='text/html; charset=utf-8'>\r\n";
			//generic stylesheet
			$this->stylesheet('generic');
			$this->script('common');
			//page title
			echo "<title>$title</title>\r\n<!-- end default head content -->\r\n";
			//base url (allows for easier url writting)
			echo "<base href=\"{$this->site_url()}\">";
		}
		
		/**
		 * navigation
		 * 
		 * This function checks if a user is logged in and presents a different
		 * set of navigation options if so.
		 *
		 * It prints the navigation as html
		 */
		function navigation()
		{
			echo '<div id="nav" class="right">
			<ul>
				<li><a href="'.$this->site_url('dashboard').'">dashboard</a></li>';
			if(isset($_SESSION['user']['logged']) && ($_SESSION['user']['logged'] == true))
			{
				echo '<li><a href="'.$this->site_url('manage').'">affero settings</a></li>
				<li><a href="'.$this->site_url('user/settings').'">user settings</a></li>
				<li><a href="'.$this->site_url('user/invite').'">invite user</a></li>
				<li><a href="'.$this->site_url('user/logout').'">logout</a></li>';
			}
			echo '</ul>
			</div>';
		}
		
		/**
		 * stylesheet
		 *
		 * This simple helper prints the html for loading stylesheets within the
		 * affero css directory
		 *
		 * @param string $name the name of the css file (ex. '.css')
		 */
		function stylesheet($name)
		{
			echo '<link rel="stylesheet" href="'.$this->site_url('asset/css/'.$name.'.css').'" type="text/css">'."\r\n";
		}
		
		/**
		 * script
		 *
		 * This simple helper prints the html for loading scripts within the
		 * affero js directory
		 *
		 * @param string $name the name of the js file (ex. '.js')
		 */
		function script($name)
		{
			echo '<script type="text/javascript" src="'.$this->site_url('asset/js/'.$name.'.js').'"></script>'."\r\n";
		}
	}
	
?>