<?php
/**
 * This class contains functions to display views and templates.  Primarily used by a controller class.
 */
namespace System;
class Display
{
	public static function load_view($relative_file,$data=null)
	{   
		if (is_array($data)) extract($data);

		$relative_file = str_replace('..','.',$relative_file);
		
		$absolute_file = APPDIR . 'source/views/' . $relative_file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		
		$file_parts = explode('/',$relative_file);
		$package = array_shift($file_parts);
		$relative_file = implode('/',$file_parts);
		$absolute_file = APPDIR . 'packages/' . $package . '/views/' . $relative_file . '.php';
		if (file_exists($absolute_file)) { require($absolute_file); return; }
		
		echo "View Not Found: " . $relative_file . ".php"; die();
	}

	public static function return_view($file,$data=null)
	{
		ob_start();
		self::load_view($file,$data);
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	public static function template($title,$file,$data=null,$template='templates/main')
	{
		$data['content_title'] = $title;
		$data['content_view'] = $file;
		self::load_view($template, $data);
	}

	public static function widget($file,$data=null)
	{
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		$file = APPDIR . 'widgets/' . $file;
		if (!file_exists($file.'.php'))
		{  
			echo "Widget Not Found: " . $file . ".php"; die();
		}
		require($file.'.php');
	}
	
}