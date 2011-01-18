<?php
	
	/**
	 * Configuration Test Case
	 *
	 * This file contains all the code used to test the configuration file that
	 * sets the application constants for affero
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage testing
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
    
    require_once(dirname(__FILE__).'/../simpletest/autorun.php');
    require_once(dirname(__FILE__).'/../../../app/config.php');
    
    class TestOfDeferoConfiguration extends UnitTestCase
    {
        
        /**
         * test configuration constants exist and setup
         * 
         * test to see if the configuration file creates a constants for use
         * throughout the application that represent each configurable option
         */
        function testConfigObjectsExist()
        {
			//database configuration
			$this->assertIsA(DB_HOST, 'string');
			$this->assertIsA(DB_NAME, 'string');
			$this->assertIsA(DB_USER, 'string');
			$this->assertIsA(DB_PASS, 'string');
			//site configuration
			$this->assertIsA(SITE_NAME, 'string');
			$this->assertIsA(SITE_URL, 'string');
			$this->assertIsA(DEVELOPMENT_ENVIRONMENT, 'boolean');
			
        }
        
    }
    
?>
