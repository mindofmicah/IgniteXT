<?php
namespace Controllers;

class Fake_Controller {
	public static function fake_action() {}
}

class _404 {
	public static function index() {}
}

namespace System;

use \System\File_System as FS;
use \Mocks\System\File_System as MFS;

class Router_Test extends \PHPUnit_Framework_TestCase 
{	
	public static function setUpBeforeClass()	{	}
	public static function tearDownAfterClass()	{	}
	
	protected function setUp() { }
	protected function tearDown() 
	{ 
		\System\Router::$custom_routes = array();
		\System\Router::$_404 = '\Controllers\_404::index';
		\Patchwork\undoAll();
	}
	
	protected static function get_method($name) {
		$class = new \ReflectionClass('\System\Router');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	public function test_route_found()
	{
		\Patchwork\replace('System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('System\Router::automatic_routes', function() { return '\Controllers\fake_controller::fake_action'; });
		\System\Router::route('anything');
		$this->assertEquals(\System\Router::$action, '\Controllers\fake_controller::fake_action');
	}
	
	public function test_route_found404()
	{
		\Patchwork\replace('System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('System\Router::automatic_routes', function() { return '\Controllers\NA_Controller::NA_action'; });
		\System\Router::route('anything');
		$this->assertEquals(\System\Router::$action, '\Controllers\_404::index');
	}
	
	public function test_route_nothingfound()
	{
		\System\Router::$_404 = 'hide 404 controller';
		\Patchwork\replace('System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('System\Router::automatic_routes', function() { return '\Controllers\NA_Controller::NA_action'; });
		\Patchwork\replace('System\Router::die_404', function() { return 'dead'; });
		$ret = \System\Router::route('anything');
		$this->assertEquals($ret, 'dead');
	}
		
	public function test_automatic_routes()
	{
		$automatic_routes = static::get_method('automatic_routes');
		$action = $automatic_routes->invoke(null, 'fake_controller/fake_action');
		$this->assertEquals($action, '\Controllers\fake_controller::fake_action');
	}
	
	public function test_automatic_routes_fail()
	{
		$automatic_routes = static::get_method('automatic_routes');
		$action = $automatic_routes->invoke(null, 'na_controller/na_action');
		$this->assertFalse($action);
	}
	
	public function test_custom_routes()
	{
		$this->markTestIncomplete();
	}
	
	public function test_simple_route()
	{
		$this->markTestIncomplete();
	}
	
	public function test_regex_route()
	{
		$this->markTestIncomplete();
	}
	
}