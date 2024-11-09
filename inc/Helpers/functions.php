<?php
/**
 * Functions.
 *
 * This class holds reusable utility functions that can be
 * accessed across the plugin.
 *
 * @package PendingOrderBot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Plugin Options.
 *
 * @since 1.0.0
 *
 * @param string $option   Plugin option to be retrieved.
 * @param string $fallback Default return value.
 *
 * @return mixed
 */
function pbot_get_settings( $option, $fallback = '' ) {
	return get_option( 'pending_order_bot', [] )[ $option ] ?? $fallback;
}
