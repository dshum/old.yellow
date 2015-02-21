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
		\DB::enableQueryLog();
		
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

		/*
		\App::error(function(\Exception $exception, $code) {
			\Log::error($exception);
			\LemonTree\ErrorMessageUtils::sendMessage($exception);
			if (\Config::get('app.debug') !== true) {
				return \Response::view('error500', array(), 500);
			}
		});
		 */

		/*
		\App::missing(function($exception) {
			return \Response::view('error404', array(), 404);
		});
		 */

		/*
		\Cache::extend('file', function($app) {
			return new \Illuminate\Cache\Repository(
				new \LemonTree\CustomFileStore($app['files'], $app['config']['cache.path'])
			);
		});
		 */

		\Blade::extend(function($value) {
			return preg_replace('/\{\?(.+)\?\}/', '<?php ${1} ?>', $value);
		});

		\Config::set(
			'cartalyst/sentry::groups.model',
			'LemonTree\Models\Group');

		\Config::set(
			'cartalyst/sentry::users.model',
			'LemonTree\Models\User');

		\Config::set(
			'cartalyst/sentry::users.login_attribute',
			'login');

		\Config::set(
			'cartalyst/sentry::user_groups_pivot_table',
			'cytrus_users_groups');

		\Config::set(
			'cartalyst/sentry::throttling.model',
			'LemonTree\Models\Throttle');

		\Config::set(
			'cartalyst/sentry::hasher',
			'sha256');

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
