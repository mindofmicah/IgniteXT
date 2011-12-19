<?php
/**
 * Event Log Class
 */
namespace System;

class Event_Type
{
	const NORMAL = 0;
	const NOTICE = 1;
	const ERROR = 2;
	const ON_FIRE = 3;
}

class Event
{
	static $log = array();
	static $log_events = true;
	static $start_time = 0;

	function event($type, $from, $action, $description)
	{
		if (self::$log_events)
		{
			if (!isset(self::$log[0])) self::$start_time = microtime(true);
			self::$log[] = array(microtime(true), $type, $from, $action, $description);
		}
	}
}