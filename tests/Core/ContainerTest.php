<?php

namespace PendingOrderBot\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;

use PendingOrderBot\Core\Container;
use PendingOrderBot\Services\Admin;
use PendingOrderBot\Services\Boot;
use PendingOrderBot\Services\Scheduler;

/**
 * @covers \PendingOrderBot\Core\Container::__construct
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
}
