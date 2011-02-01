<?php
    
    /**
	 * Utilities Test Case
	 *
	 * This file contains all the code used to test the utilities class that
	 * contains general functions that can be of use in areas of the application
	 * but that are not specific to a particular part of the application.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage testing
	 * @copyright MPL 1.1/LGPL 2.1/GPL 2.0 William Duyck
	 */
    
    //include the simple test framework
	require_once(dirname(__FILE__).'/../simpletest/autorun.php');
    //include the utilities class
	require_once(dirname(__FILE__).'/../../../app/libraries/utilities.lib.php');
	
	/**
     * TestOfUtilitiesClass
     * 
	 * This class will test the utilities class. It will test all the methods
	 * that are available for use from the utilities class. These methods are
	 * not related to any specifc areas of the application.
	 */
	class TestOfUtilitiesClass extends UnitTestCase
	{
		/**
         * setUp
		 *
		 * Setup for a new utilities object for each test
		 */
		function setUp()
		{
			$this->utils = new Utilities();
		}
		
		/**
         * tearDown
         * 
		 * Remove the utilities object at the end of each test to avoid tests
		 * influencing the result of eachother
		 */
		function tearDown()
		{
			unset($this->utils);
		}
		
		/**
         * testCurrentUrlMethod
         * 
		 * This test will attempt to check that the url returned by the
		 * current_url method is the same as the url of this file.
		 *
		 * The current_url method should not accept any parameters and should
		 * return a string that matches the url of the current page.
		 *
		 * This test will only pass when the expected url of this test file is
		 * known and manually programed into the regex of the assertion.
		 */
		function testCurrentUrlMethod()
		{
			$this->assertIsA($this->utils->current_url(), 'string');
			$this->assertPattern('/https?:\/\/'.str_replace('.', '\.', $_SERVER['HTTP_HOST']).'(\/affero)?\/test\/php\/testcase\/utilities\.php/i', $this->utils->current_url());
		}
		
		/**
		 * testHashStringMethod
		 *
		 * This test will attempt to verify that the hash_string method returns
		 * an sha512 hash that has a salt.
		 *
		 * The hash_string method should accept a string to hash and an optional
		 * salt.
		 *
		 * Test Data:
		 * 	- string to hash = 'HashMePlease'
		 * 	- salt = 'WithASaltToo'
		 *
		 * Algorithm:
		 *
		 * 	if salt provided the method should hash {string}_{salt} else just {string}
		 *
		 * Expected Returns:
		 * 	- without salt = '632c18211a33f50e32981cc46c95d015098bcfa5432f3d09e0fadf1effb2189fd208b4e8828071011fe187a592c53f0ccf95b4ff97b5054fba0ac87a6eedc5ca'
		 * 	- with salt = 'babf3f6d79803c7cb6d07737dbfeb0a9c3cbe81411be23c60549dd3155a1288b96b650d32331e7d69f0690c87c6687cc68d1fb01493f14c7d8678d3d62d83491'
		 */
		function testHashStringMethod()
		{
			/*
			 Set test data some convienient variables
			*/
			$stringToHash = 'HashMePlease';
			$salt = 'WithASaltToo';
			
			/*
			 test without a salt
			*/
			$this->assertIsA($this->utils->hash_string($stringToHash), 'string');
			$this->assertIdentical($this->utils->hash_string($stringToHash), '632c18211a33f50e32981cc46c95d015098bcfa5432f3d09e0fadf1effb2189fd208b4e8828071011fe187a592c53f0ccf95b4ff97b5054fba0ac87a6eedc5ca');
			
			/*
			 test with a salt
			*/
			$this->assertIsA($this->utils->hash_string($stringToHash, $salt), 'string');
			$this->assertIdentical($this->utils->hash_string($stringToHash, $salt), 'babf3f6d79803c7cb6d07737dbfeb0a9c3cbe81411be23c60549dd3155a1288b96b650d32331e7d69f0690c87c6687cc68d1fb01493f14c7d8678d3d62d83491');
		}
		
	}

?>
