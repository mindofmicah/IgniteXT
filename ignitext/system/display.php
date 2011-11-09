<?php
/**
 * This class contains functions to display views and templates.  Primarily used by a controller class.
 */
namespace System;
class Display
{
	public static function load_view($file,$data=null)
	{   
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		$file = APPDIR . 'source/views/' . $file;
		if (!file_exists($file.'.php'))
		{  
			echo "View Not Found: " . $file . ".php"; die();
		}
		require($file.'.php');
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