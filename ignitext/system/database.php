<?php
/**
 * Database Manager
 * 
 * Manages connections and runs queries.  This class serves as a wrapper for PDO.
 */
namespace System;
class Database
{
	private static $PDO_connections = array();
	private static $selected_connection = '';
	
	/**
	 * Connects to a database server and stores the connection in $PDO_connections.
	 *  
	 * @param string $identifier A name used to identify this connection
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 * @param string $database 
	 */
	public static function connect($identifier, $server, $username, $password, $database)
	{
		self::$PDO_connections[$identifier] = new \PDO('mysql:host=' . $server . ';dbname=' . $database, $username, $password);
		if (count(self::$PDO_connections) == 1) self::$selected_connection = $identifier;
	}
	
	/**
	 * Uses the identifier to select a stored connection
	 * 
	 * @param string $identifier 
	 */
	public static function select_connection($identifier)
	{
		if (array_key_exists($identifier, self::$PDO_connections))
			self::$selected_connection = $identifier;
		else
			throw new Exception('Invalid database selection.  Make sure you have successfully connected to the database that you are trying to select.');
	}
	
	/**
	 * Gets the PDO object for the stored connection
	 * 
	 * @param string $identifier
	 * @return PDO $pdo
	 */
	public static function get_pdo($identifier = null)
	{
		if ($identifier == null)
			return self::$PDO_connections[self::$selected_connection];
		else if (array_key_exists($identifier, self::$PDO_connections))
			return self::$PDO_connections[$identifier];
		else 
			throw new Exception('Invalid database selection.  Make sure you have successfully connected to the database that you are trying to get the PDO object for.');
	}
	
	/**
	 * Gets the identifier string for the currently selected connection
	 * 
	 * @return string $identifier
	 */
	public static function selected_connection() { return self::$selected_connection; }
	
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
		$dbh = self::get_pdo();
		$sth = $dbh->prepare($query);
		$sth->execute($arguments);
		return $sth;
	}

	/**
	 * Execute a query and return an array of associative arrays representing rows
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
	public static function rows()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('self::query', $arguments);
		return $sth->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Execute a query and return an associative array of associative arrays representing rows, 
	 * uses $key to create associative array.
	 * 
	 * If $key = 'user_id' then the array will be in this form:
	 * $users['1092']['first_name']
	 * 
	 * @param string $key
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
	public static function rows_key()
	{
		$arguments = func_get_args();
		
		if (count($arguments) > 1) $key = array_shift($arguments);
		else throw new Exception('The rows_key function requires at least 2 parameters: $key and $query.');
				
		$rows = call_user_func_array('self::rows', $arguments);
		if (array_key_exists($key,$rows[0]) == false) 
			throw new Exception('The specified key does not exist in the result set.');
		
		foreach ($rows as $row) 
			$rows_key[ $row[$key] ] = $row;
		
		return $rows_key;
	}

	/**
	 * Execute a query and return a single associative array representing a row
	 * 
	 * @param string $query
	 * @param string $field1 (optional, fields to be escaped, then replaces ? in query, can be array or list)
	 * @param array $row
	 */
	public static function row()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('self::query', $arguments);
		return $sth->fetch(\PDO::FETCH_ASSOC);
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
		$sth = call_user_func_array('self::query', $arguments);
		return $sth->fetchColumn();
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
		$sth = call_user_func_array('self::query', $arguments);
		$dbh = self::get_pdo();
		return $dbh->lastInsertId();
	}
	
}