<?php

	//get simple test library
	require_once('simpletest/autorun.php');
	
	/**
	 * This test suite runs all the tests it finds in the same directory as
	 * itself.
	 */
	class AllTests extends TestSuite
	{
		/**
		 * The constructor for the class. It will run automatically when the
		 * class is run, and in turn, run all the test cases in the current
		 * directory.
		 */
		function AllTests()
		{
			//Set the name of the test suite
			$this->TestSuite('All tests');
			//get the directory listing and loop through each file assigning its
			//name to the $file variable each time
			foreach(glob(dirname(__FILE__).'/testcase/*', GLOB_MARK) as $file)
			{
				//check if the file found is not infact a directory and that it
				//is a testcase we want to run
				if(!is_dir($file))
				{
					//add this file to the set of testcases we want to run in
					//this test suite.
					$this->addFile($file);
				}
			}
		}
	}

?>
