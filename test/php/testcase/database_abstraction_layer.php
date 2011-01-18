<?php
    
    /**
	 * Database Abstraction Layer Test Case
	 *
	 * This file contains all the code used to test the database abstraction
	 * layer. Tests are run automatically on file load.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage testing
	 * @copyright MPL 1.1/LGPL 2.1/GPL 2.0 William Duyck
	 */
    
    //include the simple test framework
	require_once(dirname(__FILE__).'/../simpletest/autorun.php');
    //include the database abstraction layer
	require_once(dirname(__FILE__).'/../../../app/libraries/database.lib.php');
	
	/**
     * TestOfDatabaseAbstractionLayer
     * 
	 * This class tests all methods in the Database class. It is an extension
	 * of the UnitTestCase class provided by the simpletest unit testing framework.
	 *
	 * All database connections will be made to the database at 'localhost' named
	 * 'ccw_dev' with the username 'root'. The database is set to have no password.
	 */
	class TestOfDatabaseAbstractionLayer extends UnitTestCase
	{
		/**
         * setUp
		 *
		 * Setup for a new database object for each test
		 */
		function setUp()
		{
			$this->db = new Database();
		}
		
		/**
         * tearDown
         * 
		 * Remove the database object at the end of each test to avoid tests
		 * influencing the result of eachother
		 */
		function tearDown()
		{
			unset($this->db);
		}
		
		/**
         * testDatabaseConnectDisconnect
         * 
		 * Test if able to connect/disconnect to the database. Both methods should
		 * assert true if no errors were encountered.
		 *
		 * The connect method should accept a host, database name, username, and
		 * password argument
		 *
		 * The disconnect method should not accept any arguments
		 */
		function testDatabaseConnectDisconnect()
		{
			$this->assertTrue($this->db->connect('localhost', 'ccw_dev', 'root', ''));
			$this->assertTrue($this->db->disconnect());
		}
		
		/**
         * testDatabaseInitConnectAlias
         * 
		 * Test the Database class connects to a database on initilization
		 * provided information of the database to connect to.
		 *
		 * The alias should be used in the following manner:
		 * 
		 * 		$database = new Database(host, database name, username, password);
		 */
		function testDatabaseInitConnectAlias()
		{
            //unset the created Database instance from setup
			unset($this->db);
            //create a new instance
            $this->db = new Database('localhost', 'ccw_dev', 'root', '');
            //check connection by attempting to get user wduyck
            $query = $this->db->query("SELECT * FROM user WHERE username = 'wduyck'");
			//take the results from the query and make them usable.
			$row = mysql_fetch_object($query);
			
			//check that test data matches that retrieved from the database
			$this->assertIdentical($row->username, 'wduyck');
            
            //disconnect from the database
            $this->db->disconnect();
		}
		
		/**
		 * testDatabaseQueryMethod
		 * 
		 * This test will attempt to get the first user in the database and check
		 * that the details match with what should already be in user record one.
		 *
		 * Test data stored:
		 * 	* username = wduyck
		 * 	* password = 4e2776866a185e0a9e090f5ed0a6fecfb31fe90b3c4a219b0107196c9e4759bd83cebcb600550a2af2c3e45ad1b67dc2486e5f382e36eaaa224ff651ca86ba6c
		 * 	* email = wduyck@gmail.com
		 *
		 * The query method should accept JUST a sql query string.
		 */
		function testDatabaseQueryMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			//run a simple query to attempt to retrieve the test data
			$query = $this->db->query("SELECT * FROM user WHERE username = 'wduyck'");
			//take the results from the query and make them usable.
			$row = mysql_fetch_object($query);
			
			//check that test data matches that retrieved from the database
			$this->assertIdentical($row->username, 'wduyck');
			$this->assertIdentical($row->userPassword, '4e2776866a185e0a9e090f5ed0a6fecfb31fe90b3c4a219b0107196c9e4759bd83cebcb600550a2af2c3e45ad1b67dc2486e5f382e36eaaa224ff651ca86ba6c');
			$this->assertIdentical($row->userEmail, 'wduyck@gmail.com');
			
			//disconnect from the databse
			$this->db->disconnect();
		}
		
		/**
		 * testEscapeMethod
		 *
		 * This test will pass a two strings into the function and check that the
		 * output is what is expected which is a string with quotation marks
		 * around it. Should the string already contain quotation marks then these
		 * should be escaped with a backslash.
		 *
		 * An interger and boolean will also be passed into the method to check
		 * that it spits them straight back out unchanged.
		 *
		 * The escape method should accept anything that is passed into the first
		 * argument.
		 */
		function testEscapeMethod()
		{
			$this->assertIdentical($this->db->escape('string'), "'string'");
			$this->assertIdentical($this->db->escape(2), 2);
			$this->assertIdentical($this->db->escape(false), false);
			$this->assertIdentical($this->db->escape("'string'"), "'\'string\''");
		}
		
		/**
		 * testDatabaseInsertMethod
		 * 
		 * This test will create a new user in the database and then check that
		 * we can get the same information back using the query method afterwards.
		 *
		 * It works on the assumption that the query method has passed its test
		 * and works as expected.
		 *
		 * Test data:
		 * 	* username = test_user
		 * 	* password = sha512 hash of 'eiugbkwabgp9bgv3eB£Egib'
		 * 	* email = test@example.com
		 *
		 * The insert method should accept the following arguments
		 * 	* table name
		 * 	* an associative array of data in the form 'field name' => 'value'
		 */
		function testDatabaseInsertMethod()
		{
			//test data to be submitted to the database
			$data = array(
				'username' => 'test_user',
				'userPassword' => hash('sha512', 'eiugbkwabgp9bgv3eB£Egib'),
				'userEmail' => 'test@example.com'
			);
			
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			
			//insert method should return a bool on completion (true on success)
			$this->assertTrue($this->db->insert('user', $data));
			
			//check the data is in the database
			$query = $this->db->query("SELECT * FROM user WHERE username = 'test_user'");
			$row = mysql_fetch_object($query);
			
			$this->assertIdentical('test_user', $row->username);
			$this->assertIdentical(hash('sha512', 'eiugbkwabgp9bgv3eB£Egib'), $row->userPassword);
			$this->assertIdentical('test@example.com', $row->userEmail);
			
			//disconnect from database
			$this->db->disconnect();
		}
		
		/**
		 * testDatabaseUpdateMethod
		 * 
		 * This test will check to see if we can update information in the database 
		 * by modifying the test data added in testDatabaseInsertMethod.
		 *
		 * It will attempt to change the email to 'test@test.com' and will then
		 * use the query method to check that the data has been changed.
		 *
		 * The update method should accept the following arguments:
		 * 	* table name
		 * 	* associative array to select the row(s) to modify in the form 'field' => 'value'
		 * 	* associative array of new data in the form 'field' => 'value'
		 */
		function testDatabaseUpdateMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			
			//change email of test_user to test@test.com (true on success)
			$this->assertTrue($this->db->update('user', array('username'=>'test_user'), array('userEmail'=>'test@test.com')));
			
			//check that the data in the database matches
			$query = $this->db->query("SELECT userEmail FROM user WHERE username = 'test_user'");
			$row = mysql_fetch_object($query);
			
			$this->assertIdentical('test@test.com', $row->userEmail);
			
			//disconnect from database
			$this->db->disconnect();
		}
		
		/**
		 * Test that we can delete from the database by removing the test data we
		 * just added in the insert method test
		 */
		function testDatabaseDeleteMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			
			//delete the test data we added in the insert method test (true on success)
			$this->assertTrue($this->db->delete('user', array('username'=>'test_user')));
			
			//attempt to get the test data using the query method
			$query = $this->db->query("SELECT * FROM user WHERE username = 'test_user'");
			$this->assertEqual(0, mysql_num_rows($query));
			
			//disconnect from the database
			$this->db->disconnect();
		}
		
		/**
		 * Test the number of rows returned by a query by getting all the rows
		 * from the user table and running the num_rows method on the returned
		 * results, then compare this to the number returned by mysql_num_rows.
		 */
		function testDatabaseNumRowsMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			
			//run a query to get all rows from the user table
			$query = $this->db->query('SELECT * FROM user');
			
			//check the number of rows matches what we know we should have 
			$this->assertEqual(mysql_num_rows($query), $this->db->num_rows());
			
			//disconnect from database
			$this->db->disconnect();
		}
		
		/**
		 * test that the memory from the query is freed successfully. Will
		 * assert true on success.
		 */
		function testDatabaseFreeResultMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			//run a query so that we use some resources that we can then clear
			$this->db->query('SELECT * FROM user');
			
			//check results freed
			$this->assertTrue($this->db->free_result());
			
			//disconnect from database
			$this->db->disconnect();
		}
		
		/**
		 * test that error reporting works by attempting to select from a
		 * non-existant table in the database
		 */
		function testDatabaseGetErrorMethod()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			//generate an error
			$this->db->query('SELECT * FROM cake');
			//check the error is in the correct form and atainable
			$this->assertTrue(preg_match('/\[\d+\] .+/i', $this->db->get_error()));
			//disconnect from database
			$this->db->disconnect();
		}
		
		/**
		 * Test by attempting to retrieve just the email of user wduyck in the
		 * database and that the method returns an object representation of the
		 * data it gets.
		 *
		 * There should be one row, and the email should be wduyck@gmail.com
		 */
		function testDatabaseGetMethodWithLimitations()
		{
			//connect to the database
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			//get the users
			$query = $this->db->get('user', array('username'=>'wduyck'), 'userEmail');
			//check that the result is in the correct form
			$this->assertIsA($query, 'stdClass');
			//check that correct num rows in returned object
			$this->assertEqual(1, $query->num_rows);
			//check that the expected query was generated
			$this->assertIdentical("SELECT userEmail FROM user WHERE username = 'wduyck'", $query->query);
			//check that the email is correct
			$row = $query->results[0];
			$this->assertIdentical('wduyck@gmail.com', $row->userEmail);
		}
		
		/**
		 * Test attempts to optimise the user table, should assert true on success
		 */
		function testDatabaseTableOptimizeMethod()
		{
			$this->db->connect('localhost', 'ccw_dev', 'root', '');
			$this->assertTrue($this->db->optimize('user'));
			$this->db->disconnect();
		}
		
	}

?>
