<?php
/**
 * Options Class.
 *
 * This class is responsible for holding the Admin
 * page options.
 *
 * @package PendingOrderBot
 */

namespace PendingOrderBot\Admin;

class Options {
	/**
	 * The Form.
	 *
	 * This array defines every single aspect of the
	 * Form displayed on the Admin options page.
	 *
	 * @since 1.0.0
	 */
	public static array $form;

	/**
	 * Define custom static method for calling
	 * dynamic methods for e.g. Options::get_page_title().
	 *
	 * @since 1.0.0
	 *
	 * @param string  $method Method name.
	 * @param mixed[] $args   Method args.
	 *
	 * @return string|mixed[]
	 */
	public static function __callStatic( $method, $args ) {
		static::init();

		$keys = substr( $method, strpos( $method, '_' ) + 1 );
		$keys = explode( '_', $keys );

		$value = '';

		foreach ( $keys as $key ) {
			$value = empty( $value ) ? ( static::$form[ $key ] ?? '' ) : ( $value[ $key ] ?? '' );
		}

		return $value;
	}

	/**
	 * Set up Form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init(): void {
		static::$form = [
			'page'   => static::get_form_page(),
			'notice' => static::get_form_notice(),
			'fields' => static::get_form_fields(),
			'submit' => static::get_form_submit(),
		];
	}

	/**
	 * Form Page.
	 *
	 * The Form page items containg the Page title,
	 * summary, slug and option name.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_page(): array {
		return [
			'title'   => esc_html__(
				'Pending Order Bot',
				'pending-order-bot'
			),
			'summary' => esc_html__(
				'Send reminders on WooCommerce pending orders.',
				'pending-order-bot'
			),
			'slug'    => 'pending-order-bot',
			'option'  => 'pending_order_bot',
		];
	}

	/**
	 * Form Submit.
	 *
	 * The Form submit items containing the heading,
	 * button name & label and nonce params.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_submit(): array {
		return [
			'heading' => esc_html__( 'Actions', 'pending-order-bot' ),
			'button'  => [
				'name'  => 'pending_order_bot_save_settings',
				'label' => esc_html__( 'Save Changes', 'pending-order-bot' ),
			],
			'nonce'   => [
				'name'   => 'pending_order_bot_settings_nonce',
				'action' => 'pending_order_bot_settings_action',
			],
		];
	}

	/**
	 * Form Fields.
	 *
	 * The Form field items containing the heading for
	 * each group block and controls.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_fields() {
		return [
			'general_options'       => [
				'heading'  => esc_html__( 'General Options', 'pending-order-bot' ),
				'controls' => [
					'send_text' => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Send Text', 'pending-order-bot' ),
						'summary' => esc_html__( 'Use Twilio API service to send phone text messages.', 'pending-order-bot' ),
					],
					'send_email' => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Send E-mail', 'pending-order-bot' ),
						'summary' => esc_html__( 'Use WP default e-mail address to send emails.', 'pending-order-bot' ),
					],
				],
			],
			'twilio_options'       => [
				'heading'  => esc_html__( 'Twilio Options', 'pending-order-bot' ),
				'controls' => [
					'twilio_phone'  => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Phone Number', 'pending-order-bot' ),
						'summary'     => esc_html__( 'e.g. +1234567890', 'pending-order-bot' ),
					],
					'twilio_token'  => [
						'control'     => esc_attr( 'password' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Twilio API Token', 'pending-order-bot' ),
						'summary'     => esc_html__( 'e.g. ae2kgch7i', 'pending-order-bot' ),
					],
				],
			],
		];
	}

	/**
	 * Form Notice.
	 *
	 * The Form notice containing the notice
	 * text displayed on save.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_notice() {
		return [
			'label' => esc_html__( 'Settings Saved.', 'pending-order-bot' ),
		];
	}
}
