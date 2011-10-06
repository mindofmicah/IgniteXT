<?php
/**
 * This class contains functions to display views and templates.  Primarily used by a controller class.
 */
class Display
{
	function load_view($file,$data=null)
	{   
		if (is_array($data)) extract($data);

		$file = str_replace('..','.',$file);
		$file = APPDIR . 'views/' . $file;
		if (!file_exists($file.'.php'))
		{  
			echo "View Not Found: " . $file . ".php"; die();
		}
		require($file.'.php');
	}

	function return_view($file,$data=null)
	{
		ob_start();
		self::load_view($file,$data);
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	function tpl($title,$file,$data=null,$template='templates/main')
	{
		$data['content_title'] = $title;
		$data['content_view'] = $file;
		$this->load_view($template, $data);
	}

	function tpl_rb($title,$file,$rbfile,$data=null,$template='templates/main')
	{
		$data['content_title'] = $title;
		$data['content_view'] = $file;
		$data['rightbar_view'] = $rbfile;
		$this->load_view($template, $data);
	}

	function widget($file,$data=null)
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