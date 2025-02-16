<?php
/**
 * Scheduler Service.
 *
 * Set up Scheduler script to run over a specific
 * time frame for pending orders.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Services;

use PendingOrderBot\Clients\Twilio;
use PendingOrderBot\Abstracts\Service;
use PendingOrderBot\Interfaces\Client;
use PendingOrderBot\Interfaces\Kernel;

class Scheduler extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_loaded', [ $this, 'schedule_reminders' ] );
		add_action( 'pending_orders', [ $this, 'send_reminders' ] );
		add_filter( 'cron_schedules', [ $this, 'register_cron_schedules' ] );
	}

	/**
	 * Schedule Reminder.
	 *
	 * Set up a reminder for every 24 Hours (1 day)
	 * interval gap.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function schedule_reminders(): void {
		if ( ! wp_next_scheduled( 'pending_orders' ) ) {
			wp_schedule_event( time(), 'Pending Orders', 'pending_orders' );
		}
	}

	/**
	 * Pending Orders Interval.
	 *
	 * Add custom Schedule for Pending Orders to list of WP
	 * Schedules, in this case (1 day).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $schedules WP Schedule List.
	 * @return mixed[]
	 */
	public function register_cron_schedules( $schedules ): array {
		/**
		 * Filter Interval.
		 *
		 * Specify the interval between sending reminders
		 * to WooCommerce users.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $interval Time Interval
		 * @return integer
		 */
		$interval = apply_filters( 'pbot_reminder_interval', DAY_IN_SECONDS );

		$schedules['Pending Orders'] = [
			'interval' => $interval,
			'display'  => esc_html__( 'Pending Orders', 'pending-order-bot' ),
		];

		return $schedules;
	}

	/**
	 * Send Reminders.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_reminders(): void {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		$from    = pbot_get_settings( 'twilio_phone' );
		$message = pbot_get_settings( 'twilio_message' );

		foreach ( $this->get_pending_orders() as $order ) {
			try {
				$this->get_text_client( new Twilio() )->send( $from, $order->get_billing_phone(), $message );
			} catch ( \Exception $e ) {
				$error_msg = sprintf(
					'Error: Unable to send text message, %s',
					$e->getMessage()
				);

				/**
				 * Fires after failed Send.
				 *
				 * Provide error message to user so they can use
				 * as they please.
				 *
				 * @since 1.0.2
				 *
				 * @param string $error_msg Error message.
				 * @return void
				 */
				do_action( 'pbot_send_error', $error_msg );
			}
		}
	}

	/**
	 * Get Pending Orders.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_pending_orders(): array {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return [];
		}

		return wc_get_orders(
			[
				'limit'  => -1,
				'status' => 'pending',
			]
		);
	}

	/**
	 * Get Text Client.
	 *
	 * @since 1.0.2
	 *
	 * @return Client
	 */
	protected function get_text_client( Client $client ): Client {
		/**
		 * Filter Text Client.
		 *
		 * Specify the text client to use to send messages to users
		 * with pending orders.
		 *
		 * @since 1.0.0
		 *
		 * @param Client $client Client instance.
		 * @return Client
		 */
		return apply_filters( 'pbot_text_client', $client );
	}
}
