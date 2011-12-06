<?php
namespace Libraries;

require_once dirname(__FILE__) . '/../../../ignitext/libraries/ixt_validation.php';

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

}