<?php

use Mockery as m;

class RulezTest extends PHPUnit_Framework_TestCase {

	protected $rules = [
		'base' => [
			'title' => 'required',
			'body' => 'required|min:10'
		],
		'create' => [
			'email' => 'required|email|unique:users'
		],
		'update' => [
			'email' => 'required|email|unique:users,email,%s'
		]
	];

	public function tearDown()
	{
		m::close();
	}

	public function test_register_adds_new_rules()
	{
		$mock_validator = m::mock('Keevitaja\Rulez\Validator');

		$rulez = new Keevitaja\Rulez\Rulez($mock_validator);

		$rules = $this->rules;

		$rulez->register('users', function($rulez) use($rules)
		{
			$rulez->addBase($rules['base']);
			$rulez->addCreate($rules['create']);
			$rulez->addUpdate($rules['update']);
		});

		$this->assertEquals($rules['base'], $rulez->getRules('users', 'base'));
		$this->assertEquals($rules['create'], $rulez->getRules('users', 'create'));
		$this->assertEquals($rules['update'], $rulez->getRules('users', 'update'));
	}

	public function returns_true_if_valitation_passes($type)
	{
		$mock_validator = m::mock('Keevitaja\Rulez\Validator');
		$mock_validator->shouldReceive('validate')->once()->andReturn(true);

		$rulez = new Keevitaja\Rulez\Rulez($mock_validator);

		$rules = $this->rules[$type];

		$rulez->setName('users');

		$addType = 'add' . ucfirst($type);
		$validateType = 'validate' . ucfirst($type);

		$rulez->$addType($rules);

		$this->assertTrue($rulez->$validateType('users', []));
	}

	public function test_returns_true_if_base_valitation_passes()
	{
		$this->returns_true_if_valitation_passes('base');
	}

	public function test_returns_true_if_create_valitation_passes()
	{
		$this->returns_true_if_valitation_passes('create');
	}

	public function test_returns_true_if_update_valitation_passes()
	{
		$this->returns_true_if_valitation_passes('update');
	}

	public function test_rules_get_combined()
	{
		$mock_validator = m::mock('Keevitaja\Rulez\Validator');

		$rulez = new Keevitaja\Rulez\Rulez($mock_validator);

		$rules = $this->rules;

		$rulez->register('users', function($rulez) use($rules)
		{
			$rulez->addBase($rules['base']);
			$rulez->addCreate($rules['create']);
			$rulez->addUpdate($rules['update']);
		});

		$combinedRules = array_merge($rules['base'], $rules['update']);

		$this->assertEquals($combinedRules, $rulez->combineRules('users', 'update'));
	}

	public function test_exclude_replaced_in_validate_update()
	{
		$mock_validator = m::mock('Keevitaja\Rulez\Validator');
		$mock_validator->shouldReceive('validate')->once()->andReturn(true);

		$rulez = new Keevitaja\Rulez\Rulez($mock_validator);

		$rules = $this->rules;

		$rulez->register('users', function($rulez) use($rules)
		{
			$rulez->addBase($rules['base']);
			$rulez->addUpdate($rules['update']);
		});

		$result = $rulez->validateUpdate('users', [], 10);

		$this->assertTrue($result);

		$rules['update']['email'] = sprintf($rules['update']['email'], 10);

		$this->assertEquals($rules['update'], $rulez->getRules('users', 'update'));
	}
}