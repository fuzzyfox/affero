<?php
	
	/**
     * All Tests
     *
     * This file gets, and runs all the testcases available for affero. All tests
     * are written using the simpletest framework, as is this file. This file
     * acts like a test suite.
     *
     * @author William Duyck
     * @version 0.1
     * @package affero
     * @subpackage testing
     * @copyright 2011 William Duyck
     * @license MPL 1.1/LGPL 2.1/GPL 2.0
     */
	
	//get simpletest library
	require_once('/simpletest/autorun.php');
	
	/**
	 * AllTests
	 * 
	 * This test suite runs all the tests it finds in the same directory as
	 * itself.
	 */
	class AllTests extends TestSuite
	{
		/**
		 * AllTests
		 * 
		 * The constructor for the class. It will run automatically when the
		 * class is run, and in turn, run all the test cases in the current
		 * directory.
		 *
		 * It scans the /test/php/testcase directory for all files and attempts
		 * to run them as testcases. It then gets the results and reports any
		 * errors.
		 */
		function AllTests()
		{
			//Set the name of the test suite
			$this->TestSuite('All tests');
			/*
			 get the directory listing and loop through each file assigning its
			 name to the $file variable each time.
			*/
			foreach(glob(dirname(__FILE__).'/testcase/*', GLOB_MARK) as $file)
			{
				/*
				 check if the file found is not infact a directory and that it
				 is a testcase we want to run.
				*/
				if(!is_dir($file))
				{
					/*
					 add this file to the set of testcases we want to run in
					 this test suite.
					*/
					$this->addFile($file);
				}
			}
		}
	}

?>
