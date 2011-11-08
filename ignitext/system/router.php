<?php
/**
 * The router uses a request variable to figure out which controller to load
 * and method to call.  It also creates GET variables using the extra non-route 
 * information like so:
 * 
 * http://www.example.com/admin/users/view/id/1000
 *   \Router::$route = "/admin/users/view/id/1000"
 *   \Router::$controller->dir = "[APPDIR]controllers/admin/"
 *   \Router::$controller->file = "users.php"
 *   \Router::$controller->namespace = "\Controllers\admin\"
 *   \Router::$controller->controller = "users"
 *   \Router::$controller->action = "view"
 *   $_GET['id'] = 1000
 */
namespace System;
class Router {

	private static $route_string;

	public static $max_search_depth = 3;
	public static $manual_routes = array();
	public static $route;

	/**
	 * Parses the URL to determine which controller and method should be used
	 * to display the page, then calls that method.
	 */
	public static function route()
	{
		//Get route from request
		$route_string = isset($_GET['r']) ? $_GET['r'] : '';
		$route_string = trim($route_string, '/');
		$route_string = self::manual_route($route_string);
		
		$route = self::get_route($route_string);
		
		//Any leftover route parts become GET variables
		self::set_get_variables($route->leftovers);
		
		//If the method doesn't exist, try prefixing it with "m_".  This is useful
		//if you want to have an action named "list" but PHP won't allow you to have
		//a method named "list".
		if (!method_exists($route->controller->namespace . $route->controller->controller, $route->controller->action))
			$route->controller->action = 'm_' . $route->controller->action;
		
		//The method doesn't exist.  Get the 404 controller.
		if (!method_exists($route->controller->namespace . $route->controller->controller, $route->controller->action))
			$route = self::get_404_route();
		
		//The 404 controller doesn't exist.  Fail gracefully.
		if (!method_exists($route->controller->namespace . $route->controller->controller, $route->controller->action))
		{
			header("HTTP/1.0 404 Not Found");
			echo "404 Not Found";
			die();
		}
		
		self::$route = $route;
		call_user_func($route->controller->namespace . $route->controller->controller . '::' . $route->controller->action);
	}
	
	/**
	 * Adds a route/controller pair to the $manual_routes array.
	 * 
	 * @param string $route
	 * @param string $controller 
	 */
	public static function add_manual_route($route, $controller)
	{
		self::$manual_routes[] = array('route' => $route, 'controller' => $controller);
	}
	
	/**
	 * Returns new route based on a manually defined route. Manual routes are
	 * normally created by using a configuration file that calls 
	 * \Router::add_manual_route()
	 * 
	 * @todo Regular Expressions
	 * 
	 * @param $route
	 * @return $new_route
	 */
	public function manual_route($route)
	{
		$new_route = $route;
		foreach (self::$manual_routes as $manual_route)
		{
			if (substr($route, 0, strlen($manual_route['route'])) == $manual_route['route'])
			{
				//Appends the extra stuff from self::$route onto $controller
				$new_route = $manual_route['controller'] . substr($route,strlen($manual_route['route']));
				return $new_route;
			}
		}
		return $route;
	}
	
	/**
	 * Gets the route object using the specified route string.
	 * 
	 * @param string $route_string
	 * @return object $route
	 */
	public function get_route($route_string)
	{
		$route_parts = explode('/', $route_string);
		if (!is_array($route_parts) || count($route_parts)==0) return false;
		
		$search_dir_list = array(
			array('search_dir' => APPDIR, 'package' => true),
			array('search_dir' => SHRDIR, 'package' => true),
			array('search_dir' => APPDIR, 'package' => false),
			array('search_dir' => SHRDIR, 'package' => false)
		);
		
		foreach ($search_dir_list as $key => $search_dir_info)
		{
			$search_dir = $search_dir_info['search_dir'];
			$package = $search_dir_info['package'];
			
			$route = new \stdClass();
			$route_parts_copy = $route_parts;
			$depth = 0;
			if ($package)
			{
				$package = array_shift($route_parts_copy);
				$current_dir = $search_dir . 'packages/' . $package . '/controllers/';
				$namespace =  '\\Controllers\\' . $package . '\\';
			}
			else
			{
				$current_dir = $search_dir . 'source/controllers/';
				$namespace = '\\Controllers\\';
			}
			
			if (!is_dir($current_dir)) continue;
			
			while (!empty($route_parts_copy))
			{
				if ($depth > self::$max_search_depth) break;
				$route_piece = array_shift($route_parts_copy);
				
				if (file_exists($current_dir . $route_piece . '.php'))
				{
					$route->controller->dir = $current_dir;
					$route->controller->file = $route_piece . '.php';
					$route->controller->namespace = $namespace;
					$route->controller->package = $package;
					$route->controller->controller = $route_piece;
					$route->controller->action = self::get_action($route_parts_copy);
					$route->leftovers = $route_parts_copy;
					return $route;
				}
				
				$current_dir .= $route_piece . '/';
				$namespace .= $route_piece . '\\';
								
				if (is_dir($current_dir)) 
				{
					$last_good_dirs[$key]['dir'] = $current_dir;
					$last_good_dirs[$key]['namespace'] = $namespace;
					$last_good_dirs[$key]['leftovers'] = $route_parts_copy;
				}
				else break;
				
				$depth++;
			}
		}
			
		//The controller file was not found, try index.php in the last good directories
		foreach ($last_good_dirs as $last_good_dir)
		{
			if (file_exists($last_good_dir['path'] . 'index.php'))
			{
				$route->controller->dir = $last_good_dir['dir'];
				$route->controller->file = 'index.php';
				$route->controller->namespace = $last_good_dir['namespace'];
				$route->controller->package = $package;
				$route->controller->controller = 'index';
				$route->controller->action = self::get_action($last_good_dir['leftovers']);
				$route->leftovers = $last_good_dir['leftovers'];
				return $route;
			}
		}

		return false;
	}

	private function get_action(&$route_parts)
	{
		if (count($route_parts) % 2 == 1)
			$action = shift($route_parts);
		else 
			$action = 'index';
		return $action;
	}
	
	/**
	 * Turns remaining $route_parts into GET variables.
	 * 
	 * @param array $route_parts
	 */
	private function set_get_variables($route_parts)
	{
		if (count($route_parts) > 0)
		{
			//If route parts is odd, pop off the last value because its supposed to be even
			if (count($route_parts) % 2 == 1) array_pop($route_parts);

			while (count($route_parts) > 0)
			{
				$key = array_shift($route_parts);
				$value = array_shift($route_parts);
				if (!isset($_GET[$key])) $_GET[$key]=$value;
			}
		}
	}
	
	/**
	 * Gets the controller object for the 404 page.
	 * 
	 * @return object $controller
	 */
	public function get_404_route()
	{
		$route->controller->dir = APPDIR . 'controllers/';
		$route->controller->file = '404.php';
		$route->controller->namespace = '\\Controllers\\';
		$route->controller->controller = '_404';
		$route->controller->action = 'index';
		return $route;
	}
	
}