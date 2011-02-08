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
	 * @todo user class items
	 * 	- create add user function
	 */
	class User extends Backend
	{
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
		 * invite
		 *
		 * provides a way for existing users to invite new users
		 *
		 * @todo invite system
		 * - refactor the code
		 *
		 * @access public
		 * @return void
		 */
		public function invite()
		{
			if($this->check_auth()&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//regenerate user token for security
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//load the form
				$this->utility->view('backend/user_invite');
			}
			elseif($this->check_auth())
			{
				//check valid email
				if($this->utility->valid_email($this->input->post('receipient')))
				{
					//check all fields entered
					if($this->input->post('sender') != false)
					{
						/*
						 okay so lets generate a token
						*/
						//construct key
						$key = $_SESSION['user']['username'].'_'.($this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userEmail', 1)->results[0]->userEmail);
						//check that the email address has not been submitted by this user before
						$replace['{token}'] = base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $this->input->post('receipient'), MCRYPT_MODE_ECB));
						
						//set remaining data to send in email
						$replace['{sender}'] = $this->input->post('sender');
						
						//set the strings to replace with data
						$search = array('{token}', '{sender}');
						
						//construct email
						$message = wordwrap(str_replace($search, $replace, $this->config->invite->template), 70);
						
						//set message headers
						$headers = 'From: '.$this->config->invite->replyto.'\r\nReply-To: '.$this->config->invite->replyto.'\r\nX-Mailer: PHP/'.phpversion();
						
						//send email and check it did send
						if(mail($this->input->post('receipient'), $this->config->invite->subject, $message))
						{
							//check we dont need to save to the database again
							if(!$replace['{token}'] == $this->database->get('invite', array('inviter'=>$_SESSION['user']['username']), 'token', 1)->results[0]->token)
							{
								/*
								 okay all sent we better save that information into the database
								*/
								//set the data
								$data = array(
									'token' => $replace['token'],
									'inviter' => $_SESSION['user']['username']
								);
								//insert
								$this->database->insert('invite', $data);
							}
							//inform user we succeeded
							header('Location: '.$this->utility->site_url('backend/user/invite?success=true'));
						}
						else
						{
							//failed to send inform user
							header('Location: '.$this->utility->site_url('backend/user/invite?success=false'));
							return;
						}
					}
					else
					{
						//not all fields entered, lets notify the user
						header('Location: '.$this->utility->site_url('backend/user/invite?invalid=sender'));
						return;
					}
					
				}
				elseif($this->check_auth())
				{
					//invalid email inform user
					header('Location: '.$this->utility->site_url('backend/user/invite?invalid=email'));
					return;
				}
			}
		}
		
		/**
		 * settings
		 *
		 * provides the ability to change user settings such as password and
		 * email address
		 *
		 * @return void
		 * @access public
		 */
		public function settings()
		{
			//check user logged in and if the form was submitted
			if($this->check_auth()&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//regenerate user token for security
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//get the users currently known email
				$query = $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userEmail', 1);
				$data['userEmail'] = $query->results[0]->userEmail;
				//send the email to the view and load the view
				$this->utility->view('backend/user_settings', $data);
			}
			//okay time to process the form
			elseif($this->check_auth()&&($this->input->post('oldPassword') != false))
			{
				//bool to track if we fail to update the database
				$success = true;
				
				//do we need to change the email
				if($this->input->post('email') != false)
				{
					//check email is different
					$query = $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userEmail', 1);
					if($this->input->post('email') != $query->results[0]->userEmail)
					{
						//check email is valid
						if($this->utility->valid_email($this->input->post('email')))
						{
							//make the change
							if(!$this->database->update('user', array('username'=>$_SESSION['user']['username']), array('userEmail'=>$this->input->post('email'))))
							{
								$success = false;
							}
						}
						//not a valid email
						else
						{
							header('Location: '.$this->utility->site_url('backend/user/settings?invalid=email'));
							return;
						}
					}
				}
				else
				{
					header('Location: '.$this->utility->site_url('backend/user/settings?invalid=email'));
					return;
				}
				
				//do we need to change the users password?
				if(($this->input->post('newPassword') != false)&&($this->input->post('confirmPassword') == $this->input->post('newPassword')))
				{
					//get ready to change password
					$where = array('username'=>$_SESSION['user']['username']); //constraints on rows to update
					$data = array('userPassword'=>$this->utility->hash_string($this->input->post('newPassword'), $_SESSION['user']['username'])); //new data for rows
					//make the change and check it worked
					if(!$this->database->update('user', $where, $data))
					{
						$success = false;
					}
				}
				elseif(($this->input->post('newPassword') != false)||($this->input->post('oldPassword') != false))
				{
					//passwords do not match redirect back to form with msg informing user
					header('Location: '.$this->utility->site_url('backend/user/settings?invalid=new'));
					return;
				}
				
				//report back to the user
				header('Location: '.$this->utility->site_url('backend/user/settings?success='.(($success)?'true':'false')));
				return;
			}
			elseif($this->check_auth())
			{
				//settings changed but not received password confirmation
				header('Location: '.$this->utility->site_url('backend/user/settings?invalid=old'));
				return;
			}
		}
		
		/**
		 * delete
		 *
		 * This function is responsible for the deleting users. It will ask the
		 * user for confirmation that they wish to remove their account as well
		 * as check that they have the correct password so as to avoid someone
		 * removing their account by accident, and for added security.
		 *
		 * On success this function will run the logout process, and redirect the
		 * user to the publicly available dashboard with a notice informing them
		 * of success.
		 *
		 * Only logged in users may use this method
		 *
		 * @return void
		 * @access public
		 */
		public function delete()
		{
			//check that the user is logged in before we do anything else
			if(($this->check_auth())&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//create new session token to ensure that the form is not being used for CSRF
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//load the confimation form
				$this->utility->view('backend/delete_user');
			}
			//check auth and if password submitted
			elseif($this->check_auth()&&($this->input->post('password') != false))
			{
				//get the users password and compare to the hashed version of what they provided
				$query = $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userPassword', 1);
				//compate passwords
				if($query->results[0]->userPassword == $this->utility->hash_string($this->input->post('password'), $_SESSION['user']['username']))
				{
					//attempt to delete account
					if($this->database->delete('user', array('username'=>$_SESSION['user']['username'])))
					{
						//all the session data to do with users is in $_SESSION['user'] so lets delete that
						unset($_SESSION['user']);
						//lets destroy the session too just to be on the safe side
						session_destroy();
						//finally redirect user with msg
						header('Location: '.$this->utility->site_url('backend/dashboard?msg=user_delete'));
						return;
					}
					else
					{
						//oops something went wrong!
						header('Location: '.$this->utility->site_url('backend/user/delete?failed=true'));
						return;
					}
				}
				else
				{
					//logged in but incorrect password
					header('Location: '.$this->utility->site_url('backend/user/delete?invalid=true'));
					return;
				}
			}
			//empty password field check auth
			elseif($this->check_auth())
			{
				//logged in but no password submitted
				header('Location: '.$this->utility->site_url('backend/user/delete?invalid=true'));
				return;
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
				header('Location: '.$this->utility->site_url('backend/dashboard'));
			}
			else
			{
				header('Location: '.$this->utility->site_url('backend/user/login?invalid=true'));
			}
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
		 * on success will redirect to the backend dashboard
		 *
		 * @return bool false on failed logout.
		 */
		function logout()
		{
			//all the session data to do with users is in $_SESSION['user'] so lets delete that
			unset($_SESSION['user']);
			//lets destroy the session too just to be on the safe side
			session_destroy();
			
			//okay lets just check that worked before sending true/false
			if(!isset($_SESSION['user']))
			{
				header('Location: '.$this->utility->site_url('backend/dashboard'));
				return true;
			}
			else
			{
				return false;
			}
		}
		
	}
	
?>