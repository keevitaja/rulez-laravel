<?php

use Mockery as m;

class RulezTest extends PHPUnit_Framework_TestCase {

	protected $rulez;
	protected $validator;

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		parent::setUp();

		$this->validator = m::mock('Keevitaja\Rulez\Validator', [
			'validate' => false,
			'getErrors' => new stdClass()
		]);

		$this->rulez = new Keevitaja\Rulez\Rulez($this->validator);
	}

	public function test_register_rules()
	{
		$uRules = [
			'test' => 'required|min:3'
		];

		$bRules = [
			'text' => 'required',
			'email' => 'required|email'
		];

		$cRules = [
			'test' => 'required',
		];

		$this->rulez->setName('testing');

		$this->rulez->register('testing', function($rulez) use($bRules, $uRules, $cRules)
		{
			$rulez->addBase($bRules);
			$rulez->addUpdate($uRules);
			$rulez->addCreate($cRules);
		});

		$this->assertEquals($bRules, $this->rulez->getRules('testing', 'base'));
		$this->assertEquals($cRules, $this->rulez->getRules('testing', 'create'));
		$this->assertEquals($uRules, $this->rulez->getRules('testing', 'update'));
	}

	public function test_combine_rules()
	{
		$uRules = [
			'test' => 'required|min:3'
		];

		$bRules = [
			'text' => 'required',
			'email' => 'required|email'
		];

		$this->rulez->setName('testing');

		$this->rulez->addBase($bRules);
		$this->rulez->addUpdate($uRules);

		$merged = array_merge($uRules, $bRules);

		$this->assertEquals($this->rulez->combineRules('testing', 'update'), $merged);
	}

	public function validateRules($type)
	{
		$ruleType = 'add' . ucfirst($type);
		$validationType = 'validate' . ucfirst($type);

		$this->validator->shouldReceive('validate')->once()->andReturn(false);

		$this->rulez->setName('testing');

		$rules = [
			'text' => 'required'
		];

		$this->rulez->$ruleType($rules);

		$result = $this->rulez->$validationType('testing', []);

		$this->assertFalse($result);
	}

	public function test_validate_rules_validation()
	{
		$this->validateRules('base');
		$this->validateRules('update');
		$this->validateRules('create');
	}

	public function test_validation_errors()
	{
		$this->validator->shouldReceive('getErrors')->once();

		$this->rulez->validationErrors();
	}
}