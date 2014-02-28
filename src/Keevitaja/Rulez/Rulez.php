<?php namespace Keevitaja\Rulez;

class Rulez {

	protected $validator;
	protected $rules = [];
	protected $name;

	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	public function register($name, callable $callback)
	{
		$this->name = $name;

		call_user_func($callback, $this, $name);
	}

	public function addRules($rules, $type)
	{
		$this->rules[$this->name][$type] = $rules;
	}

	public function addBase($rules)
	{
		$this->addRules($rules, 'base');
	}

	public function addCreate($rules)
	{
		$this->addRules($rules, 'create');
	}

	public function addUpdate($rules)
	{
		$this->addRules($rules, 'update');
	}

	public function combineRules($name, $type)
	{
		if ( ! isset($this->rules[$name]['base'])) return $this->rules[$name][$type];

		return array_merge($this->rules[$name][$type], $this->rules[$name]['base']);
	}

	public function validateBase($name, $input)
	{
		$rules = $this->rules[$name]['base'];

		return $this->validator->validate($input, $rules);
	}

	public function validateCreate($name, $input)
	{
		$rules = $this->combineRules($name, 'create');

		return $this->validator->validate($input, $rules);
	}

	public function validateUpdate($name, $input, $exclude = false)
	{
		if ($exclude)
		{
			foreach ($this->rules[$name]['update'] as $field => $rule) 
			{
				$this->rules[$name]['update'][$field] = sprintf($rule, $exclude);
			}
		}

		$rules = $this->combineRules($name, 'update');

		return $this->validator->validate($input, $rules);
	}

	public function validationErrors()
	{
		return $this->validator->getErrors();
	}
}