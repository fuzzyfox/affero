<?php
	
	/**
	 * Database Abstraction Layer
	 *
	 * This file contains all the code needed for interacting with the database 
	 * directly and provides tools to make life simpler and make the rest of the 
	 * coding quicker.
	 *
	 * @author William Duyck <wduyck@gmail.com>
	 * @version 0.1
	 * @package affero
	 * @subpackage libraries
	 * @copyright 2011 William Duyck
	 * @license MPL 1.1/LGPL 2.1/GPL 2.0
	 */
	
	/**
	 * Database
	 *
	 * Provides an easy way to run simple database queries with good
	 * optimisation and a reduction on database overhead caused by deletion
	 * of rows.
	 */
	class Database
	{
		//connection handler
		private $_handler;
        //resources returned by queries
		private $_result;
		
        /**
         * __construct
         *
         * Provides an alias for initilizing the class and connecting to a
         * database straight away.
         *
         * @param string $host host server for the database
		 * @param string $database the database name to connect to
		 * @param string $username username to connect to the database with
		 * @param string $password the password for the database
		 *
		 * @return mixed returns true on successful connection at initilization,
		 * false on failed connection at initilization, void if alias not utilized
         */
        function __construct($host = null, $database = null, $username = null, $password = null)
        {
            if($host !== null)
            {
                return $this->connect($host, $database, $username, $password);
            }
        }
        
		/**
         * connect
         *
		 * Connects to a database.
		 *
		 * @param string $host host server for the database
		 * @param string $database the database name to connect to
		 * @param string $username username to connect to the database with
		 * @param string $password the password for the database
		 *
		 * @return bool returns true on success
		 */
		function connect($host, $database, $username, $password)
		{
			//make the connection to the mysql server
			$this->_handler = mysql_connect($host, $username, $password);
			//check if connected
			if($this->_handler != false)
			{
				//attemp to connect to the database and check if successfull
				if(mysql_select_db($database, $this->_handler))
				{
					//connection to the database was made
					return true;
				}
			}
			
			//seems we couldn't connect, lets return false
			return false;
		}
		
		/**
         * disconnect
         * 
		 * Closes the connection to the active database
		 *
		 * @return bool true on success
		 */
		function disconnect()
		{
			return mysql_close($this->_handler);
		}
		
		/**
         * query
         * 
		 * Run a query on the database
		 *
		 * @param string $queryString the sql query to run on the database
		 * @return resource on success a mysql resource is returned, else false
		 */
		function query($queryString)
		{
			$this->_result = mysql_query($queryString, $this->_handler);
			return $this->_result;
		}
		
		/**
         * free_result
         * 
		 * Frees all allocated memory from the last query
		 *
		 * @return bool returns true if successfull
		 */
		function free_result()
		{
			return mysql_free_result($this->_result);
		}
		
		/**
         * get_error
         * 
		 * Gets the mysql error and formats it into a string containing the
		 * error number and error description
		 *
		 * @return string [{error number}] {error string}
		 */
		function get_error()
		{
			return '['.mysql_errno($this->_handler). '] '.mysql_error($this->_handler);
		}
		
		/**
         * escape
         * 
		 * Smart escape string. Escapes data based on type.
		 *
		 * @param string $string what we want to escape
		 * @return mixed an escaped version of the string
		 */
		function escape($string)
		{
			if(is_string($string))
			{
				return "'".addslashes($string)."'";
			}
			else
			{
				return $string;
			}
		}
		
		/**
         * insert
         * 
		 * Insert a record into the database
		 *
		 * @param string $table the table to insert the record to
		 * @param assoc_array $data the data to insert, using an associative array
		 * @return bool returns true on success
		 */
		function insert($table, $data)
		{
			//clean the data for use in the query string
			foreach(array_values($data) as $value)
			{
				$values[] = $this->escape($value);
			}
			
			//construct the query string
			$queryString = "INSERT INTO $table (".implode(', ', array_keys($data)).") VALUES (".implode(', ', $values).");";
			
			//run the query and return the result
			return $this->query($queryString);
		}
		
		/**
         * update
         * 
		 * Update record(s) in the database
		 *
		 * @param string $table the table to update record(s) from
		 * @param assoc_array $constraints the constraints on what to update
		 * @param assoc_array $data the new data for the record(s)
		 * @return bool true on success
		 */
		function update($table, $constraints, $data)
		{
			//construct the where cluase for limits
			foreach($constraints as $field => $value)
			{
				$where[] = "$field = ".$this->escape($value);
			}
			
			//construct the set clause
			foreach($data as $field => $value)
			{
				$set[] = "$field = ".$this->escape($value);
			}
			
			//construct remainder of the string
			$queryString = "UPDATE $table SET ".implode(' AND ', $set)." WHERE ".implode(' AND ', $where).";";
			
			//run query and return result
			return $this->query($queryString);
		}
		
		/**
         * delete
         * 
		 * Delete a record from the database
		 *
		 * @param string $table the table to delete from
		 * @param assoc_array $constraints the constriants on what to delete (where x = y)
		 * @return bool true on success
		 */
		function delete($table, $constraints)
		{
			//construct the where clause of query
			foreach($constraints as $field => $value)
			{
				$where[] = "$field = ".$this->escape($value);
			}
			
			//construct query
			$queryString = "DELETE FROM $table WHERE ".implode(' AND ', $where).";";
			
			//run the query and return the result
			return $this->query($queryString);
		}
		
		/**
         * num_rows
         * 
		 * Gets the number of rows returned by the last run query
		 *
		 * @return integer number of rows
		 */
		function num_rows()
		{
			return mysql_num_rows($this->_result);
		}
		
		/**
         * get
         * 
		 * Generates and runs a select query, formats the results into an object
		 * along with the number of rows, and clears all the resources from the 
		 * query.
		 *
		 * @param string $table table to run the query on
		 * @param assoc_array $constraints constrains of the query
		 * @param mixed $columns the columns to select data on can be string or assoc_array
		 * @param integer $limit the number of rows to select
		 * @param mixed $orderBy the column to order the results by
		 * @param string $order asc or desc order
		 * @return object results of the query and the number of rows fetched
		 */
		function get($table, $constraints = null, $columns = '*', $limit = null, $orderBy = null, $order = 'asc')
		{
			//construct a where clause for the query
			if($constraints !== null)
			{
				foreach($constraints as $field => $value)
				{
					$where[] = "$field = ".$this->escape($value);
				}
				$where = ' WHERE '.implode(' AND ', $where);
			}
			//we don't need a where clause so lets just leave it blank
			else
			{ $where = ''; }
			
			//construct the select from clause
			if(is_array($columns))
			{
				//columns submitted in an array, so format for query
				$select = 'SELECT '.implode(', ', $columns).' FROM '.$table;
			}
			else
			{
				//columns submitted as string, just add straight into the query
				$select = 'SELECT '.$columns.' FROM '.$table;
			}
			
			//check if a limit has been added
			if($limit !== null)
			{
				//limit set so construct query part
				$limit = ' LIMIT '.$limit;
			}
			//no limit set leave blank
			else
			{ $limit = ''; }
			
			//check if we need to order the results
			if($orderBy !== null)
			{
				//check if items to order by are in string format or array
				if(is_array($orderBy))
				{
					//in array form, lets convert it to string
					$orderBy = implode(', ', $orderBy);
				}
				$order = ' ORDER BY '.$orderBy.' '.$order;
			}
			//no order needed leave blank
			else
			{
				$order = '';
			}
			
			//put it all together
			$queryString = $select.$where.$limit.$order;
			
			//run the query
			$this->_result = $this->query($queryString);
			
			//format the return
			$return = array(
				'num_rows' => $this->num_rows(),
				'results' => array(),
				'query' => $queryString
			);
			//fill the results section of the return
			while($row = mysql_fetch_object($this->_result))
			{
				$return['results'][] = $row;
			}
			
			//free the resources used
			$this->free_result();
			
			//cast the return as an object then return it
			return (object)$return;
		}
		
        /**
         * optimize
         *
         * Optimizes a given table. (Removes a tables overhead caused by deletion 
         * of rows)
         * 
         * @param string $table the table to optimize (remove overhead from)
         */
		function optimize($table)
		{
			return $this->query('OPTIMIZE TABLE '.$table);
		}
		
	}

?>
