<?php
/**
 * Session
 * 
 * Manages the application's session.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

abstract class Session
{
	public static function set($name, $value)
	{
		$_SESSION[APPID][$name] = $value;
	}
	
	public static function get($name)
	{
		if (isset($_SESSION[APPID][$name])) return $_SESSION[APPID][$name];
		else return null;
	}
	
	public static function &reference()
	{
		if (!isset($_SESSION[APPID])) $_SESSION[APPID] = array();
		return $_SESSION[APPID];
	}
}