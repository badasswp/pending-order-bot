<?php

namespace PendingOrderBot\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Services\Scheduler;
use PendingOrderBot\Abstracts\Service;

/**
 * @covers \PendingOrderBot\Services\Scheduler::register
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
}
