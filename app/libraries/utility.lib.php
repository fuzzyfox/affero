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
		 * __construct
		 *
		 * loads the affero configuration file and assigns config details to
		 * $this->config
		 */
		function __construct()
		{
			//fetch the configuration file
			include(dirname(__FILE__).'/../config.php');
			//create class global for config
			$this->config = $config;
		}
		
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
			return 'http'.((isset($_SERVER['HTTPS']))?'s':'').'://'.$this->config->site->url.'/'.$localtion;
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
			return preg_match("/\b^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$\b/ix", $email);
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
		
	}
	
?>