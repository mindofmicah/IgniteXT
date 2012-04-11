<?php
namespace System;

class Router_Test extends \PHPUnit_Framework_TestCase 
{
	protected static $ref;
	protected static $ref_custom_routes;
	
	public static function setUpBeforeClass()	
	{	
		self::$ref = new \ReflectionClass('\System\Router');
		self::$ref_custom_routes = self::$ref->getProperty('custom_routes');
		self::$ref_custom_routes->setAccessible(true);
	}
	public static function tearDownBeforeClass()	{	}
	
	protected function setUp() {  }
	protected function tearDown() 
	{ 
		//The custom_routes property is public so I don't have to clear it this way,
		//but I'm keeping this code around for when I need to write a test that uses
		//static protected properties.
		self::$ref_custom_routes->setValue(array()); 
	}

	public function test_route()
	{
		$this->markTestIncomplete();
	}
	
	public function test_route__404()
	{
		$this->markTestIncomplete();
	}
	
	public function test_automatic_routes()
	{
		$this->markTestIncomplete();
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