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
			'area' => array(
				'slug' => 'areaSlug',
				'parent' => 'areaParentSlug',
				'url' => 'areaURL',
				'description' => 'areaDescription',
				'name' => 'areaName',
				'time' => 'timeRequirementID'
			)
		);
		
		function GET($args)
		{
			//set the format based on url OR fall back to json
			$this->format = ((isset($args['format']))&&$args['controller'] != null)?$args['controller']:'json';
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
			$opts = explode('&', substr(urldecode($args['query_string']), 1));
			foreach($opts as $opt)
			{
				$opt = explode('=', $opt, 2);
				if(($this->input->clean_key($opt[0]) != 'tag')&&(array_key_exists($this->input->clean_key($opt[0]), $this->alias['area'])))
				{
					$constraints[$this->alias['area'][$this->input->clean_key($opt[0])]] = $this->input->get($opt[0]);
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
			$where = '';
			//construct where clause
			foreach($constraints as $field => $value)
			{
				$where[] = "$field = ".$this->database->escape($value);
			}
			$where = print_r(implode(' AND ', $where).((isset($tagClause))?' AND '.$tagClause:null));
			
			/*
			 get area(s) of contribution
			*/
		}
	}
	
?>