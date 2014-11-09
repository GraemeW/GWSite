<?php

/*	Basic accessor/wrapper class for mysqli, the not-insane way of accessing MySQL in PHP.
	Handles connections, properly binds parameters, parses responses into associative arrays.
	Define the global variables $db_host, $db_user, $db_pass, $db_name or update these variables
	in the Open() function before using in your project.
	
	Basic use:
		include 'db.php';
		...
		$db = new Database();
		$result = $db->Select('SELECT firstname,lastname,title FROM users WHERE id = ?', $user_id);
		$firstname = $result[0]['firstname'];
		
		$success = $db->Alter('UPDATE users SET firstname = ? WHERE id = ?', $firstname, $user_id);
		
		echo $db->query_count . ' SQL queries took ' . $db->total_query_time . ' seconds';
	
	Basic notes:
		- Database::Select() always returns a 2-D array, even if only one row has been returned by the database.
		The rows are integer-indexed in database order, the columns are named as in the database response 
		(SELECT column AS name is respected). As an example:
			$result = $db->Select('SELECT count(id) AS count FROM users');
			$user_count = $result[0]['count'];
		
		- Alter() is used for queries that don't return values (UPDATEs, INSERTs, ALTERs, etc). It returns true or false 
		based on the database response (it passes through return value of mysqli_stmt::execute()).
		Note that mysqli_stmt::execute() can return true even if zero rows were affected by the query.
		When doing an INSERT to a table with an auto_increment column, get the ID of the newly created row 
		with GetLastInsertID() - this is a thin wrapper on 'SELECT LAST_INSERT_ID()'.
		
		- FormatDate() formats integer Unix timestamps and DateTime objects in the MySQL DATETIME format, 'YYYY-MM-DD HH:MM:SS'
		(as produced by PHP's date('Y-m-d H:i:s')). Use this function if you need the consistent format. 
		If Select() and Alter() are called with a DateTime param, it will automatically be reformatted as a string using FormatDate().
		
		- Any arrays passed as params to Select() and Alter() will be unpacked, recursively if required.
		The following are equivalent:
			$db->Select('SELECT firstname FROM users WHERE id = ? AND class = ? AND year = ?', $id, $class, $year);
		
			$params = array($id, $class, $year);
			$db->Select('SELECT firstname FROM users WHERE id = ? AND class = ? AND year = ?', $params);
			
			$params = array($id, array($class, $year));
			$db->Select('SELECT firstname FROM users WHERE id = ? AND class = ? AND year = ?', $params);
		Use whichever is most convenient at the point of invocation.
		
		- Error() returns the string description of the last error encountered. This is a wrapper on mysqli::error().
		
		- Basic statistics/performance are available through the $query_count, $last_query_time, and $total_query_time variables.
		The time variables are in seconds.
		
		- There is a thin wrapper for transaction support, using functions StartTransaction(), Commit(), and Rollback().
		This uses the functions mysqli::autocommit(), mysqli::commit(), mysqli::rollback(). It's relatively untested so 
		please tread carefully. Behaviour with non-transactional databases is undefined. Please don't use MyISAM.
		
	Authors:
		Originally written in 2009 for the Take the GRT project by Jarek Piórkowski.
		
		Amended and updated in 2011 by Jarek Piórkowski for Blast Radius for a client project.
		Permission has been granted to Blast Radius to freely reuse, improve, amend, rewrite, 
		and otherwise modify the class as required, in perpetuity, without credit being mandatory.
		
		Distribution and use outside of Blast Radius projects is subject to the ISC license:
		
		Copyright (c) 2009-2012, Jarek Piórkowski <jarek@piorkowski.ca>
		
		Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby granted, 
		provided that the above copyright notice and this permission notice appear in all copies.
		
		The software is provided "as is" and the author disclaims all warranties with regard to this software 
		including all implied warranties of merchantability and fitness. In no event shall the author be liable 
		for any special, direct, indirect, or consequential damages or any damages whatsoever resulting from loss of use,
		data or profits, whether in an action of contract, negligence or other tortious action, 
		arising out of or in connection with the use or performance of this software. 
	*/

class Database
{
	private static $conn = false;

	public $query_count = 0;
	public $last_query_time = 0;
	public $total_query_time = 0;

	function Open()
	{
		global $db_host, $db_user, $db_pass, $db_name;

		self::$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
		
		// enable unicode
		// http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
		// SET NAMES 'cp1251' tells the server, “future incoming messages from this client are in character set cp1251.” 
		// It also specifies the character set that the server should use for sending results back to the client. 
		// (For example, it indicates what character set to use for column values if you use a SELECT statement.)
		$this->Alter('SET NAMES \'utf8\';');

		if (mysqli_connect_errno())
		{
			throw new Exception('Database connect failed: ' . mysqli_connect_error());
		}
	}
	
	function StartTransaction()
	{
		if (self::$conn === false || self::$conn === null)
		{
			self::Open();
		}
	
		// turn off autocommit to go into 'transaction' mode
		self::$conn->autocommit(false);
	}
	
	function Commit()
	{
		// commit transaction
		self::$conn->commit();
		
		// reenable autocommit
		self::$conn->autocommit(true);
	}
	
	function Rollback()
	{
		// rollback transaction
		self::$conn->rollback();
		
		// reenable autocommit
		self::$conn->autocommit(true);
	}

	function Select($query_string)
	{
		$query_start = microtime(true);
		
		if (self::$conn === false || self::$conn === null)
		{
			self::Open();
		}

		$stmt = mysqli_stmt_init(self::$conn);

		if ($stmt->prepare($query_string))
		{
			$num_params = func_num_args();
			$params = array();
			$types = array();

			if ($num_params > 1)
			{
				// Need to add params. bind_param expects a number of params, by reference,
				// the first of which is a string of types of the SQL query params. 
				// Insane, but what are you going to do? Read out the param types, 
				// add them to one array, add params to another array,
				// flatten the type array, and we're almost ready.			
			
				for ($i = 1; $i < $num_params; ++$i)
				{
					$this_param = func_get_arg($i);
					self::AddParameter($params, $types, $this_param);
				}

				$types_flat = implode('', $types);
				array_unshift($params, $types_flat); // add $types_flat at the beginning of $params - required format
				
				call_user_func_array(array($stmt, 'bind_param'), self::refValues($params)); // bind parameters. must bind all at the same time
			}

			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows > 0)
			{
				$res_meta = $stmt->result_metadata();

				while ($field = $res_meta->fetch_field())
				{
					$fieldnames[] = &$array[$field->name];
				}

				call_user_func_array(array($stmt, 'bind_result'), $fieldnames);
			}

			while ($stmt->fetch())
			{
				foreach($array as $key => $val) 
				{ 
					$c[$key] = $val; 
				}

				$result_array[] = $c;
			}

			$stmt->free_result();

			$this->query_count++;
			$this->last_query_time = microtime(true) - $query_start;
			$this->total_query_time = $this->total_query_time + $this->last_query_time;

			if (empty($result_array) == true)
			{
				$result_array = array();
			}

			return $result_array;
		}
		else
		{
			throw new Exception('Unable to prepare SQL command ' . $this->Error());
		}
	}

	private function AddParameter(&$params, &$types, $this_param)
	{
		if (is_array($this_param) === true && count($this_param) > 0)
		{
			// recurse into any number of nested arrays as necessary
			foreach ($this_param as $this_parameter)
			{
				self::AddParameter($params, $types, $this_parameter);
			}
		}
		else
		{
			if (is_string($this_param))
			{
				$types[] = 's';
			}
			else if (is_int($this_param))
			{
				$types[] = 'i';
			}
			else if (is_double($this_param))
			{
				$types[] = 'd';
			}
			else if ($this_param instanceof DateTime)
			{
				$types[] = 's';
				$this_param = self::FormatDate($this_param);
			}
			else
			{
				$types[] = 's'; // string as default
			}

			$params[] = $this_param;
		}
	}
	
	// from http://php.net/manual/en/mysqli-stmt.bind-param.php#96770
	// by fabio at kidopi dot com dot br, 15-Mar-2010 05:14
	// turn a value array into a reference array
	// required for recent versions of mysqli_stmt::bind_param
	private function refValues($arr)
	{ 
		if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+ 
		{
			$refs = array(); 
			foreach($arr as $key => $value) 
				$refs[$key] = &$arr[$key]; 
			return $refs; 
		}
		else 
		{
			return $arr; 
		}
	}

	function Alter($query_string)
	{
		$query_start = microtime(true);
		
		if (self::$conn === false || self::$conn === null)
		{
			self::Open();
		}

		$stmt = mysqli_stmt_init(self::$conn);

		if ($stmt->prepare($query_string))
		{
			$num_params = func_num_args();
			$params = array();
			$types = array();

			if (func_num_args() > 1)
			{
				for ($i = 1; $i < $num_params; ++$i)
				{
					$this_param = func_get_arg($i);
					self::AddParameter($params, $types, $this_param);
				}

				$types_flat = implode('', $types);
				array_unshift($params, $types_flat); // add $types_flat at the beginning of $params - required format
				
				call_user_func_array(array($stmt, 'bind_param'), self::refValues($params)); // bind parameters. must bind all at the same time
			}

			$result = $stmt->execute();
			
			$this->query_count++;
			$this->last_query_time = microtime(true) - $query_start;
			$this->total_query_time = $this->total_query_time + $this->last_query_time;
			
			return $result;
		}
		else
		{
			throw new Exception('Unable to prepare SQL command ' . $this->Error());
		}
	}

	function GetLastInsertID()
	{
		$result = $this->Select('SELECT LAST_INSERT_ID() AS id');

		return $result[0]['id'];
	}
	
	function GetAffectedRows()
	{
		return self::$conn->affected_rows;
	}

	function Error()
	{
		if (self::$conn !== false)
		{
			if (empty(self::$conn->error) === false)
			{
				return self::$conn->error;
			}
			else
			{
				return mysqli_error(self::$conn);
			}
		}
		else
		{
			return 'unknown error';
		}
	}
	
	function FormatDate($timestamp)
	{
		if (is_integer($timestamp))
		{
			return date('Y-m-d H:i:s', $timestamp);
		}
		else if ($timestamp instanceof DateTime)
		{
			return $timestamp->format('Y-m-d H:i:s');
		}
		else
		{
			// let through in case we passed in a string or something
			return $timestamp;
		}
	}
}

?>
