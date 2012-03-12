<?php
/**
 * Display
 * 
 * Shows views and templates.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

abstract class Display
{
	public static function view($file, &$data = null)
	{
		$requested_file = $file;
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		
		$absolute_file = APPDIR . 'source/views/' . $file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		
		$file_parts = explode('/',$file);
		$package = array_shift($file_parts);
		$file = implode('/',$file_parts);
		$absolute_file = APPDIR . 'packages/' . $package . '/views/' . $file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		
		echo "View Not Found: " . $requested_file . ".php"; die();
	}

	public static function return_view($file, &$data = null)
	{
		ob_start();
		static::view($file,$data);
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}
	
	public static function template_view($file, &$data)
	{
		$requested_file = $file;
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		
		$absolute_file = APPDIR . 'templates/' . $file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		echo "Template View Not Found: " . $requested_file . ".php"; die();
	}

	public static function template($files, &$data=null, $template = 'main')
	{
		$content = '';
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) $content .= static::return_view($file, $data);
		$data['content'] = $content;
		static::template_view($template, $data);
	}
	
}