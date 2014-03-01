<?php

use Mockery as m;

class ValidatorTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function test_validation_fails()
	{
		$mValidator = m::mock(['fails' => true, 'messages' => new stdClass()]);
		$mFactory = m::mock('Illuminate\Validation\Factory');

		$mFactory->shouldReceive('make')->once()->andReturn($mValidator);

		$validator = new Keevitaja\Rulez\Validator($mFactory);

		$validate = $validator->validate([], []);

		$this->assertFalse($validate);
	}
}