<?php
	
	/**
	 * API Controller
	 *
	 * This file contains the processing behind the system API which is internal
	 * and external facing.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage controllers
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Api
	 *
	 * This class controls the processing behind the JSON API
	 */
	class Api extends Controller
	{
		function area()
		{
			/*
			 get parent areas of contribution
			*/
			if($this->input->get('slug') != false)
			{
				$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, timeRequirementShortDescription, areaParentSlug FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID WHERE area.areaSlug = ".$this->database->escape($this->input->get('slug')));
			}
			else
			{
				$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, timeRequirementShortDescription, areaParentSlug FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID WHERE area.areaParentSlug = 'root'");
			}
			
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
				if($this->input->get('slug') != false)
				{
					$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, areaDescription, timeRequirementShortDescription, areaParentSlug FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID  WHERE area.areaParentSlug = ".$this->database->escape($this->input->get('slug')));
				}
				else
				{
					$queryResource = $this->database->query("SELECT areaSlug, areaName, areaURL, areaDescription, timeRequirementShortDescription, areaParentSlug FROM area INNER JOIN timeRequirement ON area.timeRequirementID = timeRequirement.timeRequirementID  WHERE area.areaParentSlug != 'root'");
				}
				
				$query['children'] = array();
				while($queryChildren = mysql_fetch_object($queryResource))
				{
					$query['children'][] = $queryChildren;
				}
				/*
				 mash sets together
				*/
				foreach($query['parents'] as $parent)
				{
					$parentSlug = $parent->areaSlug;
					foreach($query['children'] as $child)
					{
						if($child->areaParentSlug == $parent->areaSlug)
						{
							$parent->children[] = $child;
						}
					}
					$data['area'][] = $parent;
				}
			}
			
			echo json_encode($data);
		}
	}
	
?>