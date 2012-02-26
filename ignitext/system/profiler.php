<?php
/**
 * Event Log Class
 */
namespace System;

class Event_Type
{
	const NORMAL = 0;
	const NOTICE = 1;
	const WARNING = 2;
	const ERROR = 3;
	const ON_FIRE = 4;
	static $types = array('NORMAL','NOTICE','WARNING','ERROR','ON_FIRE');
	function get_type($type)
	{
		return self::$types[$type];
	}
}

class Profiler
{
	static $log_events = false;
	static $display_log = false;
	
	static $log = array();
	static $start_time = 0;

	function event($type, $from, $action, $description)
	{
		if (self::$log_events)
		{
			if (!isset(self::$log[0])) self::$start_time = microtime(true);
			self::$log[] = array(
				'time' => microtime(true) - self::$start_time, 
				'type' => $type, 
				'from' => $from, 
				'action' => $action, 
				'description' => $description
			);
			if ($type == Event_Type::ON_FIRE) { self::display(); die(); }
		}
	}
	
	function display()
	{
		?>
		<br style="clear: both;" />
		<style type="text/css">
			#ixt_events { border-collapse: collapse; }
			#ixt_events td, #ixt_events th { padding: 5px; }
			#ixt_events th { border: 1px solid #DDD; background-color: #EEE; }
			#ixt_events td { border: 1px solid #EEE; }
			#ixt_events td.type { width: 32px; }
			#ixt_events td.normal { border: 1px solid #7E6; background-color: #7E6; }
			#ixt_events td.notice { border: 1px solid #6BE; background-color: #6BE; }
			#ixt_events td.warning { border: 1px solid #FF0; background-color: #FF0; }
			#ixt_events td.error { border: 1px solid #F00; background-color: #F00; }
			#ixt_events td.on_fire { border: 1px solid #000; background-color: #000; text-shadow: 0 -2px 1px #FF0, 0 -4px 1px #FD0, 0 -6px 1px #FB0, 0 -8px 2px #F90, 0 -10px 2px #F70; font-weight: bold; font-size: 17px; text-align: center; }
		</style>
		<table id="ixt_events">
			<tr>
				<th>&nbsp;</th>
				<th>Time</th>
				<th>From</th>
				<th>Action</th>
				<th>Description</th>
			</tr>
			<? foreach (self::$log as $log): ?>
			<? $type = Event_Type::get_type($log['type']); ?>
				<tr>
					<td class="type <?=strtolower($type)?>"><?=$type=='ON_FIRE'?'&otimes;':'&nbsp;'?></td>
					<td><?=number_format($log['time'],6)?>s</td>
					<td><?=$log['from']?></td>
					<td><?=$log['action']?></td>
					<td><?=$log['description']?></td>
				</tr>
			<? endforeach; ?>
		</table>
		<?
	}
}