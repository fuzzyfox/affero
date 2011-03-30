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
			'date' => 'metricDate',
			'qty' => 'metricQty',
			'locale' => 'localeID',
			'localeQty' => 'metricLocaleQty',
			'tag' => array(
				'slug' => 'skillTag',
				'name' => 'skillName'
			)
		);
		
		/**
		 * GET
		 * 
		 * Responds to all get requests to the api and routes calls off to the
		 * relevant functions to produce a response. It is also in charge of
		 * formatting the response into either XML or JSON.
		 * 
		 * @param array $args the aguments supplied by gluephp about the url
		 */
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
		
		/**
		 * respond
		 * 
		 * This is used to send the response by setting the headers, and echoing
		 * the content to the calling body.
		 *
		 * @param int $status the relevant http status code
		 * @param string $data the data to respond with
		 * @param string $mime the mime type of the content we are responding with
		 */
		private function respond($status, $data, $mime)
		{
			header('HTTP/1.0 '.$status);
			header('Content-type: '.$mime);
			echo $data;
		}
		
		/**
		 * get_area
		 *
		 * Spits out all the data relating to areas based on constraints passed
		 * via the url IF required.
		 *
		 * @param array $args the aguments supplied by gluephp about the url
		 */
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
				$query = $this->database->query('SELECT area.areaSlug FROM area'.(($tags != '')?' INNER JOIN areaSkill ON areaSkill.areaSlug = area.areaSlug INNER JOIN skill ON areaSkill.skillTag = skill.skillTag':null).((($constraints != '')||($tags != ''))?' WHERE ':'').(($constraints != '')?$constraints.(($tags != '')?' AND (':null):null).(($tags != '')?$tags.(($constraints != '')?(($tags != '')?')':null):null):null));
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
		
		/**
		 * get_time
		 *
		 * Responds with data about time requirements based on constraints passed
		 * via the url IF required.
		 *
		 * @param array $args the aguments supplied by gluephp about the url
		 */
		private function get_time($args)
		{
			if(isset($args['query_string'])&&($args['query_string'] != null))
			{
				$constraints = explode('&', substr(urldecode($args['query_string']), 1));
				foreach($constraints as $constraint)
				{
					$constraint = explode('=', $constraint, 2);
					if(array_key_exists($this->input->clean_key($constraint[0]), $this->alias))
					{
						$cleanConstraints[$this->alias[$this->input->clean_key($constraint[0])]] = $this->input->get($constraint[0]);
					}
					else
					{
						header('HTTP/1.0 400 Bad Request');
						die('Bad Request');
					}
				}
				
				$times = (array)$this->database->get('timeRequirement', $cleanConstraints)->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
			else
			{
				$times = (array)$this->database->get('timeRequirement')->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
		}
		
		/**
		 * get_metric
		 *
		 * Gets the metrics data and exposes it for the read only api
		 *
		 * @param array $args the aguments supplied by gluephp about the url
		 */
		private function get_metric($args)
		{
			
			if(isset($args['query_string'])&&($args['query_string'] != null))
			{
				$constraints = explode('&', substr(urldecode($args['query_string']), 1));
				foreach($constraints as $constraint)
				{
					$constraint = explode('=', $constraint, 2);
					if(array_key_exists($this->input->clean_key($constraint[0]), $this->alias))
					{
						$cleanConstraints[$this->alias[$this->input->clean_key($constraint[0])]] = $this->input->get($constraint[0]);
					}
					else
					{
						header('HTTP/1.0 400 Bad Request');
						die('Bad Request');
					}
				}
				
				$times = (array)$this->database->get('metric', $cleanConstraints)->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
			else
			{
				$times = (array)$this->database->get('metric')->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
		}
		
		/**
		 * get_locale
		 *
		 * Gets the locale metric data and exposes it for the read only api
		 *
		 * @param array $args the aguments supplied by gluephp about the url
		 */
		private function get_locale($args)
		{
			
			if(isset($args['query_string'])&&($args['query_string'] != null))
			{
				$constraints = explode('&', substr(urldecode($args['query_string']), 1));
				foreach($constraints as $constraint)
				{
					$constraint = explode('=', $constraint, 2);
					if(array_key_exists($this->input->clean_key($constraint[0]), $this->alias))
					{
						$cleanConstraints[$this->alias[$this->input->clean_key($constraint[0])]] = $this->input->get($constraint[0]);
					}
					else
					{
						header('HTTP/1.0 400 Bad Request');
						die('Bad Request');
					}
				}
				
				$times = (array)$this->database->get('metricLocale', $cleanConstraints)->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
			else
			{
				$times = (array)$this->database->get('metricLocale')->results;
				
				$alias = $this->alias;
				unset($alias['tag']);
				$alias = array_flip($alias);
				$tmp = array();
				$data = array();
				
				foreach($times as $time)
				{
					foreach($time as $key => $value)
					{
						$tmp[$alias[$key]] = $value;
					}
					array_push($data, $tmp);
				}
				
				$this->data = $data;
			}
		}
	}
	
?>