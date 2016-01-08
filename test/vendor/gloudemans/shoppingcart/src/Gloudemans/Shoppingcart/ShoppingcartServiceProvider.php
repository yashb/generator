<?php namespace Gloudemans\Shoppingcart;

use Illuminate\Support\ServiceProvider;

class ShoppingcartServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['cart'] = $this->app->share(function($app)
		{
			$session = $app['session'];
			$event = $app['events'];
			return new Cart($session, $event);
		});
	}
}