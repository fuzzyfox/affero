<?php
	
	/**
	 * Manage Controller
	 *
	 * This file contains a collection of tools for managing the areas of
	 * contribution, skills, and time requirements that are used when generating
	 * recomendations on the front end of affero
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage controllers
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Manage
	 *
	 * This class provides all the processing behind the views that allow users
	 * to make changes to areas of contribution, etc...
	 */
	class Manage extends Controller
	{
		/**
		 * index
		 *
		 * This is the default page for all management tasks, it will likely
		 * contain some warnings and links to different common tasks
		 */
		function index()
		{
			if($this->utility->check_auth())
			{
				/*
				 get parent areas of contribution
				*/
				$queryResource = $this->database->query("SELECT areaSlug, areaName, areaUrl, areaDescription, timeRequirementShortDescription FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID WHERE area.areaParentSlug = 'root'");
				while($queryParents = mysql_fetch_object($queryResource))
				{
					$query['parents'][] = $queryParents;
				}
				
				if($this->database->num_rows() > 0)
				{
					$this->database->free_result();
					/*
					 get child areas of contibution
					*/
					$queryResource = $this->database->query("SELECT areaSlug, areaName, areaUrl, areaDescription, areaParentSlug, timeRequirementShortDescription FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID  WHERE area.areaParentSlug != 'root'");
					while($queryChildren = mysql_fetch_object($queryResource))
					{
						$query['children'][] = $queryChildren;
					}
					/*
					 mash sets together
					*/
					$data['areas'] = new stdClass;
					foreach($query['parents'] as $parent)
					{
						$parentSlug = $parent->areaSlug;
						$data['areas']->$parentSlug = $parent;
						foreach($query['children'] as $child)
						{
							if($child->areaParentSlug == $parent->areaSlug)
							{
								$data['areas']->$parentSlug->children[] = $child;
							}
						}
					}
				}
				
				$this->view->load('backend/manage_index', $data);
			}
		}
		
		/**
		 * tag
		 *
		 * This function handles editing/deleting/creating tags
		 */
		function tag()
		{
			
		}
		
		/**
		 * area
		 *
		 * This function handles editing/deleting/creating areas
		 */
		function area()
		{
			//get the url segments for loading specific views/calling correct functions
			$urlSegment = func_get_arg(0);
			
			//check the user is logged in
			if($this->utility->check_auth())
			{
				//use a nice simple switch to load the correct view/function
				switch($urlSegment[7])
				{
					case 'add': //if we need to add an area
						//check we are not processing a form
						if($this->input->post('name') == false)
						{
							//refresh session token for security
							$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
							
							//load information from database for dropdown menus
							$data['parents'] = $this->database->get('area', array('areaParentSlug'=>'root'), 'areaSlug, areaName');
							$data['timeRequirements'] = $this->database->get('timeRequirement', null, 'timeRequirementID, timeRequirementShortDescription');
							
							//load view
							$this->view->load('backend/area_add', $data);
						}
						else
						{
							$this->area_add();
						}
					break;
					case 'delete':
						
					break;
					case 'edit':
						
					break;
					default:
						
					break;
				}
			}
		}
		
		/**
		 * area_add
		 *
		 * to reduce the size of the area function and for readability when a
		 * user adds a new area of contribution this function will be called into
		 * action to do the processing.
		 *
		 * @access private
		 */
		private function area_add()
		{
			//check that all required fields were submitted
			if(($this->input->post('name') != false)&&
			   ($this->input->post('url') != false)&&
			   ($this->input->post('description') != false)&&
			   ($this->input->post('time') != 'null'))
			{
				//something was not entered, inform the user
				header('Location: '.$this->utility->site_url('manage/area/add').'?invalid=missing');
			}
			//check that the slug is not used already
			elseif((preg_match('/\b[a-zA-Z0-9]*[a-zA-Z0-9\-]*[a-zA-Z0-9]*\b/'))&&($this->database->get('area', array('areaSlug'=>$this->input->post('slug')), 'areaSlug', 1)->num_rows == 0))
			{
				//start getting ready to add data to the database
				$data = array();
				
				//check slug no empty if so correct this and generate slug
				if($this->input->post('slug') == false)
				{
					//index 'areaSlug' stores the area name in hyphenated form + unique id
					$data['areaSlug'] = implode('-', explode(' ', strtolower($this->input->post('name')))).uniqid();
				}
				else
				{
					$data['areaSlug'] = strtolower($this->input->post('slug'));
				}
			}
			else
			{
				//invalid slug
			}
		}
	}
	
?>