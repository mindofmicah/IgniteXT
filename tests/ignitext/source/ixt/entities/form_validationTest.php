<?php
namespace Entities\IXT;

class Test_Rule_Class
{
	public static function must_be_five($input, $return_error_message = false)
	{
		if (isset($input) && $input == 5) return true;
		else if ($return_error_message == true) return 'must be five.';
		else return false;
	}
}

class Form_ValidationTest extends \PHPUnit_Framework_TestCase {

	protected $form;

	protected function setUp() { $this->form = new Form_Validation;	}
	protected function tearDown() {	}
	
	public function testCreate_with_rule_class()
	{
		$this->form = new Form_Validation(new Test_Rule_Class);
		$rules = array( array('test','test','must_be_five') );
		$input = array('test' => 'test');
		$this->form->set_rules($rules);
		$this->form->validate($input);
		$this->assertEquals($this->form->get_errors(),'The test field must be five.');
		
		$this->form->reset();
		$input = array('test' => 5);
		$this->form->validate($input);
		$this->assertEquals($this->form->get_errors(),'');
		$this->assertTrue($this->form->valid());
	}

	public function testValid() {	}
	public function testChecked() {	}
	public function testChecked_valid() {	}
	public function testChecked_invalid() {	}

	public function testClear_rules() {
		$rules = array(
			array('name','Name','required'),
			array('number','Number','required|numeric'),
			array('sub[0]','SubZero','required|integer')
		);
		$this->form->set_rules($rules);
		$this->assertEquals(count($this->form->get_rules()),3);
		$this->form->clear_rules();
		$this->assertEquals(count($this->form->get_rules()),0);
	}

	public function testSet_error() {
		$this->assertEquals($this->form->get_errors(),'');
		$this->form->set_error('name','The name field is wrong.');
		$this->assertEquals($this->form->get_errors(),'The name field is wrong.');
	}

	public function testClear_errors() {
		$this->form->set_error('name','The name field is wrong.');
		$this->form->clear_errors();
		$this->assertEquals($this->form->get_errors(),'');
		
		$this->form->set_error('name','The name field is wrong.');
		$this->assertEquals($this->form->get_errors(),'The name field is wrong.');
		$this->form->clear_errors();
		$this->assertEquals($this->form->get_errors(),'');
	}

	public function testSet_delim() {
		$this->form->set_error('name','The name field is wrong.');
		$this->form->set_error('number','The number field is wrong.');
		$this->assertEquals($this->form->get_errors(),'The name field is wrong.The number field is wrong.');
		$this->form->set_delim('<li>','</li>');
		$this->assertEquals($this->form->get_errors(),'<li>The name field is wrong.</li><li>The number field is wrong.</li>');
		$this->form->set_delim('<span>','</span>');
		$this->assertEquals($this->form->get_errors(),'<span>The name field is wrong.</span><span>The number field is wrong.</span>');
	}

	public function testSet_rules() {
		$rules = array(
			array('name','Name','required'),
			array('number','Number','required|numeric'),
			array('sub[0]','SubZero','required|integer')
		);
		$this->form->set_rules($rules);
		
		$expected_rules = array(
			'name' => array('label' => 'Name', 'rules' => 'required'),
			'number' => array('label' => 'Number', 'rules' => 'required|numeric'),
			'sub[0]' => array('label' => 'SubZero', 'rules' => 'required|integer')
		);
		$this->assertEquals($this->form->get_rules(), $expected_rules);
	}

	public function testAdd_rules() {
		$rules = array(
			array('name', 'Name', 'required'),
			array('number', 'Number', 'required|numeric'),
			array('sub[0]', 'SubZero', 'required|integer')
		);
		$this->form->set_rules($rules);
		
		$expected_rules = array(
			'name' => array('label' => 'Name', 'rules' => 'required'),
			'number' => array('label' => 'Number', 'rules' => 'required|numeric'),
			'sub[0]' => array('label' => 'SubZero', 'rules' => 'required|integer')
		);
		$this->assertEquals($this->form->get_rules(), $expected_rules);
		
		$add_rules = array(
			array('time', 'Time', 'required'),
			array('date', 'Date', 'required'),
		);
		$this->form->add_rules($add_rules);
		
		$expected_rules['time'] = array('label' => 'Time', 'rules' => 'required');
		$expected_rules['date'] = array('label' => 'Date', 'rules' => 'required');
		$this->assertEquals($this->form->get_rules(), $expected_rules);
	}

	public function testValidate() {
		$input_array = array(
			'name' => 'Brian',
			'email' => 'brian@websiteduck.com',
			'number' => array('123', '4.9')
		);
		$rules_array = array(
			array('name', 'Name', 'required'),
			array('email', 'Email', 'required|email'),
			array('number[0]', 'Number1', 'required|integer'),
			array('number[1]', 'Number2', 'required|decimal|greater_than[4]')
		);
		$this->form->set_rules($rules_array);
		$form_valid = $this->form->validate($input_array);
		
		$this->assertTrue( $form_valid );
		$this->assertTrue( $this->form->checked() );
		$this->assertTrue( $this->form->valid() );
		$this->assertTrue( $this->form->checked_valid() );
		$this->assertFalse( $this->form->checked_invalid() );
	}

	public function testRule_parts() {
		$reflection_class = new \ReflectionClass("Entities\IXT\Form_Validation");
		$method = $reflection_class->getMethod('rule_parts');
		$method->setAccessible(true);
		
		//Just rule name, no parameters
		$expected_return = array('test123', array());
		$actual_return = $method->invoke($this->form, 'test123');
		$this->assertEquals($actual_return, $expected_return);	
		
		//Rule name + empty parameters
		$expected_return = array('test123', array(''));
		$actual_return = $method->invoke($this->form, 'test123[]');
		$this->assertEquals($actual_return, $expected_return);
		
		//Rule name + parameters
		$expected_return = array('test456', array('one','two'));
		$actual_return = $method->invoke($this->form, 'test456[one,two]');
		$this->assertEquals($actual_return, $expected_return);		
	}

	public function testGet_value() {
		$reflection_class = new \ReflectionClass("Entities\IXT\Form_Validation");
		$method = $reflection_class->getMethod('get_value');
		$method->setAccessible(true);
		
		$input_array = array(
			'one' => 'two', 
			'three' => array('four' => 'five')
		);
		$this->form->validate($input_array);
		$this->assertSame($method->invoke($this->form, 'one'), 'two');
		$this->assertSame($method->invoke($this->form, 'three[four]'), 'five');
		$this->assertSame($method->invoke($this->form, 'three[six]'), '');
	}

	public function testGet_error() {
		$this->markTestIncomplete();
	}

	public function testGet_errors() {
		$this->markTestIncomplete();
	}

	public function testForm_value() {
		$this->markTestIncomplete();
	}

	public function testForm_select() {
		$input_array = array('name' => 'Brian');
		$rules_array = array( array('name', 'Name', 'required')	);
		$this->form->set_rules($rules_array);
		$form_valid = $this->form->validate($input_array);
		
		$this->assertEquals($this->form->form_select('name', 'Brian'), 'selected="selected"');
		$this->assertEquals($this->form->form_select('name', 'NotSelected'), '');
		$this->assertEquals($this->form->form_select('NotExists', 'Anything'), '');
	}

	public function testForm_check() {
		$this->assertEquals($this->form->form_check('default_checkbox', true), 'checked="checked"');
		
		$input_array = array(
			'checkbox1' => '1',
			'checkbox2' => 'anything'
		);
		$form_valid = $this->form->validate($input_array);
		
		$this->assertEquals($this->form->form_check('checkbox1'), 'checked="checked"');
		$this->assertEquals($this->form->form_check('checkbox2'), 'checked="checked"');
		$this->assertEquals($this->form->form_check('checkbox3'), '');
	}

}
