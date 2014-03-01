<?php

use Mockery as m;

class ValidatorTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function test_returns_false_if_validation_fails()
	{
		$mock_factory_methods = m::mock(['fails' => true]);
		$mock_factory_methods->shouldReceive('messages')->once()->andReturn('validation failed');

		$mock_factory = m::mock('Illuminate\Validation\Factory');
		$mock_factory->shouldReceive('make')->with([], [])->once()->andReturn($mock_factory_methods);

		$validator = new Keevitaja\Rulez\Validator($mock_factory);

		$this->assertFalse($validator->validate([], []));
		$this->assertEquals($validator->getErrors(), 'validation failed');
	}

	public function test_returns_true_if_validation_passes()
	{
		$mock_factory_methods = m::mock(['fails' => false]);
		$mock_factory_methods->shouldReceive('messages')->never();

		$mock_factory = m::mock('Illuminate\Validation\Factory');
		$mock_factory->shouldReceive('make')->with([], [])->once()->andReturn($mock_factory_methods);

		$validator = new Keevitaja\Rulez\Validator($mock_factory);

		$result = $validator->validate([], []);

		$this->assertTrue($result);
	}
}