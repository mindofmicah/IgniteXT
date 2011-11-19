<?php
/**
 * This class contains functions to display views and templates.  Primarily used by a controller class.
 */
namespace System;
class Display
{
	public static function view($file,$data=null)
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

	public static function return_view($file,$data=null)
	{
		ob_start();
		self::view($file,$data);
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}
	
	public static function template_view($file,$data)
	{
		$requested_file = $file;
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		
		$absolute_file = APPDIR . 'templates/' . $file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		echo "Template View Not Found: " . $requested_file . ".php"; die();
	}

	public static function template($title,$file,$data=null,$template='main')
	{
		$data['content_title'] = $title;
		$data['content_view'] = $file;
		self::template_view($template, $data);
	}
	
}