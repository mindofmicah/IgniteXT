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
	public static function view($view, &$data = null)
	{
		$ixt = array(); //Prevent Variable Collisions From $data
		$ixt['view'] = $ixt['requested_view'] = $view;
		unset($view);
		
		$ixt['view'] = str_replace('..', '.', $ixt['view']);
		
		$ixt['path'] = APPDIR . 'source/views/' . $ixt['view'] . '.php';
		
		if (!file_exists($ixt['path']))
		{
			$ixt['file_parts'] = explode('/', $ixt['view']);
			$ixt['package'] = array_shift($ixt['file_parts']);
			$ixt['view'] = implode('/', $ixt['file_parts']);
			$ixt['path'] = APPDIR . 'packages/' . $ixt['package'] . '/views/' . $ixt['file'] . '.php';
		}
		
		if (!file_exists($ixt['path']))
		{
			throw new \Exception('View Not Found: ' . $ixt['requested_view']);
		}
		
		if (is_array($data)) extract($data, EXTR_SKIP);
		if (!isset($tpl)) $tpl = array();
		require($ixt['path']); 
		$data['tpl'] = $tpl;
		return true; 
	}

	public static function return_view($view, &$data = null)
	{
		ob_start();
		static::view($view,$data);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	public static function template_view($view, &$data)
	{
		$ixt = array(); //Prevent Variable Collisions From $data
		$ixt['view'] = $ixt['requested_view'] = $view;
		unset($view);
		
		$ixt['view'] = str_replace('..', '.', $ixt['view']);
		
		$ixt['path'] = APPDIR . 'templates/' . $ixt['view'] . '.php';
		if (!file_exists($ixt['path']))
		{
			throw new \Exception('Template View Not Found: ' . $ixt['requested_view']);
		}
		
		if (is_array($data)) extract($data, EXTR_SKIP);
		if (!isset($tpl)) $tpl = array();
		require($ixt['path']); 
		$data['tpl'] = $tpl;
		return true; 
	}

	public static function template($files, &$data=null, $template = 'default')
	{
		$content = '';
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) $content .= static::return_view($file, $data);
		$data['tpl']['content'] = $content;
		static::template_view($template, $data);
	}
	
}