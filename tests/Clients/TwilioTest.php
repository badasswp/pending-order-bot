<?php

namespace PendingOrderBot\Tests\Clients;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Clients\Twilio;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Api\V2010\Account\MessageList as TwilioMessages;

/**
 * @covers \PendingOrderBot\Clients\Twilio::get_twilio_client
 * @covers \PendingOrderBot\Clients\Twilio::send
 * @covers pbot_get_settings
 */
class TwilioTest extends TestCase {
	public Twilio $twilio;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->twilio = new Twilio();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_twilio_client_returns_instance_of_twilio_client() {
		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->times( 2 )
			->with( 'pending_order_bot', [] )
			->andReturn(
				[
					'twilio_sid'   => 1,
					'twilio_token' => 'a8g2lagjekied9',
				]
			);

		$this->assertInstanceOf( TwilioClient::class, $twilio->get_twilio_client() );
	}

	public function test_twilio_client_sends_text_message() {
		$client = Mockery::mock( TwilioClient::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$client->messages = Mockery::mock( TwilioMessages::class )->makePartial();
		$client->messages->shouldAllowMockingProtectedMethods();

		$client->messages->shouldReceive( 'create' )
			->with(
				'+1(234)567890',
				[
					'from' => 'john@doe.com',
					'body' => 'You have abandoned cart orders.',
				]
			);

		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();

		$twilio->shouldReceive( 'get_twilio_client' )
			->andReturn( $client );

		$twilio->send( 'john@doe.com', '+1(234)567890', 'You have abandoned cart orders.' );

		$this->assertConditionsMet();
	}
}
