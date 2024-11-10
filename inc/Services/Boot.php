<?php
/**
 * Boot Service.
 *
 * Handle all setup logic before plugin is
 * fully capable.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Services;

use PendingOrderBot\Abstracts\Service;
use PendingOrderBot\Interfaces\Kernel;

class Boot extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_translation' ] );
	}

	/**
	 * Add Plugin text translation.
	 *
	 * @since 1.0.0
	 *
	 * @wp-hook 'init'
	 */
	public function register_translation() {
		load_plugin_textdomain(
			'pending-order-bot',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../../languages'
		);
	}
}
