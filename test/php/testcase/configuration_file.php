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
    
    require_once('/simpletest/autorun.php');
    require_once(dirname(__FILE__).'/../../../app/config.php');
    
    class TestConfiguration extends UnitTestCase
    {
        
        /**
         * test configuration constants exist and setup
         */
        function testConfigObjectsExist()
        {
						
        }
        
    }
    
?>
