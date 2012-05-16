<?php
namespace Services\System;

class Session_Test extends \PHPUnit_Framework_TestCase 
{
	public static function setUpBeforeClass()	{	if (!isset($_SESSION)) @session_start(); }
	public static function tearDownBeforeClass()	{	}
	
	protected function setUp() { $_SESSION = array(); }
	protected function tearDown() {	$_SESSION = array(); }

	public function test_set()
	{
		Session::set('one', 'two');
		$this->assertEquals($_SESSION[APPID]['one'], 'two');
		
		Session::set('three', array('four','five'));
		$this->assertEquals($_SESSION[APPID]['three'][0], 'four');
		$this->assertEquals($_SESSION[APPID]['three'][1], 'five');
		
		Session::set('six', array('seven' => 'eight'));
		$this->assertEquals($_SESSION[APPID]['six']['seven'], 'eight');
	}
	
	public function test_get() {
		$_SESSION[APPID] = array(
			'one' => 'two',
			'three' => array('four', 'five'),
			'six' => array('seven' => 'eight')
		);
		
		$one = Session::get('one');
		$this->assertEquals($one, 'two');
		
		$three = Session::get('three');
		$this->assertEquals($three[0], 'four');
		$this->assertEquals($three[1], 'five');
		
		$six = Session::get('six');
		$this->assertEquals($six['seven'], 'eight');
		
		$this->assertNull(Session::get('nine'));
	}
	
	public function test_reference()
	{
		$session = &Session::reference();
		$session['foo'] = 'bar';
		$this->assertEquals($_SESSION[APPID]['foo'], 'bar');
	}

}