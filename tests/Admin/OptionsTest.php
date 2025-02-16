<?php

namespace PendingOrderBot\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use PendingOrderBot\Admin\Options;

/**
 * @covers \PendingOrderBot\Admin\Options::get_form_page
 * @covers \PendingOrderBot\Admin\Options::get_form_submit
 * @covers \PendingOrderBot\Admin\Options::get_form_notice
 * @covers \PendingOrderBot\Admin\Options::get_form_fields
 */
class OptionsTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_form_page() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 2,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		$form_page = Options::get_form_page();

		$this->assertSame(
			$form_page,
			[
				'title'   => 'Pending Order Bot',
				'summary' => 'Send reminders on WooCommerce pending orders.',
				'slug'    => 'pending-order-bot',
				'option'  => 'pending_order_bot',
			]
		);
	}

	public function test_get_form_submit() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 2,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		$form_submit = Options::get_form_submit();

		$this->assertSame(
			$form_submit,
			[
				'heading' => 'Actions',
				'button'  => [
					'name'  => 'pending_order_bot_save_settings',
					'label' => 'Save Changes',
				],
				'nonce'   => [
					'name'   => 'pending_order_bot_settings_nonce',
					'action' => 'pending_order_bot_settings_action',
				],
			]
		);
	}

	public function test_get_form_fields() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		$form_fields = Options::get_form_fields();

		$this->assertSame(
			$form_fields,
			[
				'general_options' => [
					'heading'  => 'General Options',
					'controls' => [
						'send_text'  => [
							'control' => 'checkbox',
							'label'   => 'Send Text',
							'summary' => 'Use Twilio to send phone text messages.',
						],
						'send_email' => [
							'control' => 'checkbox',
							'label'   => 'Send E-mail',
							'summary' => 'Use WP default e-mail address to send emails.',
						],
					],
				],
				'twilio_options'  => [
					'heading'  => 'Twilio Options',
					'controls' => [
						'twilio_sid'     => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Account SID',
							'summary'     => 'Twilio SID Number',
						],
						'twilio_token'   => [
							'control'     => 'password',
							'placeholder' => '',
							'label'       => 'API Token',
							'summary'     => 'Twilio API Token string',
						],
						'twilio_phone'   => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Phone Number',
							'summary'     => 'e.g. +1234567890',
						],
						'twilio_message' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Message',
							'summary'     => 'e.g. You have abandoned orders.',
						],
					],
				],
			]
		);
	}

	public function test_get_form_notice() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'pending-order-bot' ) {
					return $text;
				},
			]
		);

		$form_notice = Options::get_form_notice();

		$this->assertSame(
			$form_notice,
			[
				'label' => 'Settings Saved.',
			]
		);
	}
}
