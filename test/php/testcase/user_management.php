<?php
	
	/**
	 * Test Of User Management Class
	 *
	 * This testcase will check that the User Management Class is fully
	 * functional and fit for purpose.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage testing
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	//include the simple test framework
	require_once(dirname(__FILE__).'/../simpletest/autorun.php');
	//include the required libraries
	require_once(dirname(__FILE__).'/../../../app/libraries/utilities.lib.php');
	require_once(dirname(__FILE__).'/../../../app/libraries/input.lib.php');
	require_once(dirname(__FILE__).'/../../../app/libraries/database.lib.php');
    //include the user management class
	require_once(dirname(__FILE__).'/../../../app/modules/user.php');
	
	class TestOfUserManagementClass extends UnitTestCase
	{
		/**
		 * setUp
		 *
		 * create an new instance of the user management class
		 */
		function setUp()
		{
			$this->user = new User();
		}
		
		/**
		 * tearDown
		 *
		 * destroies the instance of the user management class ready for the next 
		 * test
		 */
		function tearDown()
		{
			unset($this->user);
		}
		
		/**
		 * TestSessionCreate
		 *
		 * This function will test that a new session is created on successfull
		 * login and that the session is destroied on logout
		 *
		 * Will attempt to login with a number of invlaid credentials, then use 
		 * valid credentials to complete a successful login. The login method
		 * from the user class should assert true on success and false on failure
		 *
		 * The function will then test to see if a session has been created for
		 * the login and that it stores the correct username.
		 */
		function testSessionCreateAndDestroy()
		{
			//attempt invalid login (wrong user so wrong pass)
			$this->assertFalse($this->user->login('invlaid.user', 'blarBlar'));
			//attempt invalid login (right user, wrong pass)
			$this->assertFalse($this->user->login('john.doe', 'blarBlar'));
			//attempt valid login
			$this->assertTrue($this->user->login('john.doe', 'secretpassword'));
			//check for the session
			$this->assertEqual($_SESSION['username'], 'john.doe');
			//attempt a logout
			$this->assertTrue($this->user->logout());
			//check session no longer exists
			$this->assertFalse(isset($_SESSION['username']));
		}
		
	}
	
?>