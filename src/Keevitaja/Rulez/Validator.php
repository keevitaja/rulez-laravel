<?php namespace Keevitaja\Rulez;

use Illuminate\Validation\Factory;

class Validator {

	protected $factory;
	protected $errors;

	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	public function validate($input, $rules)
	{
		$validation = $this->factory->make($input, $rules);

		if ($validation->fails())
		{
			$this->errors = $validation->messages();

			return false;
		}

		return true;
	}

	public function getErrors()
	{
		return $this->errors;
	}
}