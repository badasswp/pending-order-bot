<?php
/**
 * Plugin Name: Pending Order Bot
 * Plugin URI:  https://github.com/badasswp/pending-order-bot
 * Description: Send reminders on WooCommerce pending orders.
 * Version:     1.0.1
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: pending-order-bot
 * Domain Path: /languages
 *
 * @package PendingOrderBot
 */

namespace badasswp\PendingOrderBot;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'PBOT_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( PBOT_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'pending-order-bot' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Autoload classes.
require_once PBOT_AUTOLOAD;

// Run plugin.
require_once __DIR__ . '/inc/Helpers/functions.php';
( \PendingOrderBot\Plugin::get_instance() )->run();
