<?php
/**
 * Database Helper Functions
 * 
 * This class is used to make getting results from queries just a little bit easier.
 */
class DB
{
	/**
	 * Execute a query, return a result
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return resource $result
	 */
  public static function query()
  {
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
    $result = mysql_query($query) or die(mysql_error());
    return $result;
  }

	/**
	 * Execute a query and return an array of objects representing rows
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
  public static function rows()
  {
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
    $result = self::query($query);
    if (mysql_num_rows($result)==0) return false;
    $rows = array();
		
		while ($row = mysql_fetch_object($result)) $rows[] = $row;
		
    return $rows;
  }
	
	/**
	 * Execute a query and return an array of objects representing rows, uses $key to create associative array.
	 * If $key = 'user_id' then the array will be in this form:
	 * $users['1092']->first_name
	 * 
	 * @param string $query
	 * @param string $key
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
	public static function rows_key()
	{
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $key = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
		$result = self::query($query);
    if (mysql_num_rows($result)==0) return false;
    $rows = array();
		
		//Check if key exists, if not, clear it and reset mysql row
		if ($key!='')
		{
			$row = mysql_fetch_object($result);
			if (!property_exists($row,$key)) $key = '';
			mysql_data_seek($result,0);
		}
		
		if ($key!='')
			while ($row = mysql_fetch_object($result)) $rows[ $row->$key ] = $row;
		else
			while ($row = mysql_fetch_object($result)) $rows[] = $row;
		
    return $rows;	
	}

  /**
	 * Execute a query and return a single object representing a row
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @param stdClass $object
	 */
  public static function row()
  {
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
    $result = self::query($query);
    if (mysql_num_rows($result)==0) return false;
    return mysql_fetch_object($result);
  }
 
  /**
	 * Execute a query and return the first field that was selected
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return string $field
	 */
  public static function field()
  {
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
    $result = self::query($query);
    return mysql_result($result,0);
  }
	
	/**
	 * Execute a query and return the insert id
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return integer $insert_id
	 */
	public static function insert()
	{
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)>0) $query = self::replace($query,$arguments);
		
		self::query($query);
		return mysql_insert_id();
	}
	
	/**
	 * Escapes an array BY REFERENCE, changes original array passed to function
	 * 
	 * @param array $fields
	 */
	public static function escape_array(&$fields)
	{
		foreach ($fields as &$field) $field = mysql_real_escape_string($field);
	}
	
	/**
	 * Replaces question marks in a query with mysql escaped fields
	 * Usage: DB::replace($query, $field1, $field2, ...);
	 * 
	 * @param string $query
	 * @param string $field
	 * @return string $replaced
	 */
	public static function replace()
	{
		$arguments = func_get_args();
		$query = array_shift($arguments);
		
		$fields = array();
		
		foreach ($arguments as $argument)
		{
			if (is_array($argument)) 
			{
				$argument = array_values($argument); //Associative array to numeric array
				$fields = array_merge($fields, $argument);
			}
			else $fields[] = $argument;
		}
		
		self::escape_array($fields);
		
		$escape_query = '';
		$parts = explode('?',$query);
		for ($x=0; $x<count($parts); $x++)
		{
			$escape_query .= $parts[$x];
			if ($x<count($parts)-1)
			{
				$escape_query .= '\'';
				if ($x != (count($parts)-1) && isset($fields[$x])) $escape_query .= $fields[$x];
				$escape_query .= '\'';
			}
		}
		return $escape_query;
	}
	
	/**
	 * Mysql escapes a value and puts single quotes around it
	 * 
	 * @param string $value
	 * @return string $escaped_value
	 */
	public static function x($value) { return '\'' . mysql_real_escape_string($value) . '\''; }
	
}