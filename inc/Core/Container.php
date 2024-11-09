<?php
/**
 * Container class.
 *
 * This class is responsible for registering the
 * plugin services.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Core;

use PendingOrderBot\Services\Admin;
use PendingOrderBot\Services\Boot;
use PendingOrderBot\Services\Routes;
use PendingOrderBot\Interfaces\Kernel;

class Container implements Kernel {
	/**
	 * Services.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed[]
	 */
	public static array $services = [];

	/**
	 * Prepare Singletons.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::$services = [
			Admin::class,
		];
	}

	/**
	 * Register Service.
	 *
	 * Establish singleton version for each Service
	 * concrete class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( static::$services as $service ) {
			( $service::get_instance() )->register();
		}
	}
}
