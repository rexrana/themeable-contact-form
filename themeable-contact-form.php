<?php
/**
 * Plugin Name:     Themeable Contact Form
 * Plugin URI:      https://github.com/rexrana/themeable-contact-form
 * Description:     A simple contact form plugin that allows you to customize the template to match your theme
 * Author:          Rex Rana Design and Development Ltd.
 * Author URI:      https://rexrana.ca
 * Text Domain:     themeable-contact-form
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Themeable_Contact_Form
 */

define( 'TCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Composer autoloader
require_once (__DIR__ . '/vendor/autoload.php');

use RexRana\ThemeableContactForm\ContactForm;
use dependencies\RationalOptionPages;

new ContactForm();

// options page
$pages = array(
	'themeable-contact-form'    => array(
		'page_title'    => __( 'Themeable Contact Form', 'themeable-contact-form' ),
		'menu_title' => __( 'Contact Form', 'themeable-contact-form' ),
		'parent_slug' => 'options-general.php',
		'sections'      => array(
			'email' => array(
				'title'       => __( 'Email Settings', 'themeable-contact-form' ),
				'fields'      => array(
					'from_name' => array(
						'title'     => __( 'From Name', 'themeable-contact-form' ),
						'type'      => 'text',
						'text'      => 'name that will be used in the email From header',
						'value' => get_option('blogname'),
					),
					'email_from'   => array(
						'title'     => __( 'From address', 'themeable-contact-form' ),
						'type'      => 'email',
						'text'      => __('Address that will be used in the email From header. This should be an email address at your site\'s domain to avoid spam filters', 'themeable-contact-form' ),
						'value' => get_option('admin_email'),
					),
					'email_to'   => array(
						'title'     => __( 'Send Emails To', 'themeable-contact-form' ),
						'type'      => 'email',
						'text'      => 'email address where form submissions should be sent',
						'value' => get_option('admin_email'),
					),
				),
			),
			'template' => array(
				'title' => __( 'Template', 'themeable-contact-form' ),
				'text'      => __('Template used to display the form. You can also override the default template in your theme.', 'themeable-contact-form' ),
				'fields' => array(
					'form_template'		=> array(
						'title'			=> __( 'Form Template', 'themeable-contact-form' ),
						'type'			=> 'select',
						'value'			=> '',
						'choices'		=> array(
							''	=> __( 'Default (no styles)', 'themeable-contact-form' ),
							'bootstrap'	=> __( 'Bootstrap', 'themeable-contact-form' ),
							'foundation'	=> __( 'Foundation', 'themeable-contact-form' ),
						),
					)

				)
			)
		),
	),
);
$option_page = new RationalOptionPages( $pages );


function tcf_activate(){
    register_uninstall_hook( __FILE__, 'tcf_uninstall' );
}
register_activation_hook( __FILE__, 'tcf_activate' );

// allow writing to WordPress debug log
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}


// define the wp_mail_failed callback 
function tcf_action_wp_mail_failed($wp_error) 
{
    return error_log(print_r($wp_error, true));
}
          
// add the action 
add_action('wp_mail_failed', 'tcf_action_wp_mail_failed', 10, 1);