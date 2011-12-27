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

class Event
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
		}
	}
	
	function display()
	{
		?>
		<table id="ixt_events">
			<tr>
				<th>&nbsp;</th>
				<th>Time</th>
				<th>From</th>
				<th>Action</th>
				<th>Description</th>
			</tr>
			<? foreach (self::$log as $log): ?>
			<? $type = \System\Event_Type::get_type($log['type']); ?>
				<tr>
					<td><img src="<?=BASEURL?>assets/ixt/images/event_<?=strtolower($type)?>.png" alt="<?=$type?>"></td>
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