<?php
namespace Libraries;

error_reporting(E_ALL ^ E_NOTICE);
require_once dirname(__FILE__) . '/../../../../ignitext/source/libraries/ixt_validation.php';

class IXT_ValidationTest extends \PHPUnit_Framework_TestCase 
{
	protected function setUp() { }
	protected function tearDown() {	}

	//4 assertions
	public function testRequired() 
	{		
		$test_data = array(
			array(null, false),
			array('', false),
			array('something', true),
			array(0, true)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::required($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}

	//13 assertions
	public function testEmail() 
	{		
		$test_data = array(
			array(null, false),
			array('brian@websiteduck.com', true),
			array('brian@website-duck.com', true),
			array('bri\'an@websiteduck.com', true),
			array('brian@duck.museum', true),
			array('bri+an@websiteduck.com', true),
			array('brian@websiteduck.com.', false),
			array('brian@websiteduck.c', false),
			array('brian@websiteduck.-com', false),
			array('brian@website\'duck.com', false),
			array('brian@duck.mu-seum', false),
			array('brian@website+duck.com', false),
			array('brian@\u008fwebsiteduck.com', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::email($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	public function testEmail_List()
	{
		$test_data = array(
			array(null, false),
			array('brian@websiteduck.com', true),
			array('brian@website-duck.com', true),
			array('bri\'an@websiteduck.com', true),
			array('brian@duck.museum', true),
			array('bri+an@websiteduck.com', true),
			array('brian@websiteduck.com,brian@website-duck.com,bri\'an@websiteduck.com,brian@duck.museum,bri+an@websiteduck.com',true),
			array('brian@websiteduck.com.', false),
			array('brian@websiteduck.c', false),
			array('brian@websiteduck.-com', false),
			array('brian@website\'duck.com', false),
			array('brian@duck.mu-seum', false),
			array('brian@website+duck.com', false),
			array('brian@\u008fwebsiteduck.com', false),
			array('brian@websiteduck.com.,brian@websiteduck.c,brian@websiteduck.-com',false),
			array('brian@websiteduck.com,brian@websiteduck.-com', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::email_list($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	//24 assertions
	public function testInteger() 
	{
		$test_data = array(
			array(null, false),
			array(0, true),
			array('-1', true),
			array('1', true),
			array('10', true),
			array('1986', true),
			array('1234567890', true),
			array('2147483647', true), //Max integer
			array(0, true),
			array(-1, true),
			array(1, true),
			array(10, true),
			array(1986, true),
			array(1234567890, true),
			array(2147483647, true), //Max integer
			array('a', false),
			array('0.5', false),
			array('one', false),
			array('9.99', false),
			array('+1', false),
			array('1e5', false),
			array('0xA', false),
			array(-1.5, false),
			array(1.5, false)
		);
		
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::integer($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	//24 assertions
	public function testNumeric() 
	{
		$test_data = array(
			array(null, false),
			array(0, true),
			array('-1', true),
			array('1', true),
			array('10', true),
			array('1986', true),
			array('1234567890', true),
			array('9999999999', true), //is_numeric has better support for large numbers
			array(0, true),
			array(-1, true),
			array(1, true),
			array(10, true),
			array(1986, true),
			array(1234567890, true),
			array(9999999999, true),
			array('0.5', true),
			array('9.99', true),
			array('+1', true),
			array('1e5', true),
			array('0xA', true),
			array(-1.5, true),
			array(1.5, true),
			array('a', false),
			array('one', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::numeric($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	//26 assertions
	public function testDecimal() 
	{
		$test_data = array(
			array(null, false),
			array(0, true),
			array('-1', true),
			array('1', true),
			array('10', true),
			array('1986', true),
			array('1234567890', true),
			array('9999999999', true), //is_numeric has better support for large numbers
			array(0, true),
			array(-1, true),
			array(1, true),
			array(10, true),
			array(1986, true),
			array(1234567890, true),
			array(9999999999, true),
			array('0.5', true),
			array('9.99', true),
			array('+1', true),
			array('+1.25', true),
			array('-1.25', true),
			array('1e5', false),
			array('0xA', false),
			array(-1.5, true),
			array(1.5, true),
			array('a', false),
			array('one', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::decimal($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	public function testAlpha()
	{
		$test_data = array(
			array('teststring', true),
			array('TeStStRiNg', true),
			array('TestôString', false),
			array('Test String', false),
			array('Test-String', false),
			array('Test_String', false),
			array('Test\\String', false),
			array('TestString.', false),
			array('1', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::alpha($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	public function testAlphanumeric()
	{
		$test_data = array(
			array('teststring', true),
			array('TeStStRiNg', true),
			array('Test1String', true),
			array('123', true),
			array('TestôString', false),
			array('Test String1', false),
			array('Test-String', false),
			array('Test_String', false),
			array('Test\\String5', false),
			array('TestString.', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::alphanumeric($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	public function testAlphadash()
	{
		$test_data = array(
			array('teststring', true),
			array('TeStStRiNg', true),
			array('Test1String', true),
			array('123', true),
			array('Test-String', true),
			array('Test_String', true),
			array('TestôString', false),
			array('Test String1', false),
			array('Test\\String5', false),
			array('TestString.', false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::alphadash($data), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data);
		}
	}
	
	public function testGreater_Than()
	{
		$test_data = array(
			array('5', '4', true),
			array(5, 4, true),
			array('9.01', 9, true),
			array(1000000, '1', true),
			array('1e2', '99', true),
			array('0xFF', '254', true),
			array(6.9, 7, false),
			array('6.9', '7', false),
			array('0', '1', false),
			array('1e2', '100', false),
			array('0xFF', '256', false),
			array('NaN', 5, false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $param, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::greater_than($data,$param), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data . ' (' . gettype($param) . ') ' . (string)$param);
		}
	}
	
	public function testLess_Than()
	{
		$test_data = array(
			array('0xFF', '256', true),
			array(6.9, 7, true),
			array('6.9', '7', true),
			array('0', '1', true),
			array('5', '4', false),
			array(5, 4, false),
			array('9.01', 9, false),
			array(1000000, '1', false),
			array('1e2', '99', false),
			array('0xFF', '254', false),
			array('1e2', '100', false),
			array('NaN', 5, false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $param, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::less_than($data,$param), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data . ' (' . gettype($param) . ') ' . (string)$param);
		}
	}
	
	public function testRange()
	{
		$test_data = array(
			array('5', '1', '10', true),
			array(7, 6, 8, true),
			array('10', 9, 11, true),
			array(7, '7', 7, true),
			array('0xFF', '254', '256', true),
			array('1e2', '99', '200', true),
			array('1', '2', '2', false),
			array(5000, 1, 10, false),
			array('0xFF', 250, 254, false)
		);
		foreach ($test_data as $data_assert)
		{
			list($data, $from, $to, $assert) = $data_assert;
			$this->assertEquals(IXT_Validation::range($data,$from,$to), $assert, 'Input: ('. gettype($data) . ') ' . (string)$data . ' (' . gettype($param) . ') ' . (string)$param);
		}
	}

}