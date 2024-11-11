<?php

namespace PendingOrderBot\Tests\Clients;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Clients\Twilio;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Api\V2010\Account\MessageList as TwilioMessages;

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

	public function test_client_sends_text_message() {
		$messages = Mockery::mock( TwilioMessages::class )->makePartial();
		$messages->shouldAllowMockingProtectedMethods();

		$client = Mockery::mock( TwilioClient::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();
		$client->messages = $messages;

		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();
		$twilio->client = $client;

		$messages->shouldReceive( 'create' )
			->with(
				'+1(234)567890',
				[
					'from' => 'john@doe.com',
					'body' => 'You have abandoned cart orders.',
				]
			);

		$twilio->client->messages->create(
			'+1(234)567890',
			[
				'from' => 'john@doe.com',
				'body' => 'You have abandoned cart orders.',
			]
		);

		$this->assertConditionsMet();
	}
}
