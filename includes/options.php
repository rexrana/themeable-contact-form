<?php
/**
 * Themeable Contact Form - Option Page.
 *
 * @package Themeable_Contact_Form
 * @since   1.0.0
 */

/**
 * Registers a new options page under Settings.
 *
 * @return void
 */
function tcf_admin_menu() {
	add_options_page(
		// page_title.
		__( 'Themeable Contact Form', 'themeable-contact-form' ),
		// menu_title.
		__( 'Contact Form', 'themeable-contact-form' ),
		// capability.
		'manage_options',
		// menu slug.
		'themeable-contact-form',
		// callback.
		'tcf_options_page'
	);
}
add_action( 'admin_menu', 'tcf_admin_menu' );

/**
 * Initialize settings
 *
 * Registers settings, sections, and fields for the plugin options page.
 *
 * @return void
 */
function tcf_settings_init() {
	// Register setting.
	register_setting(
		'themeable-contact-form',
		'themeable-contact-form',
		array(
			'sanitize_callback' => 'tcf_sanitize_settings',
		)
	);

	// Email Settings Section.
	add_settings_section(
		'tcf_email_section',
		__( 'Email Settings', 'themeable-contact-form' ),
		'tcf_email_section_callback',
		'themeable-contact-form'
	);

	// From Name field.
	add_settings_field(
		'from_name',
		__( 'From Name', 'themeable-contact-form' ),
		'tcf_from_name_field_callback',
		'themeable-contact-form',
		'tcf_email_section'
	);

	// Email From field.
	add_settings_field(
		'email_from',
		__( 'From address', 'themeable-contact-form' ),
		'tcf_email_from_field_callback',
		'themeable-contact-form',
		'tcf_email_section'
	);

	// Email To field.
	add_settings_field(
		'email_to',
		__( 'Send Emails To', 'themeable-contact-form' ),
		'tcf_email_to_field_callback',
		'themeable-contact-form',
		'tcf_email_section'
	);

	// Template Section.
	add_settings_section(
		'tcf_template_section',
		__( 'Template', 'themeable-contact-form' ),
		'tcf_template_section_callback',
		'themeable-contact-form'
	);

	// Form Template field.
	add_settings_field(
		'form_template',
		__( 'Form Template', 'themeable-contact-form' ),
		'tcf_form_template_field_callback',
		'themeable-contact-form',
		'tcf_template_section'
	);
}
add_action( 'admin_init', 'tcf_settings_init' );

/**
 * Email section callback.
 *
 * Section description output (optional).
 *
 * @return void
 */
function tcf_email_section_callback() {
	// Optional: Add section description here if needed.
}

/**
 * Template section callback.
 *
 * Outputs description for the template section.
 *
 * @return void
 */
function tcf_template_section_callback() {
	echo '<p>' . esc_html__( 'Template used to display the form. You can also override the default template in your theme.', 'themeable-contact-form' ) . '</p>';
}

/**
 * From Name field callback.
 *
 * Outputs the From Name field for the settings page.
 *
 * @return void
 */
function tcf_from_name_field_callback() {
	$options = get_option( 'themeable-contact-form', array() );
	$value   = isset( $options['from_name'] ) ? $options['from_name'] : get_option( 'blogname' );
	?>
	<input
		type="text"
		id="from_name"
		name="themeable-contact-form[from_name]"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
	/>
	<p class="description"><?php esc_html_e( 'name that will be used in the email From header', 'themeable-contact-form' ); ?></p>
	<?php
}

/**
 * Email From field callback.
 *
 * Outputs the Email From field for the settings page.
 *
 * @return void
 */
function tcf_email_from_field_callback() {
	$options = get_option( 'themeable-contact-form', array() );
	$value   = isset( $options['email_from'] ) ? $options['email_from'] : get_option( 'admin_email' );
	?>
	<input
		type="email"
		id="email_from"
		name="themeable-contact-form[email_from]"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
	/>
	<p class="description"><?php esc_html_e( 'Address that will be used in the email From header. This should be an email address at your site\'s domain to avoid spam filters', 'themeable-contact-form' ); ?></p>
	<?php
}

/**
 * Email To field callback.
 *
 * Outputs the Email To field for the settings page.
 *
 * @return void
 */
function tcf_email_to_field_callback() {
	$options = get_option( 'themeable-contact-form', array() );
	$value   = isset( $options['email_to'] ) ? $options['email_to'] : get_option( 'admin_email' );
	?>
	<input
		type="email"
		id="email_to"
		name="themeable-contact-form[email_to]"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
	/>
	<p class="description"><?php esc_html_e( 'email address where form submissions should be sent', 'themeable-contact-form' ); ?></p>
	<?php
}

/**
 * Form Template field callback.
 *
 * Outputs the Form Template dropdown field for the settings page.
 *
 * @return void
 */
function tcf_form_template_field_callback() {
	$options = get_option( 'themeable-contact-form', array() );
	$value   = isset( $options['form_template'] ) ? $options['form_template'] : '';

	$choices = array(
		''           => __( 'Default (no styles)', 'themeable-contact-form' ),
		'bootstrap'  => __( 'Bootstrap', 'themeable-contact-form' ),
		'foundation' => __( 'Foundation', 'themeable-contact-form' ),
	);
	?>
	<select id="form_template" name="themeable-contact-form[form_template]">
		<?php foreach ( $choices as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Sanitize settings.
 *
 * @param array $input The input values from the settings form.
 * @return array The sanitized values.
 */
function tcf_sanitize_settings( $input ) {
	$sanitized = array();

	// Sanitize from_name.
	if ( isset( $input['from_name'] ) ) {
		$sanitized['from_name'] = sanitize_text_field( $input['from_name'] );
	}

	// Sanitize email_from.
	if ( isset( $input['email_from'] ) ) {
		$sanitized['email_from'] = sanitize_email( $input['email_from'] );
	}

	// Sanitize email_to.
	if ( isset( $input['email_to'] ) ) {
		$sanitized['email_to'] = sanitize_email( $input['email_to'] );
	}

	// Sanitize form_template.
	if ( isset( $input['form_template'] ) ) {
		$allowed_templates          = array( '', 'bootstrap', 'foundation' );
		$sanitized['form_template'] = in_array( $input['form_template'], $allowed_templates, true )
			? $input['form_template']
			: '';
	}

	return $sanitized;
}

/**
 * Options page callback.
 *
 * Outputs the settings page HTML.
 *
 * @return void
 */
function tcf_options_page() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Show error/update messages.
	settings_errors( 'themeable-contact-form' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'themeable-contact-form' );
			do_settings_sections( 'themeable-contact-form' );
			submit_button( __( 'Save Settings', 'themeable-contact-form' ) );
			?>
		</form>
	</div>
	<?php
}
