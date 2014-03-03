<?php namespace Keevitaja\Rulez;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

class Rulez {

	/**
	 * Validator
	 *
	 * @var Keevitaja\Rulez\Validator;
	 */
	protected $validator;

	/**
	 * Rules
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Rule set identifier
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Constructor
	 *
	 * @param Validator $validator
	 */
	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	/**
	 * Setter for $name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Register new rules
	 *
	 * @param  string   $name
	 * @param  callable $callback
	 *
	 * @return void
	 */
	public function register($name, callable $callback)
	{
		$this->setName($name);

		call_user_func($callback, $this, $name);
	}

	/**
	 * Add new ruless
	 *
	 * @param array $rules
	 * @param string $type
	 */
	public function addRules($rules, $type)
	{
		$this->rules[$this->name][$type] = $rules;
	}

	/**
	 * Add base rules
	 *
	 * @param array $rules
	 */
	public function addBase($rules)
	{
		$this->addRules($rules, 'base');
	}

	/**
	 * Add create rules
	 *
	 * @param array $rules
	 */
	public function addCreate($rules)
	{
		$this->addRules($rules, 'create');
	}

	/**
	 * Add update rules
	 *
	 * @param array $rules
	 */
	public function addUpdate($rules)
	{
		$this->addRules($rules, 'update');
	}

	/**
	 * Combine rules
	 *
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return array
	 */
	public function combineRules($name, $type)
	{
		if ( ! isset($this->rules[$name]['base'])) return $this->rules[$name][$type];

		return array_merge($this->rules[$name][$type], $this->rules[$name]['base']);
	}

	/**
	 * Validate base rules
	 *
	 * @param  string $name
	 * @param  array $input
	 *
	 * @return boolean
	 */
	public function validateBase($name, $input)
	{
		$rules = $this->rules[$name]['base'];

		return $this->validator->validate($input, $rules);
	}

	/**
	 * Validate create rules
	 *
	 * @param  string $name
	 * @param  array $input
	 *
	 * @return boolean
	 */
	public function validateCreate($name, $input)
	{
		$rules = $this->combineRules($name, 'create');

		return $this->validator->validate($input, $rules);
	}

	/**
	 * Validate update rules
	 *
	 * @param  string $name
	 * @param  array $input
	 *
	 * @return boolean
	 */
	public function validateUpdate($name, $input, $exclude = false, $noPassword = false)
	{
		if ($noPassword)
		{
			if (isset($this->rules[$name]['base']['password'])) unset($this->rules[$name]['base']['password']);
			if (isset($this->rules[$name]['create']['password'])) unset($this->rules[$name]['create']['password']);
			if (isset($this->rules[$name]['update']['password'])) unset($this->rules[$name]['update']['password']);
		}

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

	/**
	 * Get validation errors
	 *
	 * @return object
	 */
	public function validationErrors()
	{
		return $this->validator->getErrors();
	}

	/**
	 * Get rules, used in testing.
	 *
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return array
	 */
	public function getRules($name, $type)
	{
		return $this->rules[$name][$type];
	}
}