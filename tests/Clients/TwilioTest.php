<?php

namespace PendingOrderBot\Tests\Clients;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Clients\Twilio;
use Twilio\Rest\Client as TwilioClient;

/**
 * @covers \PendingOrderBot\Clients\Twilio::__construct
 * @covers \PendingOrderBot\Clients\Twilio::send
 */
class TwilioTest extends TestCase {
	public Twilio $twilio;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->twilio = new Twilio( '1048892', 'a8g2lagjekied9' );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_client_is_instance_of_twilio() {
		$this->assertInstanceOf( TwilioClient::class, $this->twilio->get_client() );
	}
}
