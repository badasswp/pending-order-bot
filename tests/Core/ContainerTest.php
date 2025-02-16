<?php

namespace PendingOrderBot\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;

use PendingOrderBot\Services\Boot;
use PendingOrderBot\Core\Container;
use PendingOrderBot\Services\Admin;
use PendingOrderBot\Abstracts\Service;
use PendingOrderBot\Services\Scheduler;

/**
 * @covers \PendingOrderBot\Abstracts\Service::get_instance
 * @covers \PendingOrderBot\Core\Container::__construct
 * @covers \PendingOrderBot\Core\Container::register
 * @covers \PendingOrderBot\Services\Admin::register
 * @covers \PendingOrderBot\Services\Boot::register
 * @covers \PendingOrderBot\Services\Scheduler::register
 */
class ContainerTest extends TestCase {
	public Container $container;

	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_container_contains_required_services() {
		$this->container = new Container();

		$this->assertTrue( in_array( Admin::class, Container::$services, true ) );
		$this->assertTrue( in_array( Boot::class, Container::$services, true ) );
		$this->assertTrue( in_array( Scheduler::class, Container::$services, true ) );
	}

	public function test_register() {
		$container = new Container();

		/**
		 * Hack around unset Service::$instances.
		 *
		 * We create instances of services so we can
		 * have a populated version of the Service abstraction's instances.
		 */
		foreach ( Container::$services as $service ) {
			$service::get_instance();
		}

		\WP_Mock::expectActionAdded(
			'admin_init',
			[
				Service::$services[ Admin::class ],
				'register_options_init',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_menu',
			[
				Service::$services[ Admin::class ],
				'register_options_menu',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			[
				Service::$services[ Admin::class ],
				'register_options_styles',
			]
		);

		\WP_Mock::expectActionAdded(
			'init',
			[
				Service::$services[ Boot::class ],
				'register_translation',
			]
		);

		\WP_Mock::expectActionAdded(
			'wp_loaded',
			[
				Service::$services[ Scheduler::class ],
				'schedule_reminders',
			]
		);

		\WP_Mock::expectActionAdded(
			'pending_orders',
			[
				Service::$services[ Scheduler::class ],
				'send_reminders',
			]
		);

		\WP_Mock::expectFilterAdded(
			'cron_schedules',
			[
				Service::$services[ Scheduler::class ],
				'register_cron_schedules',
			]
		);

		$container->register();

		$this->assertConditionsMet();
	}
}
