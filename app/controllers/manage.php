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
	 *
	 * @todo Rethink edit functions
	 * - remove area_edit
	 * - remove area_edit view
	 * - refactor area to control add/edit/delete all in one
	 * - refactor area_add to work for both add and edit
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
				$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, timeRequirementShortDescription FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID WHERE area.areaParentSlug = 'root'");
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
					$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, areaDescription, areaParentSlug, timeRequirementShortDescription FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID  WHERE area.areaParentSlug != 'root'");
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
				
				$data['skills'] = $this->database->get('skill')->results;
				
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
			if($this->utility->check_auth())
			{
				$urlSegment = func_get_arg(0);
				$switch = explode('?', $urlSegment[7]);
				$switch = $switch[0];
				$switch = explode('/', $switch);
				
				if(($this->input->post('name') != false)&&($this->input->post('slug') != false))
				{
					$data = array(
						'skillTag' => strtolower(implode('_', explode(' ', $this->input->post('slug')))),
						'skillName' => $this->input->post('name')
					);
					
					if($this->input->post('add'))
					{
						//lets add the new tag, first does it already exist?
						if($this->database->get('skill', array('skillTag'=>$data['skillTag']), 'skillTag', 1)->num_rows == 0)
						{
							//ans = no so lets add this skill
							$this->database->insert('skill', $data);
							//redirect user back with success message
							header('Location: '.$this->utility->site_url('manage?msg=skill-success'));
						}
						else
						{
							//inform user that the tag already exists
							header('Location: '.$this->utility->site_url('manage?msg=skill-exists'));
						}
					}
					elseif($this->input->post('save'))
					{
						/*
						 we need to update the existing skill.
						 as well as relink areas to the new tag BUT only
						 if the slug has changed
						*/
						
						$this->database->update('skill', array('skillTag'=>$this->input->post('existing')), $data);
						
						if($this->input->post('existing') != $data['skillTag'])
						{
						   $this->database->update('areaSkill', array('skillTag'=>$this->input->post('existing')), array('skillTag'=>$data['skillTag']));
						}
					}
					else
					{
						//no action selected infrom user
						header('Location: '.$this->utility->site_url('manage?msg=skill-invalid-action'));
					}
				}
				elseif($switch[0] == 'delete')
				{
					//did the user confirm the delete?
					if($this->input->post('submit'))
					{
						//check token valid for security
						if($_SESSION['user']['token'] == $this->input->post('token'))
						{
							//check user password confirmed before delete
							$userPass = $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userPass', 1);
							if($userPass == $this->utility->hash_string($this->input->post('password'), $_SESSION['user']['username']))
							{
								//delete and redirect
								$this->database->delete('skill', array('skillTag'=>$this->input->get('skill')));
								$this->database->delete('areaSkill', array('skillTag'=>$this->input->get('skill')));
								
								header('Location: '.$this->utility->site_url('manage?msg=skill-delete-success'));
							}
							else
							{
								//redirect and inform invalid password
								header('Location: '.$this->utility->site_url('manage/tag/delete?msg=invalid&skill='.$this->input->get('skill')));
							}
						}
						else
						{
							//assume faul play and respond in kind
							
						}
					}
					//nope lets ask them to confim action
					else
					{
						$this->view->load('backend/skill_delete');
					}
				}
				else
				{
					//something missing lets inform the user
					header('Location: '.$this->utility->site_url('manage?msg=tag-missing-field#skills'));
				}
			}
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
				$switch = explode('?', $urlSegment[7]);
				$switch = $switch[0];
				$switch = explode('/', $switch);
				
				//use a nice simple switch to load the correct view/function
				switch($switch[0])
				{
					case 'add': //if we need to add an area
						//check we are not processing a form
						if(!$this->input->post('submit'))
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
						//check we are not processing a form
						if(!$this->input->post('submit'))
						{
							//refresh session token for security
							$_SESSION['user']['token'] = uniqid(sha1(microtime()), true);
							
							//load information from database for dropdown menus
							$data['parents'] = $this->database->get('area', array('areaParentSlug'=>'root'), 'areaSlug, areaName');
							$data['timeRequirements'] = $this->database->get('timeRequirement', null, 'timeRequirementID, timeRequirementShortDescription');
							
							//load information on this area to populate form
							$queryResource = $this->database->query('SELECT areaSlug, areaName, areaURL, areaDescription, areaParentSlug, timeRequirementID FROM area WHERE areaSlug = '.$this->database->escape($switch[1]).'LIMIT 1');
							$data['area'] = mysql_fetch_object($queryResource);
							
							$queryResource = $this->database->query('SELECT skillName FROM areaSkill INNER JOIN skill ON areaSkill.skillTag = skill.skillTag WHERE areaSkill.areaSlug = '.$this->database->escape($switch[1]));
							while($skillName = mysql_fetch_object($queryResource))
							{
								$data['tags'][] = $skillName->skillName;
							}
							
							//load view
							$this->view->load('backend/area_edit', $data);
						}
						else
						{
							$this->area_edit();
						}
					break;
					default:
						//nope, not in existance, set http header to 404
						header('HTTP/1.0 404 Not Found');
						//load 404 error file
						include(dirname(__FILE__).'/../../asset/error/404.html');
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
			if($this->input->post('token') == $_SESSION['user']['token'])
			{
				//check that all required fields were submitted
				if(($this->input->post('name') == false)||
				   ($this->input->post('url') == false)||
				   ($this->input->post('time') == 'null'))
				{
					//something was not entered, inform the user
					header('Location: '.$this->utility->site_url('manage/area/add').'?invalid=missing');
				}
				//check that the slug is not used already
				elseif($this->database->get('area', array('areaSlug'=>$this->input->post('slug')), 'areaSlug', 1)->num_rows == 0)
				{
					//start getting ready to add data to the database
					$data = array(
						'areaURL' => $this->input->post('url'),
						'areaName' => $this->input->post('name'),
						'areaDescription' => $this->input->post('description'),
						'timeRequirementID' => $this->input->post('time'),
						'areaParentSlug' => $this->input->post('parent'),
					);
					
					//check slug not empty and in correct format
					if($this->input->post('slug') == false)
					{
						//index 'areaSlug' stores the area name in underscored form + unique id
						$data['areaSlug'] = implode('_', explode(' ', strtolower($this->input->post('name')))).'_'.uniqid();
					}
					else
					{
						$data['areaSlug'] = strtolower($this->input->post('slug'));
					}
					
					//add information to the database
					$this->database->insert('area', $data);
					
					/*
					 deal with tags now
					*/
					
					//prep $data for repurposing
					$areaSlug = $data['areaSlug'];
					unset($data);
					
					//split tags up
					$tags = explode(',', $this->input->post('tags'));
					//check the last tag is not empty (this happens from time to time)
					if($tags[count($tags)-1] == '')
					{
						//last tag is empty lets remove it
						unset($tags[count($tags)-1]);
					}
					
					//check if tags already exist
					foreach($tags as $tag)
					{
						$tag = trim($tag);
						//run a query so we can get the number of rows
						$queryResource = $this->database->query("SELECT skillTag FROM skill WHERE skillTag = ".$this->database->escape(strtolower($tag))." OR skillName = ".$this->database->escape($tag)." LIMIT 1");
						if($this->database->num_rows() == 1)
						{
							//get the skillTag out of the db and make link in areaSkill
							$skill = mysql_fetch_object($queryResource);
							
							//repurpose $data for the next save in the database
							$data = array(
								'areaSlug' => $areaSlug,
								'skillTag' => $skill->skillTag
							);
							
							//insert into database
							$this->database->insert('areaSkill', $data);
						}
						else
						{
							//tag does not exist, so we need to add it to the database
							$data = array(
								'skillTag' => implode('_', explode(' ', strtolower($tag))),
								'skillName' => $tag
							);
							$this->database->insert('skill', $data);
							
							//link area and new tag
							$data = array(
								'skillTag' => implode('_', explode(' ', strtolower($tag))),
								'areaSlug' => $areaSlug
							);
							$this->database->insert('areaSkill', $data);
						}
					}
					
					//all done return user to management page
					header('Location: '.$this->utility->site_url('manage'));
				}
				else
				{
					//slug taken
					header('Location: '.$this->utility->site_url('manage/area/add?invalid=slug'));
				}
			}
		}
		
		/**
		 * area_edit
		 *
		 * to reduce the size of the area function and for readability when a
		 * user edits an area of contribution this function will be called into
		 * action to do the processing.
		 *
		 * @access private
		 */
		private function area_edit()
		{
			if($this->input->post('token') == $_SESSION['user']['token'])
			{
				//check all required feilds submitted
				if(($this->input->post('name') == false)||
				   ($this->input->post('url') == false)||
				   ($this->input->post('time') == 'null'))
				{
					//something was not entered, inform the user
					header('Location: '.$this->utility->site_url('manage/area/add').'?invalid=missing');
				}
				elseif($this->input->post('area') == $this->input->post('slug'))
				{
					$data = array(
						'areaURL' => $this->input->post('url'),
						'areaName' => $this->input->post('name'),
						'areaDescription' => $this->input->post('description'),
						'timeRequirementID' => $this->input->post('time'),
						'areaParentSlug' => $this->input->post('parent'),
					);
					
					//update information in database
					$this->database->update('area', array('areaSlug'=>$this->input->post('area')), $data);
					
					/*
					 now deal with tags
					*/
					
					//prep $data for repurposing
					unset($data);
					
					//split tags up
					$tags = explode(',', $this->input->post('tags'));
					//check the last tag is not empty (this happens from time to time)
					if($tags[count($tags)-1] == '')
					{
						//last tag is empty lets remove it
						unset($tags[count($tags)-1]);
					}
					
					//check if tags already exist
					foreach($tags as $tag)
					{
						$tag = trim($tag);
						//run a query so we can get the number of rows
						$queryResource = $this->database->query("SELECT skillTag FROM skill WHERE skillTag = ".$this->database->escape(strtolower($tag))." OR skillName = ".$this->database->escape($tag)." LIMIT 1");
						if($this->database->num_rows() == 1)
						{
							//get the skillTag out of the db and make link in areaSkill
							$skill = mysql_fetch_object($queryResource);
							
							//repurpose $data for the next save in the database
							$data = array(
								'areaSlug' => $this->input->post('area'),
								'skillTag' => $skill->skillTag
							);
							
							//insert into database
							$this->database->insert('areaSkill', $data);
						}
						else
						{
							//tag does not exist, so we need to add it to the database
							$data = array(
								'skillTag' => implode('_', explode(' ', strtolower($tag))),
								'skillName' => $tag
							);
							$this->database->insert('skill', $data);
							
							//link area and new tag
							$data = array(
								'skillTag' => implode('_', explode(' ', strtolower($tag))),
								'areaSlug' => $this->input->post('area')
							);
							$this->database->insert('areaSkill', $data);
						}
					}
					
					//all done return user to management page
					header('Location: '.$this->utility->site_url('manage'));
				}
				else
				{
					//slug was changed, lets create new entry and remove old
				}
			}
		}
	}
	
?>