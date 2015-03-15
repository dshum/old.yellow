<?php namespace LemonTree\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$site = \App::make('site');

		$site->initMicroTime();

		if (file_exists($path = app_path().'/Http/site.php')) {
			include $path;
		}

		if (file_exists(
			$swift_init = app_path()
				.'/../vendor/swiftmailer/swiftmailer/lib/swift_init.php'
		)) {
			require_once $swift_init;
		}

		$this->loadViewsFrom(__DIR__.'/../../views', 'admin');

		$this->publishes([
			__DIR__.'/../../migrations' => $this->app->databasePath().'/migrations',
			__DIR__.'/../../seeds' => $this->app->databasePath().'/seeds',
			__DIR__.'/../../assets' => public_path('packages/lemon-tree/admin'),
		]);

		\DB::enableQueryLog();

		\Cache::extend('file', function($app) {
			return \Cache::repository(
				new \LemonTree\FileStore(
					$app['files'],
					config('cache.stores.file.path')
				)
			);
		});

		\Blade::extend(function($value) {
			return preg_replace('/\{\?(.+)\?\}/', '<?php ${1} ?>', $value);
		});

		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\App::singleton('site', function($app) {
			return new \LemonTree\Site;
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
