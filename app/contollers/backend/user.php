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
	
	//start session (normally done in the application root)
	session_set_cookie_params(3600, '/', 'localhost', false, true);
	session_start();
	session_name('affero_session');
	
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
	class User
	{
		/**
		 * __construct
		 *
		 * this function is temp till I work out the final structure of the
		 * application. Right now it sets up and initilises the input class and
		 * a database connection.
		 */
		function __construct()
		{
			$this->input = new Input();
			$this->database = new Database('localhost', 'ccw_dev', 'root', '');
			$this->utility = new Utilities();
		}
		
		/**
		 * login
		 *
		 * this function is responsible for the creation of sessions relating to
		 * users PROVIDED that the function receives valid credentials.
		 *
		 * @param string $username the username of the user to login
		 * @param string $password the password of the user to login
		 * @return bool true on successful login
		 */
		function login($username, $password)
		{
			//set the username to lower for usage
			$username = strtolower($username);
			//query the database for the provided user
			$query = $this->database->get('user', array('username'=>$username), 'username, userPassword', 1);
			//check that there is a match for the username and that the passwords match
			if(($query->num_rows == 1)&&($this->utility->hash_string($password, $username) == $query->results[0]->userPassword))
			{
				//valid user lets log them in
				$_SESSION['username'] = $username;
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
			unset($_SESSION['username']);
			session_destroy();
			
			if(!isset($_SESSION['username']))
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