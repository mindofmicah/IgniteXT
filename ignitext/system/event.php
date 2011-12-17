<?php
/**
 * This class handles 
 */
namespace System;
class Event
{
		static $log = array();
		static $log_events = true;
		
		function event($type, $from, $action, $description)
		{
			if (self::$log_events)
			{
				self::$log[] = array($type, $from, $action, $description);
			}
		}
}