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

namespace Services\System\Classes;

abstract class Display extends \Services\System\Service
{
	public static function view($view, &$data = null)
	{
		$requested_view = $view;
		
		$view = str_replace('..', '.', $view);
		$parts = explode('/', $view);
		$filename = array_pop($parts);
		
		$check_dirs = array(APPDIR, SHRDIR);
		foreach ($check_dirs as $dir)
		{
			for ($i = 0; $i <= count($parts); $i++)
			{
				$location = $dir . 'source/';
				if ($i > 0) $location .= implode(array_slice($parts, 0, $i),'/') . '/';
				if ($i > 0 && !is_dir($location)) continue 2; //If this isn't a directory, none of the others will be either
				$location .= 'views' . '/';
				if ($i < count($parts)) $location .= implode(array_slice($parts, -(count($parts)-$i)),'/') . '/';
				$location .= $filename . '.php';
				if (file_exists($location)) break 2;
			}
		}
		$ixt['location'] = $location;
				
		if (!file_exists($location))
		{
			throw new \Exception('View Not Found: ' . $requested_view);
		}
		
		unset($view, $requested_view, $parts, $check_dirs, $dir, $location, $filename, $i);
		
		if (is_array($data)) extract($data, EXTR_SKIP);
		if (!isset($tpl)) $tpl = new \stdClass();
		require($ixt['location']); 
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
		if (!isset($tpl)) $tpl = new \stdClass();
		require($ixt['path']); 
		$data['tpl'] = $tpl;
		return true; 
	}

	public static function template($files, &$data=null, $template = 'default')
	{
		$data['tpl']->template = $template;
		
		$content = '';
		if (!is_array($files)) $files = array($files);
		foreach ($files as $file) $content .= static::return_view($file, $data);
		$data['tpl']->content = $content;
		
		static::template_view($template, $data);
	}
	
}