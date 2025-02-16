<?php
/**
 * Twilio class.
 *
 * This concrete class acts as a wrapper around the
 * TwilioClient implementation.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Clients;

use PendingOrderBot\Interfaces\Client;
use Twilio\Rest\Client as TwilioClient;

class Twilio implements Client {
	/**
	 * Send Text Message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from    Sender Number/E-mail.
	 * @param string $to      Recipient Number/E-mail.
	 * @param string $message Message.
	 *
	 * @return void
	 */
	public function send( $from, $to, $message ): void {
		$this->get_twilio_client()->messages->create(
			$to,
			[
				'from' => $from,
				'body' => $message,
			]
		);
	}

	/**
	 * Set up.
	 *
	 * @since 1.0.2
	 *
	 * @return TwilioClient
	 */
	protected function get_twilio_client(): TwilioClient {
		$sid   = pbot_get_settings( 'twilio_sid' );
		$token = pbot_get_settings( 'twilio_token' );

		return new TwilioClient( $sid, $token );
	}
}
