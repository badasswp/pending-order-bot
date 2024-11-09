<?php
/**
 * Client Interface
 *
 * Establish Client interface to be adopted
 * by different clients.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Interfaces;

interface Client {
	/**
	 * Send Method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from    Sender Number/E-mail.
	 * @param string $to      Recipient Number/E-mail.
	 * @param string $message Message.
	 *
	 * @return void
	 */
	public function send( $from, $to, $message ): void;
}
