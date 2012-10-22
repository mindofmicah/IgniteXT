<?php
/**
 * Event Type
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