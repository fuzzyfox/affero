<?php
	
	/**
	 * Input Class
	 *
	 * This file contains a collection of tools for sanitizing, and filtering
	 * user supplied data via $_GLOBAL's. It does so by unregistering globals,
	 * checking for xss type inputs, and standardising new lines.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Input
	 *
	 * This class takes all input, and passes it through some sanitization, and
	 * xss filtering before it is used.
	 */
	class Input
	{
		/**
		 * __construct
		 *
		 * This method will do all the bits that should be done as soon as the
		 * class is intiated. It unregisters globals and runs $_GET, $_POST, and
		 * $_COOKIE through the clean_input method from the get go.
		 */
		function __construct()
		{
			//globals to get rid of
			$global = array($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV, ((isset($_SESSION) && is_array($_SESSION))? $_SESSION : array()));
			
			/**
			 * deep_unset
			 * 
			 * A small helper function that recursively unsets a global.
			 *
			 * @param string $key the key for the global to unregister
			 * @param string $value the data in the global to check for arrays
			 */
			function deep_unset($key, $value)
			{
				if(!is_array($key))
				{
					//it would be sorta wrong to remove this globals
					if(!in_array($key, array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES')))
					{
						unset($GLOBALS[$key]);
					}
				}
				elseif(is_array($value))
				{
					deep_unset($value);
				}
			}
			
			//do a deep_unset for each global
			foreach($global as $key => $value)
			{
				deep_unset($key, $value);
			}
			
			//clean $_GET data
			$_GET = $this->clean_input($_GET);
			//clean $_POST data
			$_POST = $this->clean_input($_POST);
			/*
			 $_COOKIE gets some special attention due to some specially treated
			 cookies existing so lets unset those so as not to trip clean_keys()
			*/
			unset($_COOKIE['$Version']);
			unset($_COOKIE['$Path']);
			unset($_COOKIE['$Domain']);
			//okay now lets clean cookies
			$_COOKIE = $this->clean_input($_COOKIE);
		}
		
		/**
		 * clean_input
		 *
		 * removes magic quotes, standarizes new lines, and runs the xss filter
		 * on input
		 *
		 * @access public
		 * @param mixed $input an array/object or string to clean
		 * @return mixed a cleaned version of input that is safe for use
		 */
		public function clean_input($input)
		{
			//oops this is an array, lets clean the keys and values seperately
			if(is_array($input) || is_object($input))
			{
				$return = array();
				foreach($input as $key => $value)
				{
					$return[$this->clean_key($key)] = $this->clean_input($value);
				}
				return $return;
			}
			
			//magic quotes are bad, lets get rid of those slashes
			if(get_magic_quotes_gpc())
			{
				$input = stripslashes($input);
			}
			
			//run through the xss filter
			$input = $this->xss_filter($input);
			
			//standardize new lines
			if(strpos($input, "\r") !== false)
			{
				$input = str_replace(array("\r\n", "\r"), "\n", $input);
			}
			
			return $input;
		}
		
		/**
		 * clean_key
		 *
		 * A simple helper function that kills the application when it detects
		 * a nasty key in an array/object such as those containing
		 * non-alphanumeric text (with a few exceptions)
		 *
		 * @access private
		 * @param string $key the key to check
		 * @return string the key is safe so returned unaltered
		 */
		private function clean_key($key)
		{
			if(!preg_match('/^[a-z0-9:_\/-]+$/i', $key))
			{
				exit('Stop trying to expoit my keys');
			}
			return $key;
		}
		
		/**
		 * get
		 * 
		 * an alias for $_GET that has been filtered for xss
		 *
		 * @access public
		 * @param string $key the key you want from $_GET
		 * @return mixed a clean version of $_GET[$key]
		 */
		public function get($key = '')
		{
			return $this->fetch_global_data($_GET, $key);
		}
		
		/**
		 * post
		 * 
		 * an alias for $_POST that has been filtered for xss
		 *
		 * @access public
		 * @param string $key the key you want from $_POST
		 * @return mixed a clean version of $_POST[$key]
		 */
		public function post($key = '')
		{
			return $this->fetch_global_data($_POST, $key);
		}
		
		/**
		 * cookie
		 * 
		 * an alias for $_COOKIE that has been filtered for xss
		 *
		 * @param string $key the key you want from $_COOKIE
		 * @return mixed a clean version of $_COOKIE[$key]
		 */
		public function cookie($key = '')
		{
			return $this->fetch_global_data($_COOKIE, $key);
		}
		
		/**
		 * server
		 * 
		 * an alias for $_SERVER that has been filtered for xss
		 *
		 * @access public
		 * @param string $key the key you want from $_SERVER
		 * @return mixed a clean version of $_SERVER[$key]
		 */
		public function server($key = '')
		{
			return $this->fetch_global_data($_SERVER, $key);
		}
		
		/**
		 * fetch_global_data
		 *
		 * this simple method grabs the xss filtered data from the global
		 * variable passed to it.
		 *
		 * @access private
		 * @param array &$global the global to retrieve data from
		 * @param string $key the index (key) of the data to retrive
		 * @return mixed returns false on failure and the xss filtered data on success
		 */
		private function fetch_global_data(&$global, $key)
		{
			if(!isset($global[$key]))
			{
				return false;
			}
			
			return $this->xss_filter($global[$key]);
		}
		
		/**
		 * xss_filter
		 *
		 * This is a xss filter that should remove most nasty code entered into
		 * the site via form of url.
		 *
		 * This function is not created by William Duyck. Please observe the legal
		 * rights of the following:
		 *
		 * @author Christian Stocker <christian.stocker@liip.ch>
		 * @copyright Copyright (c) 2001 - 2008 Liip AG
		 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License version 2.0
		 * @link http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
		 *
		 * @access private
		 * @param string $data the data to be filtered
		 * @return string the filtered data
		 */
		private function xss_filter($data)
		{
			$data = str_replace(array("&amp;", "&lt;", "&gt;"), array("&amp;amp;", "&amp;lt;", "&amp;gt;"), $data);
			
			// fix &entitiy\n;
			$data = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;", $data);
			$data = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$data);
			$data = html_entity_decode($string, ENT_COMPAT, "UTF-8");
			
			// remove any attribute starting with "on" or xmlns
			$data = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>#iUu', "$1>", $data);
			
			// remove javascript: and vbscript: protocol
			$data = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $data);
			$data = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $data);
			$data = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $data);
			$data = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $data);
			
			//remove any style attributes, IE allows too much stupid things in them, eg.
			//<span style="width: expression(alert('Ping!'));"></span> 
			// and in general you really don't want style declarations in your UGC
			
			$data = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $data);
			
			//remove namespaced elements (we do not need them...)
			$data = preg_replace('#</*\w+:\w[^>]*>#i', "", $data);
			//remove really unwanted tags
			
			do {
				$olddata = $data;
				$data = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $data);
			} while ($olddata != $data);
			
			return $data;
		}
		
	}
	
?>