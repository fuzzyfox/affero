<?php
	
	/**
	 * Dashboard Controller
	 *
	 * This file contains a collection of tools for the running of the affero
	 * dashboard
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage controllers
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Dashboard
	 *
	 * This class provides all the processing behind the dashboard which is both
	 * public and user access able.
	 */
	class Dashboard extends Controller
	{
		
		/**
		 * index
		 *
		 * This loads the view as well as the data that drives it
		 */
		function index()
		{
			$this->view->load('backend/dashboard');
		}
		
	}
	
?>