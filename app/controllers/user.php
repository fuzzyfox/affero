<?php
	
	/**
	 * User Controller
	 *
	 * This file contains a collection of tools for logging in/logging out users
	 * as well as creating them, modifying them, and deleting them from the
	 * database.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage controllers
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
	class User extends Controller
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
			if($this->utility->check_auth())
			{
				header('Location: '.$this->utility->site_url('dashboard'));
			}
		}
		
		/**
		 * create
		 *
		 * this function creates a user, it does so by checking the provided token,
		 * unencrypting it if it is valid, and then asking the user to provide a
		 * username and password. (the decrypted token is the users email)
		 *
		 * @access public
		 * @return void
		 */
		public function create()
		{
			if(!isset($_SESSION['user']['logged']))
			{
				//check a token was provided
				if($this->input->get('token') != false)
				{
					/*
					 query db for all needed information in this function
					*/
					//run raw query
					$queryResource = $this->database->query('SELECT user.username, user.userEmail FROM invite INNER JOIN user ON invite.inviter = user.username WHERE invite.token = '.$this->database->escape($this->input->get('token')).' LIMIT 1');
					//return the information from database
					$query = mysql_fetch_object($queryResource);
					
					//check the token exists
					if($this->database->num_rows() == 1)
					{
						//free up memory used in query
						$this->database->free_result();
						
						/*
						 decrypt token to get email address
						*/
						//set the key
						$key = $query->username.'_'.$query->userEmail;
						//get the user email
						$data['email'] = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, base64_decode($this->input->get('token')), MCRYPT_MODE_ECB);
						$data['token'] = $this->input->get('token');
						//regenerate user token for security
						$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
						//load the form to get the rest of the information we need
						$this->view->load('backend/create_user', $data);
					}
					else
					{
						//respond as a teapot (assume someone is attempting to hack into affero)
						header('HTTP/1.0 418 I\'m a teapot');
						return;
					}
				}
				//check we dont need to create a user
				elseif($this->input->post('token') == $_SESSION['user']['token'])
				{
					//check if the submitted username is not taken (and was taken)
					if(($this->input->post('username') != false)&&
					   ($this->database->get('user', array('username'=>$this->input->post('username')), 'username', 1)->num_rows == 0)&&
					   ($this->database->get('invite', array('invitee'=>$this->input->post('username')), 'invitee', 1)->num_rows == 0))
					{
						//check email is valid
						if(!$this->utility->valid_email($this->input->post('email')))
						{
							//email is invalid notify user
							header('Location: '.$this->utility->site_url('user/create?token='.$this->input->post('inviteToken').'&invalid=email'));
							return false;
						}
						//check the passwords match
						elseif(($this->input->post('password') != false)&&($this->input->post('password') == $this->input->post('confPassword')))
						{
							//create their account
							$data = array(
								'username' => strtolower($this->input->post('username')),
								'userPassword' => $this->utility->hash_string($this->input->post('password'), strtolower($this->input->post('username'))),
								'userEmail' => $this->input->post('email')
							);
							$this->database->insert('user', $data);
							$this->database->update('invite', array('token'=>$this->input->post('inviteToken')), array('invitee'=>$this->input->post('username')));
							
							//log user in
							session_regenerate_id(false);
							$_SESSION['user']['username'] = strtolower($this->input->post('username'));
							$_SESSION['user']['logged'] = true;
							header('Location: '.$this->utility->site_url('dashboard'));
						}
						else
						{
							//passwords dont match inform user
							header('Location: '.$this->utility->site_url('user/create?token='.$this->input->post('inviteToken').'&invalid=passwords'));
							return false;
						}
					}
					else
					{
						//username taken inform user
						header('Location: '.$this->utility->site_url('user/create?token='.$this->input->post('inviteToken').'&invalid=username'));
						return false;
					}
				}
				else
				{
					//notify that we are missing a token
					echo 'token not found, required to create an account';
				}
			}
			else
			{
				//oops they are not logged in they are not allowed to see this page...
				header('Location: '.$this->utility->site_url('user/login'));
				return false;
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
			if($this->utility->check_auth()&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//regenerate user token for security
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//load the form
				$this->view->load('backend/user_invite');
			}
			elseif($this->utility->check_auth())
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
						if(mail($this->input->post('receipient'), $this->config->invite->subject, $message, $headers))
						{
							//check we dont need to save to the database again
							if($this->database->get('invite', array('token'=>$replace['{token}']), 'token', 1)->num_rows == 0)
							{
								/*
								 okay all sent we better save that information into the database
								*/
								//set the data
								$data = array(
									'token' => $replace['{token}'],
									'inviter' => $_SESSION['user']['username']
								);
								//insert
								$this->database->insert('invite', $data);
							}
							//inform user we succeeded
							header('Location: '.$this->utility->site_url('user/invite?success=true'));
						}
						else
						{
							//failed to send inform user
							header('Location: '.$this->utility->site_url('user/invite?success=false'));
							return;
						}
					}
					else
					{
						//not all fields entered, lets notify the user
						header('Location: '.$this->utility->site_url('user/invite?invalid=sender'));
						return;
					}
					
				}
				elseif($this->utility->check_auth())
				{
					//invalid email inform user
					header('Location: '.$this->utility->site_url('user/invite?invalid=email'));
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
			if($this->utility->check_auth()&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//regenerate user token for security
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//get the users currently known email
				$query = $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userEmail', 1);
				$data['userEmail'] = $query->results[0]->userEmail;
				//send the email to the view and load the view
				$this->view->load('backend/user_settings', $data);
			}
			//okay time to process the form
			elseif($this->utility->check_auth()&&($this->input->post('oldPassword') != false))
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
							header('Location: '.$this->utility->site_url('user/settings?invalid=email'));
							return;
						}
					}
				}
				else
				{
					header('Location: '.$this->utility->site_url('user/settings?invalid=email'));
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
					header('Location: '.$this->utility->site_url('user/settings?invalid=new'));
					return;
				}
				
				//report back to the user
				header('Location: '.$this->utility->site_url('user/settings?success='.(($success)?'true':'false')));
				return;
			}
			elseif($this->utility->check_auth())
			{
				//settings changed but not received password confirmation
				header('Location: '.$this->utility->site_url('user/settings?invalid=old'));
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
			if(($this->utility->check_auth())&&($this->input->post('token') != $_SESSION['user']['token']))
			{
				//create new session token to ensure that the form is not being used for CSRF
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				//load the confimation form
				$this->view->load('backend/delete_user');
			}
			//check auth and if password submitted
			elseif($this->utility->check_auth()&&($this->input->post('password') != false))
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
						header('Location: '.$this->utility->site_url('dashboard?msg=user_delete'));
						return;
					}
					else
					{
						//oops something went wrong!
						header('Location: '.$this->utility->site_url('user/delete?failed=true'));
						return;
					}
				}
				else
				{
					//logged in but incorrect password
					header('Location: '.$this->utility->site_url('user/delete?invalid=true'));
					return;
				}
			}
			//empty password field check auth
			elseif($this->utility->check_auth())
			{
				//logged in but no password submitted
				header('Location: '.$this->utility->site_url('user/delete?invalid=true'));
				return;
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
				header('Location: '.$this->utility->site_url('dashboard'));
			}
			elseif((!isset($_SESSION['user']['token']))||($this->input->post('token') !== $_SESSION['user']['token']))
			{
				$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
				$this->view->load('backend/login');
			}
			elseif($this->process_login($this->input->post('username'), $this->input->post('password')))
			{
				//regenerate the session id but dont delete old session
				session_regenerate_id(false);
				$_SESSION['user']['username'] = strtolower($this->input->post('username'));
				$_SESSION['user']['logged'] = true;
				header('Location: '.$this->utility->site_url('dashboard'));
			}
			else
			{
				header('Location: '.$this->utility->site_url('user/login?invalid=true'));
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
			$query = $this->database->get('user', array('username'=>$username), 'userPassword', 1);
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
				header('Location: '.$this->utility->site_url('dashboard'));
				return true;
			}
			else
			{
				return false;
			}
		}
		
	}
	
?>