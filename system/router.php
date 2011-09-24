<?php
class Router {

  var $route;
  var $controller_path;
  var $controller_namespace;
  var $controller_class;
  var $controller_method;

  function __construct()
  {
    $this->route = isset($_GET['r']) ? $_GET['r'] : '';
    $this->route = rtrim($this->route,'/');
  }

  function route()
  {
    //See if there is an explicity defined route
    global $routes;
    foreach ($routes as $route=>$controller)
    {
      if ($this->route==$route)
      {
        $this->route = $controller . substr($this->route,strlen($route));
        break;
      }
    }

    //Find the path and file for the given route
    if ($this->route=='') $route_parts = array();
    else $route_parts = explode('/',$this->route);
    $path = APPDIR . 'controllers/';
    $this->controller_namespace = '\\';

    //If the route is empty, use the 'main' controller
    if (count($route_parts)==0)
      $file = 'main';
    else
      $file = array_shift($route_parts);

    $file = str_replace('..','.',$file);
    while (!file_exists($path.$file.'.php'))
    {
      if (count($route_parts)>0)
      {
        $path .= $file . '/';
        $this->controller_namespace .= $file . '\\';
        $file = array_shift($route_parts);
        $file = str_replace('..','.',$file);
      }
      else
      {
        self::show_404();
      }
    }
    $this->controller_path = $path;
    $this->controller_class = $file;

    //If the number of route_parts is odd, then it contains a method
    //otherwise it only contains variables, so show the index method
    if (count($route_parts)>0 && count($route_parts)%2==1)
      $this->controller_method = array_shift($route_parts);
    else
      $this->controller_method = 'index';

    include($path.$file.'.php');
    $class = 'Controllers'.$this->controller_namespace.$this->controller_class;
    $controller = new $class;

    //Any leftover route parts become get variables
    if (count($route_parts)>0)
    {
      //If route parts is odd, pop off the last value because its supposed to be even
      if (count($route_parts)%2==1) array_pop($route_parts);

      while (count($route_parts)>0)
      {
        $key = array_shift($route_parts);
        $value = array_shift($route_parts);
        if (!isset($_GET[$key])) $_GET[$key]=$value;
      }
    }

    if (method_exists($controller, $this->controller_method))
      $controller->{$this->controller_method}();
    elseif (method_exists($controller, 'm_'.$this->controller_method))
      $controller->{'m_'.$this->controller_method}();
    else
      self::show_404();
  }

  function show_404()
  {
    header("HTTP/1.0 404 Not Found");
    
    //The file or class method doesn't exist, try to show the 404 controller
    if (!file_exists(APPDIR.'controllers/404.php'))
    {
      //404 controller doesn't exist, print message and die
      echo "Page Not Found"; die();
    }
    include(APPDIR.'controllers/404.php');

    $class = 'Controllers\\_404';
    $controller = new $class;

    if (method_exists($controller, 'index'))
      $controller->index();
    else
      echo "Page Not Found";

    die();
  }

}