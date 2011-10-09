<?php
/**
 * The router uses a request variable to figure out which controller to load
 * and method to call.  It also creates GET variables using the extra non-route 
 * information like so:
 * 
 * http://www.example.com/admin/users/view/id/1000
 *   \Router::$route = "/admin/users/view/id/1000"
 *   \Router::$controller_path = "[APPDIR]controllers/admin/"
 *   \Router::$controller_file = "users.php"
 *   \Router::$controller_namespace = "\Controllers\admin"
 *   \Router::$controller_class = "Users"
 *   \Router::$controller_method = "view"
 *   $_GET['id'] = 1000
 */
class Router {

	private static $route;

	public static $manual_routes = array();
	public static $controller_path;
	public static $controller_file;
	public static $controller_namespace;
	public static $controller_class;
	public static $controller_method;

	public static function route()
	{
		self::get_route_from_request();
		self::check_manually_defined_routes();
		self::parse_route_string();
		self::load_controller_and_call_method();
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
	
	private function get_route_from_request()
	{
		self::$route = isset($_GET['r']) ? $_GET['r'] : '';
		self::$route = trim(self::$route, '/');
	}
	
	/**
	 * Checks to see if a route was explicity defined.  Normally this is
	 * done by a configuration file setting the Router::$manual_routes array.
	 */
	private function check_manually_defined_routes()
	{
		foreach (self::$manual_routes as $manual_route)
		{
			if (substr(self::$route, 0, strlen($manual_route['route'])) == $manual_route['route'])
			{
				//Appends the extra stuff from self::$route onto $controller
				self::$route = $manual_route['controller'] . substr(self::$route,strlen($manual_route['route']));
				return;
			}
		}
	}
	
	/**
	 * Gets information from the route string that is needed to load the
	 * controller and call the method.  It may also set GET variables if any were
	 * included in the URL.
	 */
	private function parse_route_string()
	{
		if (self::$route == '') $route_parts = array();
		else $route_parts = explode('/', self::$route);
		
		self::$controller_path = APPDIR . 'controllers/';
		self::$controller_namespace = '\\Controllers\\';
		
		//Find the controller file
		if (count($route_parts) != 0)
		{
			self::$controller_file = array_shift($route_parts);
			while (!file_exists(self::$controller_path . self::$controller_file . '.php'))
			{
				if (count($route_parts) > 0)
				{
					self::$controller_path .= self::$controller_file . '/';
					self::$controller_namespace .= self::$controller_file . '\\';
					self::$controller_file = array_shift($route_parts);
					self::$controller_file = str_replace('..', '.', self::$controller_file);
				}
				else //We are out of $route_parts and we haven't found the controller file
				{
					self::set_404_request();
					return;					
				}
			}
		}
		else //The route is empty, use the 'Main' controller
		{
			self::$controller_file = 'main';
		}

		self::$controller_class = ucfirst(self::$controller_file);
		self::$controller_file .= '.php';

		//If the number of route_parts is odd, then it contains a method
		//otherwise it only contains variables, so show the index method
		if (count($route_parts) > 0 && count($route_parts) % 2 == 1)
			self::$controller_method = array_shift($route_parts);
		else
			self::$controller_method = 'index';
		
		//Any leftover route parts become GET variables
		self::set_get_variables($route_parts);
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
	 * Sets the self::$controller_ variables so that the
	 * load_controller_and_call_method function will show the 404 page.
	 */
	private function set_404_request()
	{
		//If we're looking for the 404 controller here, it means we didn't find it 
		//the first time, so just echo a message and give up.
		if (self::$controller_path == APPDIR . 'controllers/' && self::$controller_file == '404.php')
		{
			echo "404 Not Found";
			die();
		}
				
		header("HTTP/1.0 404 Not Found");
		self::$controller_path = APPDIR . 'controllers/';
		self::$controller_namespace = '\\Controllers\\';
		self::$controller_file = '404.php';
		self::$controller_method = 'index';
		self::$controller_class = '_404';
	}
	
	/**
	 * Creates an instance of the requested controller and calls the requested
	 * method in that controller.
	 */
	private function load_controller_and_call_method()
	{
		echo "lcacm";
		if (!file_exists(self::$controller_path . self::$controller_file)) 
		{
			self::set_404_request();
			self::load_controller_and_call_method();
			return;
		}
		
		include self::$controller_path . self::$controller_file;
		
		$class = self::$controller_namespace . self::$controller_class;
		$controller = new $class;

		if (method_exists($controller, self::$controller_method))
			$controller->{self::$controller_method}();
		elseif (method_exists($controller, 'm_' . self::$controller_method))
			$controller->{'m_' . self::$controller_method}();
		else
		{
			self::set_404_request();
			self::load_controller_and_call_method();
		}
	}

}