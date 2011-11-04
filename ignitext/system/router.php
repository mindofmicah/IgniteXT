<?php
/**
 * The router uses a request variable to figure out which controller to load
 * and method to call.  It also creates GET variables using the extra non-route 
 * information like so:
 * 
 * http://www.example.com/admin/users/view/id/1000
 *   \Router::$route = "/admin/users/view/id/1000"
 *   \Router::$controller->path = "[APPDIR]controllers/admin/"
 *   \Router::$controller->file = "users.php"
 *   \Router::$controller->namespace = "\Controllers\admin"
 *   \Router::$controller->class = "users"
 *   \Router::$controller->method = "view"
 *   $_GET['id'] = 1000
 */
namespace System;
class Router {

	private static $route;

	public static $manual_routes = array();
	public static $controller;

	/**
	 * Parses the URL to determine which controller and method should be used
	 * to display the page, then calls that method.
	 */
	public static function route()
	{
		//Get route from request
		$route = isset($_GET['r']) ? $_GET['r'] : '';
		$route = trim($route, '/');
		$route = self::manual_route($route);
		
		$controller = self::get_controller_from_route($route);
		
		//Any leftover route parts become GET variables
		self::set_get_variables($controller->leftovers);
		
		//If the method doesn't exist, try prefixing it with "m_".  This is useful
		//if you want to have a page named "list" but PHP won't allow you to have
		//a method named "list".
		if (!method_exists($controller->namespace . $controller->class, $controller->method))
			$controller->method = 'm_' . $controller->method;
		
		//The method doesn't exist.  Get the 404 controller.
		if (!method_exists($controller->namespace . $controller->class, $controller->method))
			$controller = self::get_404_controller();
		
		//The 404 controller doesn't exist.  Fail gracefully.
		if (!method_exists($controller->namespace . $controller->class, $controller->method))
		{
			header("HTTP/1.0 404 Not Found");
			echo "404 Not Found";
			die();
		}
		
		self::$controller = $controller;
		self::$route = $route;
		call_user_func($controller->namespace . $controller->class . '::' . $controller->method);
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
	 * Gets the controller object using the specified route.
	 * 
	 * @param string $route
	 * @return object $controller
	 */
	public function get_controller_from_route($route)
	{
		$controller = new \stdClass();
		if ($route == '') $route_parts = array();
		else $route_parts = explode('/', $route);
		
		$controller->path = APPDIR . 'source/';
		$controller->namespace = '\\Controllers\\';
		
		//Find the controller file
		if (count($route_parts) != 0)
		{
			$controller->file = array_shift($route_parts);
			while (!file_exists($controller->path . 'controllers/' . $controller->file . '.php'))
			{
				if (count($route_parts) > 0)
				{
					$controller->path .= $controller->file . '/';
					$controller->namespace .= $controller->file . '\\';
					$controller->file = array_shift($route_parts);
					$controller->file = str_replace('..', '.', $controller->file);
				}
				else //We are out of $route_parts and we haven't found the controller file
				{
					$controller = self::get_404_controller();
					return $controller;
				}
			}
		}
		else //The route is empty, use the 'index' controller
		{
			$controller->file = 'index';
		}

		$controller->class = strtolower($controller->file);
		$controller->file .= '.php';

		//If the number of route_parts is odd, then it contains a method
		//otherwise it only contains variables, so show the index method
		if (count($route_parts) > 0 && count($route_parts) % 2 == 1)
			$controller->method = array_shift($route_parts);
		else
			$controller->method = 'index';
		
		$controller->leftovers = $route_parts;
		
		return $controller;
	}
	
	private function find_source_controller($route_parts)
	{
		if (!is_array($route_parts) || count($route_parts)==0) return false;
		$controller = new \stdClass();
		$path_found = false;
		$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
		foreach ($check_dirs as $dir)
		{
			$route_parts_copy = $route_parts;
			$controller->path = 'source/controllers/';
			$controller->file = array_shift($route_parts_copy);
			while (!file_exists($dir . $controller->path . $controller->file . '.php') && is_dir($controller->path) && !empty($route_parts_copy))
			{
				$last_good_path = $controller->path;
				$controller->path .= $controller->file . '/';
				$controller->namespace .= $controller->file . '\\';
				$controller->file = array_shift($route_parts_copy);
			}
			if (file_exists($path)) { $path_found = true; break; }
		}
		
	}
	
	private function find_package_controller($route_parts)
	{
		$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
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
	public function get_404_controller()
	{
		$controller = new \stdClass();
		$controller->path = APPDIR . 'controllers/';
		$controller->namespace = '\\Controllers\\';
		$controller->file = '404.php';
		$controller->method = 'index';
		$controller->class = '_404';
		return $controller;
	}
	
}