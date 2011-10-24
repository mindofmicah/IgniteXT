<?php

namespace Libraries;

require_once dirname(__FILE__) . '/../../../ignitext/libraries/ixt_validation.php';

class IXT_ValidationTest extends \PHPUnit_Framework_TestCase {

	protected $validation;

	protected function setUp() {
		$this->validation = new IXT_Validation;
	}
	
	protected function tearDown() {	}

	public function testRequired() {
		$this->assertFalse($this->validation->required(null), 'null');
		
		$test_data = array(
				'' => false,
				'something' => true,
				0 => true
		);
		foreach ($test_data as $data => $assert)
			$this->assertEquals( $this->validation->required($data), $assert, 'Input:'.(string)$data);
	}

	public function testEmail() {
		$this->assertFalse( $this->validation->email(null) );
		
		$test_data = array(
			'brian@websiteduck.com' => true,
			'brian@website-duck.com' => true,
			'bri\'an@websiteduck.com' => true,
			'brian@duck.museum' => true,
			'bri+an@websiteduck.com' => true,
			'brian@websiteduck.com.' => false,
			'brian@websiteduck.c' => false,
			'brian@websiteduck.-com' => false,
			'brian@website\'duck.com' => false,
			'brian@duck.mu-seum' => false,
			'brian@website+duck.com' => false,
			'brian@\u008fwebsiteduck.com' => false
		);
		foreach ($test_data as $data => $assert)
			$this->assertEquals( $this->validation->email($data), $assert, 'Input:'.(string)$data);
	}

}

?>
