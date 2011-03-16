<?php
	
	/**
	 * Affero Controller
	 *
	 * This file contains all the controls for the front end of affero, from the
	 * recomendation engine to the form that gets user details/interests/skills
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage controllers
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Affero
	 *
	 * This class provides all the processing behind the front end which is both
	 * public and user access able.
	 */
	class Affero extends Controller
	{
		
		function index()
		{
			$data['timeRequirements'] = $this->database->get('timeRequirement', null, 'timeRequirementID, timeRequirementShortDescription');
			$this->view->load('frontend/index', $data);
		}
		
	}
	
?>