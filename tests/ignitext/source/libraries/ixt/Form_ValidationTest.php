<?php
namespace Libraries\IXT;

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

	/**
	 * @todo Implement testSet_rules().
	 */
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

	/**
	 * @todo Implement testValidate().
	 */
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
		echo $this->form->get_errors();
		$this->assertTrue( $form_valid );
		$this->assertTrue( $this->form->checked() );
		$this->assertTrue( $this->form->valid() );
		$this->assertTrue( $this->form->checked_valid() );
		$this->assertFalse( $this->form->checked_invalid() );
	}

	/**
	 * @todo Implement testRule_parts().
	 */
	public function testRule_parts() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGet_value().
	 */
	public function testGet_value() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGet_error().
	 */
	public function testGet_error() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGet_errors().
	 */
	public function testGet_errors() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testForm_value().
	 */
	public function testForm_value() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testForm_select().
	 */
	public function testForm_select() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testForm_check().
	 */
	public function testForm_check() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
						'This test has not been implemented yet.'
		);
	}

}

?>
