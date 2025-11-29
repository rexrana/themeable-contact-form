<?php
/**
 * Themeable Contact Form - main functionality.
 *
 * @package Themeable_Contact_Form
 * @since   1.0.0
 */

use RexRana\ThemeableContactForm\ContactForm;

/**
 * Initialize the contact form at init to ensure translations are available.
 *
 * @return void
 */
function tcf_init() {
	new ContactForm();
}
add_action( 'init', 'tcf_init' );

/**
 * Activation hook callback.
 *
 * @return void
 */
function tcf_activate() {
	register_uninstall_hook( __FILE__, 'tcf_uninstall' );
}
register_activation_hook( __FILE__, 'tcf_activate' );

/**
 * Define the wp_mail_failed callback.
 *
 * @param WP_Error $wp_error The WP_Error object.
 * @return void
 */
function tcf_action_wp_mail_failed( $wp_error ) {
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
	error_log( print_r( $wp_error, true ) );
}

// Add the action.
add_action( 'wp_mail_failed', 'tcf_action_wp_mail_failed', 10, 1 );
