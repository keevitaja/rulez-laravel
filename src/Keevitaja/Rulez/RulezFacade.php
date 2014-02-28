<?php namespace Keevitaja\Rulez;

use Illuminate\Support\Facades\Facade;

class RulezFacade extends Facade {

	protected static function getFacadeAccessor() { return 'rulez'; }
}