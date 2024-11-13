<?php

namespace PendingOrderBot\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Services\Scheduler;
use PendingOrderBot\Abstracts\Service;

/**
 * @covers \PendingOrderBot\Services\Scheduler::register
 * @covers \PendingOrderBot\Services\Scheduler::schedule_reminders
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
					'display'  => 'Default'
				]
			]
		);

		$this->assertSame(
			$schedules,
			[
				'Default' => [
					'interval' => HOUR_IN_SECONDS,
					'display'  => 'Default'
				],
				'Pending Orders' => [
					'interval' => THREE_DAYS_TIME,
					'display'  => 'Pending Orders'
				]
			]
		);
		$this->assertConditionsMet();
	}
}
