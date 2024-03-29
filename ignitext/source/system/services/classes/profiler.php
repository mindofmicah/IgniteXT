<?php
/**
 * Profiler
 * 
 * Keeps track of things that happen during execution and how long those things
 * took.  Used for debugging and logging purposes.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Services\System\Classes;

abstract class Event_Type
{
	const NORMAL = 0;
	const NOTICE = 1;
	const WARNING = 2;
	const ERROR = 3;
	const ON_FIRE = 4;
	static $types = array('NORMAL','NOTICE','WARNING','ERROR','ON_FIRE');
	function get_type($type)
	{
		return static::$types[$type];
	}
}

abstract class Profiler extends \Services\System\Service
{
	//Settings
	public static $log_everything = false;
	public static $output_json = false;
	public static $output_html = false;
	
	protected static $classes_logging = array();
	protected static $start_time = 0;
	protected static $log = array();
	
	public static function get_classes_logging() { return static::$classes_logging; }
	public static function get_start_time() { return static::$start_time; }
	public static function get_log() { return static::$log; }
	
	public static function start()
	{
		static::$start_time = microtime(true);
		register_shutdown_function('\Services\System\Profiler::finish');
	}
	
	public static function finish()
	{
		if (static::$output_html) static::render_html();
		if (static::$output_json) static::render_json();
	}

	public static function event($event_type, $class, $method, $description)
	{
		if (static::$log_everything)
		{
			static::$log[] = array(
				'time' => microtime(true) - static::$start_time, 
				'type' => $event_type, 
				'from' => $class, 
				'action' => $method, 
				'description' => $description
			);
			if ($event_type == Event_Type::ON_FIRE) { die(); }
		}
	}
	
	public static function render_json()
	{
		$ent_log = static::$log;
		array_walk_recursive($ent_log, function(&$item, $key) {	$item = htmlentities($item); });
		$json_log = json_encode($ent_log);
		$esc_json_log = str_replace("'", "\'", $json_log);
		?>
			<script type="text/javascript">
				var debug = eval('(<?php echo $esc_json_log?>)');
			</script>
		<?php
	}
	
	public static function render_html()
	{
		?>
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
		<script type="text/javascript">
			var set_jquery_noconflict = false;
			if (!window.jQuery)
			{
				if (typeof $ == 'function') set_jquery_noconflict = true;
				document.write('<scr' + 'ipt src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></scr'+'ipt>');
			}
		</script>
		<script type="text/javascript">
			if (set_jquery_noconflict) jQuery.noConflict();
			jQuery(function($) {
				$.each(debug, function(index, value) {
					//alert(value.time + ' ' + value.type + ' ' + value.from + ' ' + value.action + ' ' + value.description);
				});					
			});
		</script>
		
		<div id="ixt_profiler">
			<h1>IgniteXT Profiler</h1>
		</div>

		<table id="ixt_events">
			<tr>
				<th>&nbsp;</th>
				<th>Time</th>
				<th>From</th>
				<th>Action</th>
				<th>Description</th>
			</tr>
			<?php foreach (static::$log as $log): ?>
			<?php $type = Event_Type::get_type($log['type']); ?>
				<tr>
					<td class="type <?php echo strtolower($type)?>"><?php echo $type=='ON_FIRE'?'&otimes;':'&nbsp;'?></td>
					<td><?php echo number_format($log['time'],6)?>s</td>
					<td><?php echo $log['from']?></td>
					<td><?php echo $log['action']?></td>
					<td><?php echo $log['description']?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?
	}
}