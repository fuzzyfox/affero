<?php
	
	/**
	 * User Class
	 *
	 * This file contains a collection of tools for logging in/logging out users
	 * as well as creating them, modifying them, and deleting them from the
	 * database.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage modules
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * User
	 *
	 * This class deals with all user management functions from logging users in
	 * to creating/removing their accounts.
	 *
	 * @todo
	 * 	- create add_user function
	 * 	- create remove_user function
	 * 	- complete/refine logout function
	 * 	  - comment the code
	 * 	  - remove all user related session variables with simple loop
	 * 	- remove the __construct() as soon as final application structure worked
	 * 	  out
	 */
	class User extends Backend
	{
		/**
		 * __construct
		 *
		 * Initialises the session needed for all authentication/user tracking
		 */
		function __construct()
		{
			parent::__construct();
			//start session (normally done in the application root)
			session_set_cookie_params(3600, '/', parse_url(SITE_URL, PHP_URL_HOST), false, true);
			session_name('affero_session');
			session_start();
		}
		
		/**
		 * index
		 *
		 * provides a page to check if a user is logged in and routes them to either 
		 * the backend dashboard OR the login page depending on the result
		 *
		 * @return void
		 * @access public
		 */
		public function index()
		{
			if($this->check_auth())
			{
				header('Location: '.$this->utility->site_url('backend/dashboard'));
			}
		}
		
		/**
		 * check_auth
		 * 
		 * this is a simple helper function that checks to see if a users is or
		 * is not logged in. If they are not it redirects them to the login page
		 * and returns false, else it returns true with no redirect.
		 *
		 * @return bool True if user logged in
		 * @access private
		 */
		private function check_auth()
		{
			//do the check
			if(isset($_SESSION['user']['logged']) && ($_SESSION['user']['logged'] == true))
			{
				//return true the user is logged in
				return true;
			}
			else
			{
				//oops they are not logged in they are not allowed to see this page...
				header('Location: '.$this->utility->site_url('backend/user/login'));
				return false;
			}
		}
		
		/**
		 * login
		 * 
		 * this function is the outer face of the login system. It handles both
		 * get and post requests. When receiving a get request it loads the login
		 * form and on a post request uses the input class to get the username and
		 * password submitted and hand it off for processing to process_login
		 *
		 * @return void
		 * @access public
		 */
		public function login()
		{
			if(isset($_SESSION['user']['logged']) && ($_SESSION['user']['logged'] == true))
			{
				header('Location: '.$this->utility->site_url('backend/dashboard'));
			}
			elseif((!isset($_SESSION['user']['token']))||($this->input->post('token') !== $_SESSION['user']['token']))
			{
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				$this->utility->view('backend/login');
			}
			elseif($this->process_login($this->input->post('username'), $this->input->post('password')))
			{
				//regenerate the session id but dont delete old session
				session_regenerate_id(false);
				$_SESSION['user']['username'] = strtolower($this->input->post('username'));
				$_SESSION['user']['logged'] = true;
			}
			else
			{
				header('Location: '.$this->utility->site_url('backend/user/login?invalid=true'));
			}
			print_r($_SESSION);
		}
		
		/**
		 * process_login
		 *
		 * this function is responsible for the creation of sessions relating to
		 * users PROVIDED that the function receives valid credentials.
		 *
		 * @param string $username the username of the user to login
		 * @param string $password the password of the user to login
		 * @return bool true on successful login
		 * @access private
		 */
		private function process_login($username, $password)
		{
			//set the username to lower for usage
			$username = strtolower($username);
			//query the database for the provided user
			$query = $this->database->get('user', array('username'=>$username), 'username, userPassword', 1);
			//check that there is a match for the username and that the passwords match
			if(($query->num_rows == 1)&&($this->utility->hash_string($password, $username) == $query->results[0]->userPassword))
			{
				//valid details, lets welcome them back
				return true;
			}
			else
			{
				//invalid user lets not let them into our system
				return false;
			}
		}
		
		/**
		 * logout
		 *
		 * this function destroies user session information and logs them out of
		 * the app.
		 *
		 * @return bool true on successful logout.
		 */
		function logout()
		{
			unset($_SESSION['user']);
			session_destroy();
			
			if(!isset($_SESSION['user']))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
	}
	
?>