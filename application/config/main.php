<?php

/**
 * Global Event Logging
 * If set to true, the Profiler will log everything regardless of what 
 * classes having logging enabled.
 */
\Services\System\Profiler::$log_everything = false;

/**
 * Class-based Event Logging
 * If global event logging is set to false, use this to enable logging
 * on a per-class basis. 
 */
// \System\Profiler::enable_logging('\System\Database');

/**
 * Show the event log based on some condition.  For example:
 * 
 *   Show based on remote IP address:  
 *     if ($_SERVER['REMOTE_ADDR'] == '123.123.123.123') {
 * 
 *   Show based on variable set in config files: 
 *     if ($debug === true) {
 */
if (false) \Services\System\Profiler::$output_html = true;

/**
 * Database Configuration
 * \System\Database::connect(identifier, driver, server, username, password, database);
 */
if (isset($application_config['databases']))
{
	foreach ($application_config['databases'] as $id => $db_string)
	{
		$db_array = explode(',', $db_string);
		$db_array = array_map('trim', $db_array);
		switch (count($db_array))
		{
			case 5:	\Services\System\Database::connect($id, $db_array[0], $db_array[1], $db_array[2], $db_array[3], $db_array[4]); break;
			case 3:	\Services\System\Database::connect_dsn($id, $db_array[0], $db_array[1], $db_array[2]); break;
			case 2: \Services\System\Database::connect_dsn($id, $db_array[0], $db_array[1]); break;
			case 1:	\Services\System\Database::connect_dsn($id, $db_array[0]); break;
			default: throw new Exception('Invalid number of parameters in config database settings.'); break;
		}
	}
}