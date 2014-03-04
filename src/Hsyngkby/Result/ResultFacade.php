<?php namespace Hsyngkby\Result;

use Illuminate\Support\Facades\Facade;

class ResultFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'result';
	}

}