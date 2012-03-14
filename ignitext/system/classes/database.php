<?php
/**
 * Database Manager
 * 
 * Manages connections, runs queries, returns results.
 * This class serves as a wrapper for PDO.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

abstract class Database
{
	protected static $PDO_connections = array();
	protected static $selected_connection = '';
	
	public static $log_events = false;
	
	/**
	 * Connects to a database server and stores the connection in $PDO_connections.
	 *  
	 * @param string $identifier A name used to identify this connection
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 * @param string $database 
	 */
	public static function connect($identifier, $driver, $server, $username, $password, $database)
	{
		static::$PDO_connections[$identifier] = new \PDO($driver . ':host=' . $server . ';dbname=' . $database, $username, $password);
		if (count(static::$PDO_connections) == 1) static::$selected_connection = $identifier;
	}
	
	/**
	 * Connects to a database the same way as the connect function but uses a DSN string instead
	 * 
	 * @param string $identifier A name used to identify this connection
	 * @param string $dsn Data Source Name, contains information required to connect to a database
	 * @param string $username
	 * @param string $password
	 */
	public static function connect_dsn($identifier, $dsn, $username = null, $password = null)
	{
		if ($username != null && $password != null)
			static::$PDO_connections[$identifier] = new \PDO($dsn, $username, $password);
		else if ($username != null)
			static::$PDO_connections[$identifier] = new \PDO($dsn, $username);
		else
			static::$PDO_connections[$identifier] = new \PDO($dsn);
		if (count(static::$PDO_connections) == 1) static::$selected_connection = $identifier;
	}
	
	/**
	 * Uses the identifier to select a stored connection
	 * 
	 * @param string $identifier 
	 */
	public static function select_connection($identifier)
	{
		if (array_key_exists($identifier, static::$PDO_connections))
			static::$selected_connection = $identifier;
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
			return static::$PDO_connections[static::$selected_connection];
		else if (array_key_exists($identifier, static::$PDO_connections))
			return static::$PDO_connections[$identifier];
		else 
			throw new Exception('Invalid database selection.  Make sure you have successfully connected to the database that you are trying to get the PDO object for.');
	}
	
	/**
	 * Gets the identifier string for the currently selected connection
	 * 
	 * @return string $identifier
	 */
	public static function selected_connection() { return static::$selected_connection; }
	
	/**
	 * Execute a query, return a result
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return resource $result
	 */
	public static function query()
	{
		$arguments = func_get_args();
		$query = array_shift($arguments);
		if (count($arguments)==1 && is_array($arguments[0])) $arguments = $arguments[0];
		$dbh = static::get_pdo();
		$sth = $dbh->prepare($query);
		if (static::$log_events)
		{
			$time1 = microtime(true);
			$sth->execute($arguments);
			$time2 = microtime(true);
			\System\Profiler::event( \System\Event_Type::NORMAL, 'System\\Database', 'query', $sth->queryString . ' (t:' . number_format($time2-$time1,6) . 's)');
		}
		else $sth->execute($arguments);
		return $sth;
	}

	/**
	 * Execute a query and return an array of objects representing rows
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
	public static function rows()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetchAll(\PDO::FETCH_OBJ);
	}
	
	/**
	 * Execute a query and return an array of class objects representing rows
	 * 
	 * @param string $class_name
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return class $rows
	 */
	public static function class_rows()
	{
		$arguments = func_get_args();
		$class_name = array_shift($arguments);
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetchAll(\PDO::FETCH_CLASS, $class_name);
	}
	
	/**
	 * Execute a query and return an associative array of objects representing rows, 
	 * uses $key to create associative array.
	 * 
	 * If $key = 'user_id' then the array will be in this form:
	 * $users['1092']['first_name']
	 * 
	 * @param string $key
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return array $rows
	 */
	public static function rows_key()
	{
		$arguments = func_get_args();
		
		if (count($arguments) > 1) $key = array_shift($arguments);
		else throw new Exception('The rows_key function requires at least 2 parameters: $key and $query.');
				
		$rows = call_user_func_array('static::rows', $arguments);
		if (array_key_exists($key,$rows[0]) == false) 
			throw new Exception('The specified key does not exist in the result set.');
		
		foreach ($rows as $row) 
			$rows_key[ $row[$key] ] = $row;
		
		return $rows_key;
	}

	/**
	 * Execute a query and return a single object representing a row
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @param array $row
	 */
	public static function row()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetch(\PDO::FETCH_OBJ);
	}
	
	/**
	 * Execute a query and return a single class object representing a row
	 * 
	 * @param string $class_name
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @param class $row
	 */
	public static function class_row()
	{
		$arguments = func_get_args();
		$class_name = array_shift($arguments);
		$sth = call_user_func_array('static::query', $arguments);
		$sth->setFetchMode(\PDO::FETCH_CLASS, $class_name);
		return $sth->fetch(\PDO::FETCH_CLASS);
	}
 
	/**
	 * Execute a query and return the first field that was selected
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return string $field
	 */
	public static function field()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetchColumn();
	}

	/**
	 * Execute a query and return an array of all fields in the first column of results
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return array $fields
	 */
	public static function fields_col()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * Execute a query and return an array of all fields in the first row of results
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return array $fields
	*/
	public static function fields_row()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		return $sth->fetch(\PDO::FETCH_NUM);
	}
	
	/**
	 * Execute a query and return the insert id
	 * 
	 * @param string $query
	 * @param string $value1 (optional, values to be escaped, then replaces ? in query, can be array or list)
	 * @return integer $insert_id
	 */
	public static function insert()
	{
		$arguments = func_get_args();
		$sth = call_user_func_array('static::query', $arguments);
		$dbh = static::get_pdo();
		return $dbh->lastInsertId();
	}
	
}