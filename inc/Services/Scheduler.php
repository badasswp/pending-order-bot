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
		$schedules['Pending Orders'] = [
			'interval' => DAY_IN_SECONDS,
			'display'  => esc_html__( 'Pending Orders' ),
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
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$from    = pbot_get_settings( 'twilio_phone' );
		$message = pbot_get_settings( 'twilio_message' );
		$client  = $this->get_twilio_client();

		foreach ( $this->get_pending_orders() as $order ) {
			$client->send( $from, $order->get_billing_phone(), $message );
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
		if ( ! class_exists( 'WooCommerce' ) ) {
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
	 * Get Twilio Client.
	 *
	 * @since 1.0.0
	 *
	 * @return Twilio
	 */
	protected function get_twilio_client(): Twilio {
		$sid   = pbot_get_settings( 'twilio_sid' );
		$token = pbot_get_settings( 'twilio_token' );

		return new Twilio( $sid, $token );
	}
}