<?php
/**
 * IgniteXT Session
 */
namespace System;
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