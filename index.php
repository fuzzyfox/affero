<?php
	
	//get the glue library
	require_once(dirname(__FILE__).'/app/libraries/glue.lib.php');
	
	//setup url mapping with the help of glue
	$urls = array(
		'/' => 'index'
	);
	
	/**
	 * index
	 *
	 * the controller for all things relating to the main index page of the app
	 */
	class Index
	{
		function GET()
		{
			include('./index.html');
		}
	}
	
	glue::stick($urls);
?>