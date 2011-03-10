<?php
	
	/**
	 * Rest
	 *
	 * This file contains a collection of helper functions for the api
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Rest
	 *
	 * A collection of helper functions to make working with the api and extending
	 * it quicker and easier
	 */
	class Rest extends Controller
	{
		//the format in which to respond
		protected $format;
		//the data to respond with
		protected $data;
		//alias for database columns
		protected $alias = array(
			'slug' => 'areaSlug',
			'parent' => 'areaParentSlug',
			'url' => 'areaURL',
			'description' => 'areaDescription',
			'name' => 'areaName',
			'time' => 'timeRequirementID',
			'timeShort' => 'timeRequirementShortDescription',
			'timeLong' => 'timeRequirementLongDescription',
			'tag' => 'skillTag',
			'tagName' => 'skillName'
		);
		
		function GET($args)
		{
			//set the format based on url OR fall back to json
			$this->format = ((isset($args['format']))&&$args['format'] != null)?$args['format']:'json';
			$method = 'get_'.(((isset($args['method']))&&$args['method'] != null)?$args['method']:null);
			
			if(method_exists('Rest', $method))
			{
				$response = $this->$method($args);
			}
			else
			{
				//send bad request msg
			}
			
			//debug
			//print_r($args);
			//print_r($_GET);
		}
		
		private function get_area($args)
		{
			/*
			 get constraints
			*/
			$constraints = array();
			$where = '';
			if(isset($args['query_string']))
			{
				$opts = explode('&', substr(urldecode($args['query_string']), 1));
				foreach($opts as $opt)
				{
					$opt = explode('=', $opt, 2);
					if(($this->input->clean_key($opt[0]) != 'tag')&&(array_key_exists($this->input->clean_key($opt[0]), $this->alias)))
					{
						$constraints[$this->alias[$this->input->clean_key($opt[0])]] = $this->input->get($opt[0]);
					}
					elseif($this->input->clean_key($opt[0]) == 'tag')
					{
						$tags = explode('|', $this->input->get($opt[0]));
						foreach($tags as $tag)
						{
							$tagClause['skillTag'][] = $tag;
							$tagClause['skillName'][] = $tag;
						}
						foreach($tagClause['skillTag'] as $skillTag)
						{
							$tagClause[] = 'skillTag LIKE '.$this->database->escape($skillTag);
						}
						unset($tagClause['skillTag']);
						foreach($tagClause['skillName'] as $skillTag)
						{
							$tagClause[] = 'skillName LIKE '.$this->database->escape($skillTag);
						}
						unset($tagClause['skillName']);
						$tagClause = implode(' OR ', $tagClause);
					}
					else
					{
						header('HTTP/1.0 400 Bad Request');
						die('Bad Request');
					}
				}
				//construct where clause
				foreach($constraints as $field => $value)
				{
					$whereParts[] = "$field = ".$this->database->escape($value);
				}
				
				if(is_array($whereParts))
				{
					$where .= implode(' AND ', $whereParts);
				}
				
				if(isset($tagClause))
				{
					$where .= ((is_array($whereParts))?' AND ':null).$tagClause;
				}
			}
			
			$query = 'SELECT area.areaSlug FROM area INNER JOIN areaSkill ON area.areaSlug = areaSkill.areaSlug INNER JOIN skill ON skill.skillTag = areaSkill.skillTag';
			
			if($where == '')
			{
				$queryResource = $this->database->query($query);
			}
			else
			{
				$queryResource = $this->database->query($query.' WHERE '.$where);
			}
			
			/*
			 get area(s) of contribution
			*/
			$alias = array_flip($this->alias);
			while($area = mysql_fetch_array($queryResource))
			{
				foreach($area as $key => $value)
				{
					if(array_key_exists($key, $alias))
					{
						$data[$alias[$key]] = $value;
					}
				}
				$this->data[] = (object)$data;
			}
			
			echo json_encode((object)$this->data);
		}
		
		
		private function get_test($args)
		{
			/*
			 get constraints
			*/
			if(isset($args['query_string'])&&($args['query_string'] != null))
			{
				$constraints = explode('&', substr(urldecode($args['query_string']), 1));
				foreach($constraints as $constraint)
				{
					$constraint = explode('=', $constraint, 2);
					if(($this->input->clean_key($constraint[0]) != 'tag')&&(array_key_exists($this->input->clean_key($constraint[0]), $this->alias)))
					{
						$cleanConstraints[$this->alias[$this->input->clean_key($constraint[0])]] = $this->input->get($constraint[0]);
					}
					elseif($this->input->clean_key($constraint[0]) == 'tag')
					{
						$tags = explode('|', $this->input->get($constraint[0]));
					}
					else
					{
						header('HTTP/1.0 400 Bad Request');
						die('Bad Request');
					}
				}
				/*
				 generate query string
				*/
				foreach($cleanConstraints as $field => $value)
				{
					if(preg_match('/area/i', $field))
					{
						$field = 'area.'.$field;
					}
					$where[] = $field.' = '.$this->database->escape($value);
				}
				$constraints = implode(' AND ', $where);
				
				foreach($tags as $tag)
				{
					$tagsf[] = 'skill.skillTag LIKE '.$this->database->escape($tag).' OR skill.skillName LIKE '.$this->database->escape($tag);
				}
				$tags = implode(' OR ', $tagsf);
				
				//run query
				$query = $this->database->query('SELECT area.areaSlug FROM area INNER JOIN areaSkill ON areaSkill.areaSlug = area.areaSlug INNER JOIN skill ON areaSkill.skillTag = skill.skillTag WHERE '.$constraints.' OR '.$tags);
				
				//get slugs
				$slugs = array();
				while($area = mysql_fetch_array($query))
				{
					if(!in_array($area['areaSlug'], $slugs))
					{
						array_push($slugs, $area['areaSlug']);
					}
				}
				
				/*
				 get area(s) data
				*/
				$data = array();
				foreach($slugs as $slug)
				{
					$query = $this->database->query('SELECT * FROM area INNER JOIN timeRequirement ON timeRequirement.timeRequirementID = area.timeRequirementID WHERE area.areaSlug = '.$this->database->escape($slug).' LIMIT 1');
					$tmp = mysql_fetch_array($query);
					foreach($tmp as $key => $value)
					{
						//swap out keys into $area
					}
					//get tags
					//cast area and tags to objects
					//add area and tags to data
				}
				print_r($data);
			}
			
			/*
			 
			*/
		}
	}
	
?>