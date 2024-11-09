<?php
/**
 * Twilio class.
 *
 * This class acts as a wrapper around the Twilio REST
 * implementation for ease of use.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Clients;

use PendingOrderBot\Interfaces\Client;
use Twilio\Rest\Client as TwilioClient;

class Twilio implements Client {
	/**
	 * Client Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var TwilioClient
	 */
	private TwilioClient $client;

	/**
	 * Set up.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sid   Twilio Account SID.
	 * @param string $token Twilio Token.
	 */
	public function __construct( $sid, $token ) {
		$this->client = new TwilioClient( $sid, $token );
	}

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
		$this->client->messages->create(
			$to,
			[
				'from' => $from,
				'body' => $message,
			]
		);
	}
}
