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
				
				$data['skills'] = $this->database->get('skill', null, '*', null, 'skillName', 'asc')->results;
				$data['parents'] = $this->database->get('area', array('areaParentSlug'=>'root'), 'areaSlug, areaName');
				$data['timeRequirements'] = $this->database->get('timeRequirement', null, 'timeRequirementID, timeRequirementShortDescription');
				
				$this->view->load('backend/manage_index', $data);
			}
		}
		
		/**
		 * skill
		 *
		 * This function handles editing/deleting/creating skills
		 */
		function skill()
		{
			if($this->utility->check_auth()&&($this->input->post('token') == $_SESSION['user']['token']))
			{
				if($this->input->post('add') != false)
				{
					//add an skill
					
					//check all needed fields submitted
					if(($this->input->post('name') != false)&&($this->input->post('slug') != false))
					{
						//create data array to put into the insert method for the database
						$data = array(
							'skillTag' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($this->input->post('slug')))),
							'skillName' => $this->input->post('name')
						);
						
						//check that the skill not yet in existance
						if($this->database->get('skill', array('skillTag'=>$data['skillTag']), 'skillTag', 1)->num_rows == 0)
						{
							//no existing skills matching found insert skill
							$this->database->insert('skill', $data);
							//redirect with success msg
							header('Location: '.$this->utility->site_url('manage?msg=skill-added'));
						}
						else
						{
							//skill exists warn user
							header('Location: '.$this->utility->site_url('manage?msg=skill-exists'));
						}
					}
					else
					{
						header('Location: '.$this->utility->site_url('manage?msg=skill-missing'));
					}
				}
				elseif($this->input->post('edit') != false)
				{
					//edit an skill
					
					//check all needed fields entered
					if(($this->input->post('name') != false)&&($this->input->post('slug') != false))
					{
						//create an array of new data
						$data = array(
							'skillTag' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($this->input->post('slug')))),
							'skillName' => $this->input->post('name')
						);
						
						//check that slug does not clash if has been changed
						if(($data['skillTag'] == $this->input->post('edit'))||($this->database->get('skill', array('skillTag'=>$data['skillTag']), 'skillTag', 1)->num_rows == 0))
						{
							//no clash or tag unchanged do the update
							$this->database->update('skill', array('skillTag'=>$this->input->post('edit')), $data);
							$this->database->update('areaSkill', array('skillTag'=>$this->input->post('edit')), array('skillTag'=>$data['skillTag']));
							//redirect with success msg
							header('Location: '.$this->utility->site_url('manage?msg=skill-saved'));
						}
						else
						{
							//there is a clash between edit and other skill inform user
							header('Location: '.$this->utility->site_url('manage?msg=skill-exists'));
						}
					}
					else
					{
						header('Location: '.$this->utility->site_url('manage?msg=skill-missing'));
					}
				}
				elseif($this->input->post('delete') != false)
				{
					//delete an skill
					
					//check the password is correct then delete
					if($this->utility->hash_string($this->input->post('password'), $_SESSION['user']['username']) == $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userPassword', 1)->results[0]->userPassword)
					{
						//valid password delete and redirect back with msg
						$this->database->delete('skill', array('skillTag'=>$this->input->post('delete')));
						$this->database->delete('areaSkill', array('skillTag'=>$this->input->post('delete')));
						header('Location: '.$this->utility->site_url('manage?msg=skill-deleted'));
					}
					else
					{
						//invalid password inform user
						header('Location: '.$this->utility->site_url('manage?msg=invalid-password'));
					}
				}
				else
				{
					//direct access = bad
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
			if($this->utility->check_auth()&&($this->input->post('token') == $_SESSION['user']['token']))
			{
				if($this->input->post('add') != false)
				{
					//add an area
					
					//check all needed fields submitted
					if(($this->input->post('name') != false)&&
					   ($this->input->post('slug') != false)&&
					   ($this->input->post('url') != false)&&
					   ($this->input->post('time') != 'null'))
					{
						//create data array to put into the insert method for the database
						$data = array(
							'areaSlug' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($this->input->post('slug')))),
							'areaName' => $this->input->post('name'),
							'areaURL' => $this->input->post('url'),
							'areaDescription' => $this->input->post('description'),
							'areaParentSlug' => $this->input->post('parent'),
							'timeRequirementID' => $this->input->post('time')
						);
						
						//check that the area not yet in existance
						if($this->database->get('area', array('areaSlug'=>$data['areaSlug']), 'areaSlug', 1)->num_rows == 0)
						{
							//no existing areas matching found insert area
							$this->database->insert('area', $data);
							
							/*
							 deal with skills
							*/
							//keep areaslug available
							$areaSlug = $data['areaSlug'];
							
							//unset $data for reuse
							unset($data);
							
							//check if skills were entered
							if($this->input->post('tags') != false)
							{
								//split tags up
								$skills = explode(',', $this->input->post('tags'));
								
								//clear whitespace and remove empty tags
								$cleanSkills = array();
								for($i = 0; $i < count($tags); $i++)
								{
									if(trim($skills[$i]) != '')
									{
										$cleanSkills[] = trim($skills[$i]);
									}
								}
								$skills = $cleanSkills;
								
								//loop through tags and link them to the area
								foreach($skills as $skill)
								{
									//check if skill exists
									$queryResource = $this->database->query("SELECT skillTag FROM skill WHERE skillTag = ".$this->database->escape(strtolower($skill))." OR skillName = ".$this->database->escape($skill)." LIMIT 1");
									$existingSkill = mysql_fetch_object($queryResource);
									
									if($this->database->num_rows() == 0)
									{
										//skill not matched so lets create it
										$data = array(
											'skillTag' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($skill))),
											'skillName' => $skill
										);
										$this->database->insert('skill', $data);
										
										//make link
										$this->database->insert('areaSkill', array('areaSlug'=>$areaSlug, 'skillTag'=>$data['skillTag']));
									}
									else
									{
										//skill found so just link it
										$this->database->insert('areaSkill', array('areaSlug'=>$areaSlug, 'skillTag'=>$existingSkill->skillTag));
									}
								}
							}
							
							//redirect with success msg
							header('Location: '.$this->utility->site_url('manage?msg=area-added'));
						}
						else
						{
							//skill exists warn user
							header('Location: '.$this->utility->site_url('manage?msg=area-exists'));
						}
					}
					else
					{
						header('Location: '.$this->utility->site_url('manage?msg=area-missing'));
					}
				}
				elseif($this->input->post('edit') != false)
				{
					//edit an area
					
					//check all needed fields submitted
					if(($this->input->post('name') != false)&&
					   ($this->input->post('slug') != false)&&
					   ($this->input->post('url') != false)&&
					   ($this->input->post('time') != 'null'))
					{
						//create data array to put into the insert method for the database
						$data = array(
							'areaSlug' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($this->input->post('slug')))),
							'areaName' => $this->input->post('name'),
							'areaURL' => $this->input->post('url'),
							'areaDescription' => $this->input->post('description'),
							'areaParentSlug' => $this->input->post('parent'),
							'timeRequirementID' => $this->input->post('time')
						);
						
						//check that the area not yet in existance OR that slug is unchanged
						if(($this->input->post('edit') == $data['areaSlug'])||($this->database->get('area', array('areaSlug'=>$data['areaSlug']), 'areaSlug', 1)->num_rows == 0))
						{
							//no existing areas matching found update area
							$this->database->update('area', array('areaSlug'=>$this->input->post('edit')), $data);
							
							/*
							 deal with skills
							*/
							//as this is an update for ease lets just delete all links to the old version and add for the new
							$this->database->delete('areaSkill', array('areaSlug'=>$this->input->post('edit')));
							
							//keep areaslug available
							$areaSlug = $data['areaSlug'];
							
							//unset $data for reuse
							unset($data);
							
							//check if skills were entered
							if($this->input->post('tags') != false)
							{
								//split tags up
								$skills = explode(',', $this->input->post('tags'));
								
								//clear whitespace and remove empty tags
								$cleanSkills = array();
								for($i = 0; $i < count($skills); $i++)
								{
									if(trim($skills[$i]) != '')
									{
										$cleanSkills[] = trim($skills[$i]);
									}
								}
								$skills = $cleanSkills;
								
								//loop through tags and link them to the area
								foreach($skills as $skill)
								{
									//check if skill exists
									$queryResource = $this->database->query("SELECT skillTag FROM skill WHERE skillTag = ".$this->database->escape(strtolower($skill))." OR skillName = ".$this->database->escape($skill)." LIMIT 1");
									$existingSkill = mysql_fetch_object($queryResource);
									
									if($this->database->num_rows() == 0)
									{
										//skill not matched so lets create it
										$data = array(
											'skillTag' => preg_replace('/[^a-z0-9_\-]/', '', str_replace(' ', '_', strtolower($skill))),
											'skillName' => $skill
										);
										$this->database->insert('skill', $data);
										
										//make link
										$this->database->insert('areaSkill', array('areaSlug'=>$areaSlug, 'skillTag'=>$data['skillTag']));
									}
									else
									{
										//skill found so just link it
										$this->database->insert('areaSkill', array('areaSlug'=>$areaSlug, 'skillTag'=>$existingSkill->skillTag));
									}
								}
							}
							
							//redirect with success msg
							header('Location: '.$this->utility->site_url('manage?msg=area-saved'));
						}
						else
						{
							//skill exists warn user
							header('Location: '.$this->utility->site_url('manage?msg=area-exists'));
						}
					}
					else
					{
						header('Location: '.$this->utility->site_url('manage?msg=area-missing'));
					}
				}
				elseif($this->input->post('delete') != false)
				{
					//delete an area
					
					//check the password is correct then delete
					if($this->utility->hash_string($this->input->post('password'), $_SESSION['user']['username']) == $this->database->get('user', array('username'=>$_SESSION['user']['username']), 'userPassword', 1)->results[0]->userPassword)
					{
						//valid password delete and redirect back with msg
						$this->database->delete('area', array('areaSlug'=>$this->input->post('delete')));
						$this->database->delete('areaSkill', array('areaSlug'=>$this->input->post('delete')));
						header('Location: '.$this->utility->site_url('manage?msg=area-deleted'));
					}
					else
					{
						//invalid password inform user
						header('Location: '.$this->utility->site_url('manage?msg=invalid-password'));
					}
				}
				else
				{
					//direct access = bad
				}
			}
		}
	}
	
?>