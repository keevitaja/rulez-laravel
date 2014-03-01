<?php namespace Keevitaja\Rulez;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Illuminate\Support\Facades\Facade;

class RulezFacade extends Facade {

	protected static function getFacadeAccessor() { return 'rulez'; }
}