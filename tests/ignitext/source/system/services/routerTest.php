<?php
namespace Controllers;

class Fake_Controller {
	public static function fake_action() {}
}

class _404 {
	public static function index() {}
}

namespace Services\System;

use \Services\System\File_System as FS;
use \Mocks\Services\System\File_System as MFS;

class Router_Test extends \PHPUnit_Framework_TestCase 
{	
	public static function setUpBeforeClass()	{	}
	public static function tearDownAfterClass()	{	}
	
	protected function setUp() { }
	protected function tearDown() 
	{ 
		Router::$custom_routes = array();
		Router::$_404 = '\Controllers\_404::index';
		\Patchwork\undoAll();
	}
	
	protected static function get_method($name) {
		$class = new \ReflectionClass('\Services\System\Router');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	public function test_route_found()
	{
		\Patchwork\replace('Services\System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('Services\System\Router::automatic_routes', function() { return '\Controllers\fake_controller::fake_action'; });
		Router::route('anything');
		$this->assertEquals(Router::$action, '\Controllers\fake_controller::fake_action');
	}
	
	public function test_route_found404()
	{
		\Patchwork\replace('Services\System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('Services\System\Router::automatic_routes', function() { return '\Controllers\NA_Controller::NA_action'; });
		Router::route('anything');
		$this->assertEquals(Router::$action, '\Controllers\_404::index');
	}
	
	public function test_route_nothingfound()
	{
		Router::$_404 = 'hide 404 controller';
		\Patchwork\replace('Services\System\Router::custom_routes', function() { return false; });
		\Patchwork\replace('Services\System\Router::automatic_routes', function() { return '\Controllers\NA_Controller::NA_action'; });
		\Patchwork\replace('Services\System\Router::die_404', function() { return 'dead'; });
		$ret = Router::route('anything');
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
		\Patchwork\replace('Services\System\Router::automatic_routes', function() { return false; });
	
		Router::simple_route('%test/123%', '\Controllers\fake_controller::fake_action');
		Router::route('test/123');
		$this->assertEquals(Router::$action, '\Controllers\fake_controller::fake_action');
		
		Router::route('doesnotexist');
		$this->assertEquals(Router::$action, '\Controllers\_404::index');
	}
	
	public function test_simple_route()
	{
		Router::simple_route('%test/123%', 'one::two');
		$expected = array('regex' => '%test/123%', 'action' => 'one::two', 'after_auto' => false);
		$this->assertEquals(Router::$custom_routes[0], $expected);
		
		Router::simple_route('%test/four/five/six%', 'seven\eight::nine', true);
		$expected = array('regex' => '%test/four/five/six%', 'action' => 'seven\eight::nine', 'after_auto' => true);
		$this->assertEquals(Router::$custom_routes[1], $expected);
	}
	
	public function test_regex_route()
	{
		$this->markTestIncomplete();
	}
	
}