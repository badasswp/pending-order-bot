<?php

namespace PendingOrderBot\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Clients\Twilio;
use PendingOrderBot\Abstracts\Service;
use PendingOrderBot\Services\Scheduler;

/**
 * @covers \PendingOrderBot\Services\Scheduler::register
 * @covers \PendingOrderBot\Services\Scheduler::schedule_reminders
 * @covers \PendingOrderBot\Services\Scheduler::register_cron_schedules
 * @covers \PendingOrderBot\Services\Scheduler::get_pending_orders
 * @covers \PendingOrderBot\Services\Scheduler::get_text_client
 * @covers \PendingOrderBot\Services\Scheduler::send_reminders
 * @covers pbot_get_settings
 */
class SchedulerTest extends TestCase {
	public Scheduler $scheduler;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->scheduler = new Scheduler();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'wp_loaded', [ $this->scheduler, 'schedule_reminders' ] );
		\WP_Mock::expectActionAdded( 'pending_orders', [ $this->scheduler, 'send_reminders' ] );
		\WP_Mock::expectFilterAdded( 'cron_schedules', [ $this->scheduler, 'register_cron_schedules' ] );

		$this->scheduler->register();

		$this->assertConditionsMet();
	}

	public function test_schedule_reminders() {
		\WP_Mock::userFunction( 'wp_next_scheduled' )
			->with( 'pending_orders' )
			->andReturn( true );

		$this->scheduler->schedule_reminders();

		$this->assertConditionsMet();
	}

	public function test_schedule_reminders_schedules_event() {
		\WP_Mock::userFunction( 'wp_next_scheduled' )
			->with( 'pending_orders' )
			->andReturn( false );

		\WP_Mock::userFunction( 'wp_schedule_event' )
			->with( time(), 'Pending Orders', 'pending_orders' )
			->andReturn( null );

		$this->scheduler->schedule_reminders();

		$this->assertConditionsMet();
	}

	public function test_register_cron_schedules() {
		define( 'DAY_IN_SECONDS', 24 * 60 * 60 );
		define( 'HOUR_IN_SECONDS', 1 * 60 * 60 );
		define( 'THREE_DAYS_TIME', 3 * 24 * 60 * 60 );

		\WP_Mock::userFunction( 'esc_html__' )
			->once()
			->with( 'Pending Orders', 'pending-order-bot' )
			->andReturn( 'Pending Orders' );

		\WP_Mock::onFilter( 'pbot_reminder_interval' )
			->with( DAY_IN_SECONDS )
			->reply( THREE_DAYS_TIME );

		$schedules = $this->scheduler->register_cron_schedules(
			[
				'Default' => [
					'interval' => HOUR_IN_SECONDS,
					'display'  => 'Default',
				],
			]
		);

		$this->assertSame(
			$schedules,
			[
				'Default'        => [
					'interval' => HOUR_IN_SECONDS,
					'display'  => 'Default',
				],
				'Pending Orders' => [
					'interval' => THREE_DAYS_TIME,
					'display'  => 'Pending Orders',
				],
			]
		);
		$this->assertConditionsMet();
	}

	public function test_schedule_reminder_bails_if_woocommerce_is_not_active() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'is_plugin_active' )
			->once()
			->with( 'woocommerce/woocommerce.php' )
			->andReturn( false );

		$scheduler->send_reminders();

		$this->assertConditionsMet();
	}

	public function test_schedule_reminder_catches_exception_and_applies_action() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();

		$order = Mockery::mock( \WC_Order::class )->makePartial();
		$order->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'is_plugin_active' )
			->once()
			->with( 'woocommerce/woocommerce.php' )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_option' )
			->times( 2 )
			->with( 'pending_order_bot', [] )
			->andReturn(
				[
					'twilio_phone'   => '+1234567890',
					'twilio_message' => 'You have abandoned cart orders.',
				]
			);

		$order->shouldReceive( 'get_billing_phone' )
			->andReturn( '+0987654321' );

		$orders[] = $order;

		$scheduler->shouldReceive( 'get_pending_orders' )
			->andReturn( $orders );

		$scheduler->shouldReceive( 'get_text_client' )
			->with( Mockery::type( Twilio::class ) )
			->andReturn( $twilio );

		$twilio->shouldReceive( 'send' )
			->with(
				'+1234567890',
				'+0987654321',
				'You have abandoned cart orders.'
			)
			->andThrow(
				new \Exception( 'SMS Text API is currently down...' )
			);

		\WP_Mock::expectAction(
			'pbot_send_error',
			'Error: Unable to send text message, SMS Text API is currently down...'
		);

		$scheduler->send_reminders();

		$this->assertConditionsMet();
	}

	public function test_schedule_reminder_passes_correctly() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();

		$order = Mockery::mock( \WC_Order::class )->makePartial();
		$order->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'is_plugin_active' )
			->once()
			->with( 'woocommerce/woocommerce.php' )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_option' )
			->times( 2 )
			->with( 'pending_order_bot', [] )
			->andReturn(
				[
					'twilio_phone'   => '+1234567890',
					'twilio_message' => 'You have abandoned cart orders.',
				]
			);

		$order->shouldReceive( 'get_billing_phone' )
			->andReturn( '+0987654321' );

		$orders[] = $order;

		$scheduler->shouldReceive( 'get_pending_orders' )
			->andReturn( $orders );

		$scheduler->shouldReceive( 'get_text_client' )
			->andReturn( $twilio );

		$twilio->shouldReceive( 'send' )
			->with(
				'+1234567890',
				'+0987654321',
				'You have abandoned cart orders.'
			)
			->andReturn( null );

		$scheduler->send_reminders();

		$this->assertConditionsMet();
	}

	public function test_get_pending_orders_returns_empty_array() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'is_plugin_active' )
			->once()
			->with( 'woocommerce/woocommerce.php' )
			->andReturn( false );

		$orders = $scheduler->get_pending_orders();

		$this->assertSame( $orders, [] );
		$this->assertConditionsMet();
	}

	public function test_get_pending_orders_returns_pending_orders() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'is_plugin_active' )
			->once()
			->with( 'woocommerce/woocommerce.php' )
			->andReturn( true );

		$order = Mockery::mock( \WC_Order::class )->makePartial();

		$order->ID          = 1;
		$order->post_type   = 'product';
		$order->post_status = 'pending';

		$orders[] = $order;

		\WP_Mock::userFunction( 'wc_get_orders' )
			->with(
				[
					'limit'  => -1,
					'status' => 'pending',
				]
			)
			->andReturn( $orders );

		$response = $scheduler->get_pending_orders();

		$this->assertSame( count( $response ), 1 );
		$this->assertConditionsMet();
	}

	public function test_get_text_client_returns_twilio_client() {
		$scheduler = Mockery::mock( Scheduler::class )->makePartial();
		$scheduler->shouldAllowMockingProtectedMethods();

		$twilio = Mockery::mock( Twilio::class )->makePartial();
		$twilio->shouldAllowMockingProtectedMethods();

		\WP_Mock::expectFilter( 'pbot_text_client', $twilio );

		$twilio_client = $scheduler->get_text_client( $twilio );

		$this->assertInstanceOf( Twilio::class, $twilio_client );
		$this->assertConditionsMet();
	}
}
