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
		
		/**
		 * index
		 *
		 * Loads the needed information for the main frontend user interface.
		 */
		function index()
		{
			$data['timeRequirements'] = $this->database->get('timeRequirement', null, 'timeRequirementID, timeRequirementShortDescription');
			$this->view->load('frontend/index', $data);
		}
		
		/**
		 * out
		 *
		 * Grabs all the information needed for the metrics and then fwds the
		 * user off to the correct website
		 */
		function out($args)
		{
			//get the date for the metirc
			$metric['metricDate'] = date('Y-m-d', time());
			//get the area slug for metric
			$areaSlug = preg_split('/\/h/i', $args[6], 2);
			$metric['areaSlug'] = preg_replace('/\//', '', $areaSlug[0]);
			
			//set metriclocale data to match metric data as they are identical till this point
			$metriclocale = $metric;
			
			//check if we only need to update a row or insert one for metric
			$metricchecks = $this->database->get('metric', $metric, 'metricDate', 1);
			if($metricchecks->num_rows == 1)
			{
				/*
				 update metric
				*/
				//get the metric qty
				$metric['metricQty'] = $metricchecks->results[0]->metricQty + 1;
				
				//update table
				$this->database->update('metric', array('metricDate'=>$metric['metricDate'],'areaSlug'=>$metric['areaSlug']), $metric);
			}
			else
			{
				/*
				 add metric
				*/
				$metric['metricQty'] = 1;
				$this->database->insert('metric', $metric);
			}
			
			//get the localeID from the http headers
			preg_match('/[a-z]{2}(\-[a-z]{1,2})?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $localeID);
			$metriclocale['localeID'] = strtolower($localeID[0]);
			
			//check if we need to update metric locale or just insert a new row
			$metriclocalechecks = $this->database->get('metricLocale', $metriclocale, 'metricDate', 1);
			if($metriclocalechecks->num_rows == 1)
			{
				/*
				 update metriclocale
				*/
				//get the metriclocale qty
				$metriclocale['metricLocaleQty'] = $metriclocalechecks->results[0]->metricLocaleQty + 1;
				
				//update the table
				$this->database->update('metricLocale', array('metricDate'=>$metriclocale['metricDate'],'areaSlug'=>$metriclocale['areaSlug']), $metriclocale);
			}
			else
			{
				/*
				 insert new metriclocale
				*/
				$metriclocale['metricLocaleQty'] = 1;
				$this->database->insert('metricLocale', $metriclocale);
			}
			
			/*
			 get url to fwd to
			*/
			//split off url from current page url
			$url = preg_split('/[a-z0-9_\-]\//i', $args[6], 2);
			//check url is actually set
			if(count($url) > 1)
			{
				//it is lets do our redirect
				$url = $url[1];
				header('Location: '.$url);
			}
			else
			{
				//its not set lets redirect to an error page
			}
		}
		
	}
	
?>