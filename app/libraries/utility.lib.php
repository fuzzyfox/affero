<?php
	
	/**
	 * Utility
	 *
	 * This file contains a collection of utility methods that are not related
	 * to a particular area of the application.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright MPL 1.1/LGPL 2.1/GPL 2.0 William Duyck
	 */
	
	/**
	 * Utility
	 *
	 * Contains a collection of utility methods that are not specific to any
	 * area of the application.
	 */
	class Utility
	{
		
		/**
		* current_url
		*
		* This function get the url of the current page the user is viewing, and
		* sticks all the bits together as PHP does not do this for you.
		*
		* @return string the url of the current page.
		*/
		function current_url()
		{
			//start constructing the url string
			$url = 'http';
			//check if the user is viewing the page with a secured connection
			if(isset($_SERVER['HTTPS']))
			{
				$url .= 's';
			}
			//continue construction and append the domain name
			$url .= '://'.$_SERVER['HTTP_HOST'];
			//check if the user is not on standard port 80 and correct the url as need
			if($_SERVER['SERVER_PORT'] != 80)
			{
				$url .= $_SERVER['SERVER_PORT'];
			}
			//finally add anything that is not part of the domain (the path)
			$url .= $_SERVER['REQUEST_URI'];
			//time to return the information
			return $url;
		}
		
		/**
		 * site_url
		 *
		 * This function generates a url that points to a page on the same site
		 * based on the domain provided in the configuration file.
		 *
		 * @param string $localtion the page/file on site to point to
		 * @return string the url to that file
		 */
		function site_url($localtion)
		{
			return 'http'.((isset($_SERVER['HTTPS']))?'s':'').'://'.SITE_URL.'/'.$localtion;
		}
		
		/**
		 * valid_email
		 *
		 * This function returns true or false depending on whether the input it
		 * receives is a valid url
		 *
		 * @param string $email The email to check
		 * @return bool True when email is valid
		 * @todo valid_email
		 * - Grab regex and return bool based on preg_match of said regex
		 */
		function valid_email($email)
		{
			//return 
		}
		
		/**
		 * hash_string
		 *
		 * This function take string and optional salt to create a sha512 hash
		 * that is then returned.
		 *
		 * If a salt is not provied will just hash {string} else will hash
		 * {string}_{salt}
		 *
		 * @param string $string the string to be hashed
		 * @param string $salt the salt to use when hashing the $string
		 * @return string a sha512 string created using $string and $salt
		 */
		function hash_string($string, $salt = null)
		{
			return hash('sha512', $string.(($salt !== null)?'_'.$salt:''));
		}
		
		/**
		 * view
		 *
		 * This function gets and loads a view file and passes it data it receives
		 * via an associative array, and makes it available to the view.
		 *
		 * @param string $view the name of the view file to load
		 * @param assoc_array $data the data to pass to the view file
		 * @return void
		 */
		function view($view, $data = null)
		{
			//temp store the view name so that it does not interfere with the view
			$this->view_name = $view;
			
			//check if there is data to pass to the view
			if($data !== null)
			{
				//lets start to convert any data in the array into variables
				foreach($data as $variable => $value)
				{
					$this->view_vars[$variable] = $value;
				}
			}
			
			//remove all the newly created variables so there is nothing in scope
			unset($view, $variable, $value, $data);
			
			//extract out all the variable for the view into scope if needed
			if(isset($this->view_vars))
			{
				extract($this->view_vars);
			}
			//reload the input libray for the view to use if needed
			$this->input = new Input;
			//check if file exists that contains the view
			if(file_exists(dirname(__FILE__).'/../views/'.$this->view_name.'.php'))
			{
				//it does lets load it up already!
				include(dirname(__FILE__).'/../views/'.$this->view_name.'.php');
			}
			else
			{
				//Oops! It doth notuth existuth
				header('HTTP/1.0 404 Not Found');
				include(dirname(__FILE__).'/../../asset/error/404.html');
			}
		}
		
	}
	
?>