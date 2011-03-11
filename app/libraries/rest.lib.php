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
			'timeID' => 'timeRequirementID',
			'timeShort' => 'timeRequirementShortDescription',
			'timeLong' => 'timeRequirementLongDescription',
			'tag' => array(
				'slug' => 'skillTag',
				'name' => 'skillName'
			)
		);
		
		function GET($args)
		{
			//set the format based on url OR fall back to json
			$this->format = ((isset($args['format']))&&$args['format'] != null)?$args['format']:'json';
			$method = 'get_'.(((isset($args['method']))&&$args['method'] != null)?$args['method']:null);
			
			if(method_exists('Rest', $method))
			{
				$this->$method($args);
				switch($this->format)
				{
					case 'xml':
						//
					break;
					case 'json':
						$this->respond(200, json_encode($this->data), 'application/json');
					break;
					default:
						//do nothing
					break;
				}
			}
			else
			{
				//send bad request msg
			}
			
			//debug
			//print_r($args);
			//print_r($_GET);
		}
		
		private function respond($status, $data, $mime)
		{
			header('HTTP/1.0 '.$status);
			header('Content-type: '.$mime);
			echo $data;
		}
		
		private function get_area($args)
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
				if(isset($cleanConstraints))
				{
					foreach($cleanConstraints as $field => $value)
					{
						if(preg_match('/area/i', $field))
						{
							$field = 'area.'.$field;
						}
						$where[] = $field.' = '.$this->database->escape($value);
					}
					$constraints = implode(' AND ', $where);
				}
				else
				{
					$constraints = '';
				}
				
				if(isset($tags))
				{
					foreach($tags as $tag)
					{
						$tagsf[] = 'skill.skillTag LIKE '.$this->database->escape($tag).' OR skill.skillName LIKE '.$this->database->escape($tag);
					}
					$tags = implode(' OR ', $tagsf);
				}
				else
				{
					$tags = '';
				}
				
				//run query
				$query = $this->database->query('SELECT area.areaSlug FROM area'.(($tags != '')?' INNER JOIN areaSkill ON areaSkill.areaSlug = area.areaSlug INNER JOIN skill ON areaSkill.skillTag = skill.skillTag':null).((($constraints != '')||($tags != ''))?' WHERE ':'').(($constraints != '')?$constraints.(($tags != '')?' AND (':null):null).(($tags != '')?$tags.')':null));
				
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
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$alias['tag'] = array_flip($this->alias['tag']);
				foreach($slugs as $slug)
				{
					$query = $this->database->query('SELECT * FROM area INNER JOIN timeRequirement ON timeRequirement.timeRequirementID = area.timeRequirementID WHERE area.areaSlug = '.$this->database->escape($slug).' LIMIT 1');
					$tmp = mysql_fetch_array($query);
					
					foreach($tmp as $key => $value)
					{
						//swap out keys into $area
						if(!is_numeric($key))
						{
							$area[$alias[$key]] = $value;
						}
					}
					$area['tag'] = array();
					//get tags
					$query = $this->database->query('SELECT * FROM areaSkill INNER JOIN skill ON areaSkill.skillTag = skill.skillTag WHERE areaSkill.areaSlug = '.$this->database->escape($slug));
					$tag = array();
					while($tmp = mysql_fetch_array($query))
					{
						foreach($tmp as $key => $value)
						{
							if(!is_numeric($key) && ($key != 'areaSlug'))
							{
								$tag[$alias['tag'][$key]] = $value;
							}
						}
						array_push($area['tag'], $tag);
					}
					
					array_push($data, $area);
				}
				$this->data = $data;
			}
			else
			{
				/*
				 list all areas as none specified
				*/
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$alias['tag'] = array_flip($this->alias['tag']);
				$areaTmp = array();
				$data = array();
				//get areas
				$query = $this->database->query('SELECT * FROM area INNER JOIN timeRequirement ON timeRequirement.timeRequirementID = area.timeRequirementID');
				while($area = mysql_fetch_array($query))
				{
					foreach($area as $key => $value)
					{
						//swap out keys into $area
						if(!is_numeric($key))
						{
							$areaTmp[$alias[$key]] = $value;
						}
					}
					$area = $areaTmp;
					$area['tag'] = array();
					
					//get tags
					$tagQuery = $this->database->query('SELECT * FROM areaSkill INNER JOIN skill ON areaSkill.skillTag = skill.skillTag WHERE areaSkill.areaSlug = '.$this->database->escape($area['slug']));
					$tag = array();
					while($tmp = mysql_fetch_array($tagQuery))
					{
						foreach($tmp as $key => $value)
						{
							if(!is_numeric($key) && ($key != 'areaSlug'))
							{
								$tag[$alias['tag'][$key]] = $value;
							}
						}
						array_push($area['tag'], $tag);
					}
					
					array_push($data, $area);
				}
				
				$this->data = $data;
			}
		}
	}
	
?>