<?php
namespace Mocks\System;

class File_System
{
	public static $files = array();
	public static $dirs = array();
	
	public static function enable()
	{
		$orig = 'System\File_System::';
		$new = __NAMESPACE__ . '\File_System::';
		\Patchwork\replace($orig . 'file_exists', $new . 'file_exists');
		\Patchwork\replace($orig . 'is_dir',      $new . 'is_dir');
	}
	
	public static function create_file($filename)
	{
		static::$files[] = $filename;
	}
	
	public static function create_dir($dir)
	{
		static::$dirs[] = $dir;
	}
	
	public static function file_exists($filename)
	{
		if (in_array($filename, static::$files)) return true;
		return false;
	}
	
	public static function is_dir($dir)
	{
		if (in_array($dir, static::$dir)) return true;
		return false;
	}
	
}