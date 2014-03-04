<?php namespace Hsyngkby\Result;

use Illuminate\Support\ServiceProvider;

class ResultServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		// Register the package
		$this->package('hsyngkby/result', 'result', __DIR__.'/../../');

		// Register IoC bindings
		$this->registerBindings();

		// Shortcut so developers don't need to add an Alias in app/config/app.php
		if ($alias = $this->app['config']->get('result::alias', 'Result'))
		{
			$this->app->booting(function() use ($alias)
			{
				$loader = \Illuminate\Foundation\AliasLoader::getInstance();

				$loader->alias($alias, '\Hsyngkby\Result\ResultFacade');
			});
		}

		// Include various files
		require __DIR__ . '/../../helpers.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Register IoC bindings
	 * @return void
	 */
	public function registerBindings()
	{
		$this->app->singleton('result', function($app)
		{
			return new Result($app['session'], $app['config']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}