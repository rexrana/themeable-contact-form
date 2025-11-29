<?php
/**
 * Plugin Name:     Themeable Contact Form
 * Plugin URI:      https://github.com/rexrana/themeable-contact-form
 * Description:     A simple contact form plugin that allows you to customize the template to match your theme
 * Author:          Rex Rana Design and Development Ltd.
 * Author URI:      https://peterhebert.com
 * Text Domain:     themeable-contact-form
 * Domain Path:     /languages
 * Version:         0.3.3
 *
 * @package         Themeable_Contact_Form
 */

define( 'TCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TCF_PLUGIN_VERSION', '0.3.3' );

// Composer autoloader.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	// Fallback error for missing dependencies.
	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>';
			echo esc_html__( 'Themeable Contact Form: Composer dependencies not found. Please run "composer install --no-dev" in the plugin directory.', 'themeable-contact-form' );
			echo '</p></div>';
		}
	);
	return;
}

require_once TCF_PLUGIN_DIR . '/includes/options.php';
require_once TCF_PLUGIN_DIR . '/includes/functionality.php';
