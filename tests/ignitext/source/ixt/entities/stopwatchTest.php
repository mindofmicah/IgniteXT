<?php
namespace Entities\IXT;

class Stopwatch_Test extends \PHPUnit_Framework_TestCase {

	protected $stopwatch;

	protected function setUp() 
	{
		date_default_timezone_set('America/Chicago');
		$this->stopwatch = new Stopwatch;
	}

	protected function tearDown() {	}

	public function test_mark() 
	{
		$this->stopwatch->mark('one');
		$this->stopwatch->mark('two');
		$marks = $this->stopwatch->get_marks();
		$this->assertArrayHasKey('one',$marks);
		$this->assertArrayHasKey('two',$marks);
	}

	public function test_elapsed_time() 
	{
		$this->stopwatch->mark('one');
		usleep(50000); //five hundreths of a second
		$this->stopwatch->mark('two');
		$elapsed = $this->stopwatch->elapsed_time('one','two');
		$this->assertGreaterThan(0.01, $elapsed);
		$this->assertLessThan(0.10, $elapsed);
	}

}