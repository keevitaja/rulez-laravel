<?php namespace Keevitaja\Rulez;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Illuminate\Validation\Factory;

class Validator {

	/**
	 * Laravel validation
	 *
	 * @var Illuminate\Validation\Factory
	 */
	protected $factory;

	/**
	 * Errors from factory
	 *
	 * @var object
	 */
	protected $errors;

	/**
	 * Constructor
	 *
	 * @param Factory $factory
	 */
	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * Validate input
	 *
	 * @param  array $input
	 * @param  array $rules
	 *
	 * @return boolean
	 */
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

	/**
	 * Get errors
	 *
	 * @return object
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}