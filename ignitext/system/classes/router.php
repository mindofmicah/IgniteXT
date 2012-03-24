<?php
/**
 * Router
 * 
 * Determines which controller to load and method to call based on the URL 
 * entered.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

abstract class Router 
{

	public static $requested_url;
	public static $action;
	public static $manual_routes = array();
	
	/**
	 * Parses the URL to determine which controller and method should be used
	 * to display the page, then calls that method.
	 */
	public static function route($route)
	{
		$requested_url = isset($route) ? trim($route, '/') : '';		
		
		$action = false;
		if ($action === false) $action = static::manual_routes($requested_url);
		if ($action === false) $action = static::automatic_routes($requested_url);
		if ($action === false) $action = static::manual_routes($requested_url, true);
		
		if ($action === false && method_exists('\Controllers\_404','index')) 
			$action = '\Controllers\_404::index';
		
		//The 404 controller doesn't exist, fail gracefully.
		if ($action === false)
		{
			header("HTTP/1.0 404 Not Found");
			echo "404 Not Found";
			die();
		}

		static::$action = $action;
		static::$requested_url = $requested_url;
		call_user_func($action);
	}
	
	protected static function automatic_routes($requested_url)
	{		
		if ($requested_url == '') $url_parts = array();
		else $url_parts = explode('/', $requested_url);
		
		//Search the source folders first, then the package folders
		//array($directory, $is_package)
		$search_dir_list = array(
			array(APPDIR, false),
			array(SHRDIR, false),
			array(APPDIR, true),
			array(SHRDIR, true),
		);
		
		foreach ($search_dir_list as $search_dir_info)
		{
			list($search_dir, $is_package) = $search_dir_info;
			
			if ($is_package && count($url_parts) == 0) continue;
			$url_parts_copy = $url_parts;
			
			if ($is_package)
			{
				$package = array_shift($url_parts_copy);
				$current_dir = $search_dir . 'packages/' . $package . '/controllers/';
				$namespace =  '\\Controllers\\' . $package . '\\';
			}
			else
			{
				$current_dir = $search_dir . 'source/controllers/';
				$namespace = '\\Controllers\\';
			}
			
			if (!is_dir($current_dir)) continue;
			
			if (count($url_parts_copy) == 0 && method_exists($namespace . 'index', 'index')) return $namespace . 'index::index';
			
			$try_action = 'index';
			$try_controller = $namespace . implode('\\', $url_parts_copy);
			if (method_exists($try_controller, $try_action)) return $try_controller . '::' . $try_action;
			
			$try_action = array_pop($url_parts_copy);
			$try_controller = $namespace . implode('\\', $url_parts_copy);
			if (method_exists($try_controller, $try_action)) return $try_controller . '::' . $try_action;
			
			//If the method doesn't exist, try prefixing it with "m_".  This is useful
			//if you want to have an action named "list" but PHP won't allow you to have
			//a method named "list".
			$try_action = 'm_' . $try_action;
			if (method_exists($try_controller, $try_action)) return $try_controller . '::' . $try_action;
		}
		
		return false;
	}
	
	protected static function manual_routes($requested_url, $after_auto = false)
	{
		return false;
	}
	
	public static function simple_route($route, $after_auto = false) 
	{
		
	}
	
	public static function regex_route($route, $vars, $after_auto = false) 
	{
		
	}
	
}