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

	public static $url_path;
	public static $controller_map;
	public static $manual_routes = array();
	public static $max_search_depth = 3;
	
	/**
	 * Parses the URL to determine which controller and method should be used
	 * to display the page, then calls that method.
	 */
	public static function route()
	{
		$url_path = static::get_url_path();
		$controller_map = static::manual_route($url_path);
		if ($controller_map === false)
			$controller_map = static::get_controller_map($url_path);
		
		//Any leftover route parts become GET variables
		static::set_get_variables($controller_map->leftovers);
		
		//If the method doesn't exist, try prefixing it with "m_".  This is useful
		//if you want to have an action named "list" but PHP won't allow you to have
		//a method named "list".
		if (!method_exists($controller_map->fully_qualified_class, $controller_map->action))
			$controller_map->action = 'm_' . $controller_map->action;
		
		//If the method doesn't exist, get the 404 controller.
		if (!method_exists($controller_map->fully_qualified_class, $controller_map->action))
			$controller_map = static::get_404_controller_map();
		
		//The 404 controller doesn't exist, fail gracefully.
		if (!method_exists($controller_map->fully_qualified_class, $controller_map->action))
		{
			header("HTTP/1.0 404 Not Found");
			echo "404 Not Found";
			die();
		}
		
		static::$controller_map = $controller_map;
		call_user_func($controller_map->fully_qualified_method);
	}
	
	static function get_url_path()
	{
		$url_path = isset($_GET['r']) ? $_GET['r'] : '';
		$url_path = trim($url_path, '/');
		return $url_path;
	}
	
	/**
	 * Gets the controller map using the specified route string.
	 * 
	 * @param string $url_path
	 * @return Controller_Map $controller_map
	 */
	public function get_controller_map($url_path)
	{
		$url_parts = explode('/', $url_path);
		if (!is_array($url_parts) || count($url_parts)==0) return false;
		
		//$search_dir, $is_package
		$search_dir_list = array(
			array(APPDIR, true),
			array(SHRDIR, true),
			array(APPDIR, false),
			array(SHRDIR, false)
		);
		
		foreach ($search_dir_list as $key => $search_dir_info)
		{
			list($search_dir, $is_package) = $search_dir_info;
			
			$controller_map = new \System\Controller_Map();
			$url_parts_copy = $url_parts;
			$depth = 0;
			
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
			$last_good_dirs[$key]['dir'] = $current_dir;
			$last_good_dirs[$key]['namespace'] = $namespace;
			$last_good_dirs[$key]['leftovers'] = $url_parts_copy;
			
			while (!empty($url_parts_copy) && $url_parts_copy[0] != '')
			{
				if ($depth > static::$max_search_depth) break;
				$url_piece = array_shift($url_parts_copy);
				
				if (file_exists($current_dir . $url_piece . '.php'))
				{
					$controller_map->dir = $current_dir;
					$controller_map->file = $url_piece . '.php';
					$controller_map->namespace = $namespace;
					$controller_map->package = $package;
					$controller_map->controller = $url_piece;
					$controller_map->action = static::get_action($controller_map, $url_parts_copy);
					$controller_map->leftovers = $url_parts_copy;
					return $controller_map;
				}
				
				$current_dir .= $url_piece . '/';
				$namespace .= $url_piece . '\\';
								
				if (is_dir($current_dir)) 
				{
					$last_good_dirs[$key]['dir'] = $current_dir;
					$last_good_dirs[$key]['namespace'] = $namespace;
					$last_good_dirs[$key]['leftovers'] = $url_parts_copy;
				}
				else break;
				
				$depth++;
			}
		}
			
		//The controller file was not found, try index.php in the last good directories
		foreach ($last_good_dirs as $last_good_dir)
		{
			if (file_exists($last_good_dir['dir'] . 'index.php'))
			{
				$controller_map = new \System\Controller_Map();
				$controller_map->dir = $last_good_dir['dir'];
				$controller_map->file = 'index.php';
				$controller_map->namespace = $last_good_dir['namespace'];
				$controller_map->package = $package;
				$controller_map->controller = 'index';
				$controller_map->action = static::get_action($controller_map, $last_good_dir['leftovers']);
				$controller_map->leftovers = $last_good_dir['leftovers'];
				return $controller_map;
			}
		}

		return false;
	}

	private function get_action($controller_map, &$route_parts)
	{
		if (!is_array($route_parts) || count($route_parts) == 0) return 'index';
		
		if (method_exists($controller_map->fully_qualified_class, $route_parts[0]))
			$action = array_shift($route_parts);
		else if (method_exists($controller_map->fully_qualified_class, 'm_' . $route_parts[0]))
			$action = 'm_' . array_shift($route_parts);
		else 
			$action = 'index';
			
		return $action;
	}
	
	/**
	 * Turns remaining $url_parts into GET variables.
	 * 
	 * @param array $url_parts
	 */
	private function set_get_variables($url_parts)
	{
		if (!is_array($url_parts) || count($url_parts) == 0) return;
		
		//If route parts is odd, pop off the last value because its supposed to be even
		if (count($url_parts) % 2 == 1) array_pop($url_parts);

		while (count($url_parts) > 0)
		{
			$key = array_shift($url_parts);
			$value = array_shift($url_parts);
			if (!isset($_GET[$key])) $_GET[$key]=$value;
		}
	}
	
	/**
	 * Gets the controller map for the 404 page.
	 * 
	 * @return Controller_Map $controller_map
	 */
	public function get_404_controller_map()
	{
		$controller_map = new \System\Controller_Map();
		$controller_map->dir = APPDIR . 'controllers/';
		$controller_map->file = '404.php';
		$controller_map->namespace = '\\Controllers\\';
		$controller_map->controller = '_404';
		$controller_map->action = 'index';
		return $controller_map;
	}
	
	public function manual_route($url_path)
	{
		foreach(static::$manual_routes as $manual_route)
		{
			list($regex,$func) = $manual_route;
			if (preg_match($regex, $url_path, $matches)) return $func($matches);
		}
		return false;
	}
	
}