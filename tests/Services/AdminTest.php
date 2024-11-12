<?php

namespace PendingOrderBot\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Admin\Form;
use PendingOrderBot\Admin\Options;
use PendingOrderBot\Services\Admin;
use PendingOrderBot\Abstracts\Service;

/**
 * @covers \PendingOrderBot\Services\Admin::__construct
 * @covers \PendingOrderBot\Services\Admin::register
 * @covers \PendingOrderBot\Services\Admin::register_options_menu
 * @covers \PendingOrderBot\Services\Admin::register_options_init
 * @covers \PendingOrderBot\Services\Admin::register_options_styles
 * @covers \PendingOrderBot\Engine\Watermarker::__construct
 * @covers \PendingOrderBot\Admin\Options::__callStatic
 * @covers \PendingOrderBot\Admin\Options::get_form_fields
 * @covers \PendingOrderBot\Admin\Options::get_form_notice
 * @covers \PendingOrderBot\Admin\Options::get_form_page
 * @covers \PendingOrderBot\Admin\Options::get_form_submit
 * @covers \PendingOrderBot\Admin\Options::init
 */
class AdminTest extends TestCase {
	public Admin $admin;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->admin = new Admin();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$_POST = [];
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'admin_init', [ $this->admin, 'register_options_init' ] );
		\WP_Mock::expectActionAdded( 'admin_menu', [ $this->admin, 'register_options_menu' ] );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $this->admin, 'register_options_styles' ] );

		$this->admin->register();

		$this->assertConditionsMet();
	}

	public function test_register_options_menu() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 57,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 30,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 0,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'add_menu_page' )
			->with(
				'Pending Order Bot',
				'Pending Order Bot',
				'manage_options',
				'pending-order-bot',
				[ $this->admin, 'register_options_page' ],
				'dashicons-email-alt',
				100
			);

		$this->admin->register_options_menu();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_on_POST() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 57,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 30,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 0,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_on_NONCE() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 57,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 30,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 0,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$_POST = [
			'pending_order_bot_save_settings'  => true,
			'pending_order_bot_settings_nonce' => 'a8jfkgw2h7i',
		];

		\WP_Mock::userFunction( 'wp_unslash' )
			->with( 'a8jfkgw2h7i' )
			->andReturn( 'a8jfkgw2h7i' );

		\WP_Mock::userFunction( 'sanitize_text_field' )
			->with( 'a8jfkgw2h7i' )
			->andReturn( 'a8jfkgw2h7i' );

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8jfkgw2h7i', 'pending_order_bot_settings_action' )
			->andReturn( false );

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_passes() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 95,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 50,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 0,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$_POST = [
			'pending_order_bot_save_settings'  => true,
			'pending_order_bot_settings_nonce' => 'a8jfkgw2h7i',
		];

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->once()
			->with( 'a8jfkgw2h7i', 'pending_order_bot_settings_action' )
			->andReturn( true );

		\WP_Mock::userFunction(
			'wp_unslash',
			[
				'times'  => 7,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'sanitize_text_field',
			[
				'times'  => 7,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'update_option' )
			->once()
			->with(
				'pending_order_bot',
				[
					'twilio_sid'     => '',
					'twilio_token'   => '',
					'twilio_phone'   => '',
					'twilio_message' => '',
					'send_text'      => '',
					'send_email'     => '',
				]
			)
			->andReturn( true );

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_updates_POST_values_that_are_set() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 95,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 50,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 0,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$updated_options = [
			'send_text'  => true,
			'send_email' => false,
		];

		$_POST = array_merge(
			$updated_options,
			[
				'pending_order_bot_save_settings'  => true,
				'pending_order_bot_settings_nonce' => 'a8jfkgw2h7i',
			]
		);

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->once()
			->with( 'a8jfkgw2h7i', 'pending_order_bot_settings_action' )
			->andReturn( true );

		\WP_Mock::userFunction(
			'wp_unslash',
			[
				'times'  => 7,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'sanitize_text_field',
			[
				'times'  => 7,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'update_option' )
			->once()
			->with(
				'pending_order_bot',
				[
					'twilio_sid'     => '',
					'twilio_token'   => '',
					'twilio_phone'   => '',
					'twilio_message' => '',
					'send_text'      => true,
					'send_email'     => false,
				]
			)
			->andReturn( true );

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	/*public function test_register_options_styles_bails() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 25,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 9,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 6,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'plugins_url' )
			->with( 'pending-order-bot/styles.css' )
			->andReturn( 'https://example.com/wp-content/plugins/pending-order-bot/styles.css' );

		\WP_Mock::userFunction( 'wp_enqueue_style' )
			->with(
				'pending-order-bot',
				'https://example.com/wp-content/plugins/pending-order-bot/styles.css',
				[],
				true,
				'all'
			)
			->andReturn( null );

		$this->admin->register_options_styles();

		$this->assertConditionsMet();
	}*/
}
