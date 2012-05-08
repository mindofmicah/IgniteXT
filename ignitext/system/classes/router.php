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
	public static $custom_routes = array();
	
	/**
	 * Parses the URL to determine which controller and method should be used
	 * to display the page, then calls that method.
	 */
	public static function route($route)
	{
		$requested_url = isset($route) ? trim($route, '/') : '';		
		
		$action = false;
		if ($action === false) $action = static::custom_routes($requested_url);
		if ($action === false) $action = static::automatic_routes($requested_url);
		if ($action === false) $action = static::custom_routes($requested_url, true);
		
		if (is_callable($action) === false) $action = false;
		
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
		
		$arr = explode('::', $action, 2);
		$controller = $arr[0];

		if (is_callable($controller . '::pre_route')) call_user_func($controller . '::pre_route');
		call_user_func($action);
		if (is_callable($controller . '::post_route')) call_user_func($controller . '::post_route');
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
	
	protected static function custom_routes($requested_url, $after_auto = false)
	{
		foreach (static::$custom_routes as $route)
		{
			if ($route['after_auto'] == $after_auto)
			{
				$found_match = preg_match($route['regex'], $requested_url, $matches);
				if ($found_match === 1)
				{
					//Get rid of the matched string
					array_shift($matches);
					$i = 0;
					foreach ($matches as $key => $value)
					{
						if ($i % 2 == 0 && !isset($_GET[$key])) $_GET[$key] = $value;
						$i++;
					}
					return $route['action'];
				}
				else if ($found_match === false) throw new Exception('Manual route preg_match failed: ' . $route['regex']);
			}
		}
		return false;
	}
	
	public static function simple_route($route, $action, $after_auto = false) 
	{
		//TODO: Simple Routes
		static::$custom_routes[] = array('regex' => $route, 'action' => $action, 'after_auto' => $after_auto);
	}
	
	public static function regex_route($route, $action, $after_auto = false) 
	{
		static::$custom_routes[] = array('regex' => $route, 'action' => $action, 'after_auto' => $after_auto);
	}
	
}