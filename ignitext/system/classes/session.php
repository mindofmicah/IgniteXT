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

class Session
{
	public static function __set($name, $value)
	{
		$_SESSION[ APPID ][ $name ] = $value;
	}
	
	public static function __get($name)
	{
		return $_SESSION[ APPID ][ $name ];
	}
}