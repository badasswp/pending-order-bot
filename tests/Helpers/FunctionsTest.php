<?php

namespace PendingOrderBot\Tests\Helpers;

use WP_Mock\Tools\TestCase;

require_once __DIR__ . '/../../inc/Helpers/functions.php';

/**
 * @covers pbot_get_settings
 */
class FunctionsTest extends TestCase {
	public function test_pbot_get_settings() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'pending_order_bot', [] )
			->andReturn(
				[
					'send_text' => true,
				]
			);

		$is_send_text_enabled = pbot_get_settings( 'send_text', [] );

		$this->assertTrue( $is_send_text_enabled );
	}
}
