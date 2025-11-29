<?php
/**
 * Simple Contact Form Class
 *
 * @package Themeable_Contact_Form
 * @since   0.1.0
 */

namespace RexRana\ThemeableContactForm;

use \RexRana\ThemeableContactForm\TemplateLoader;
use \GUMP;

/**
 * Simple Contact Form class
 */
class ContactForm {



	/**
	 * Variable for instance of TemplateLoader class
	 *
	 * @var TemplateLoader
	 */
	protected $template_loader;

	/**
	 * Submitted form data
	 *
	 * @var array
	 */
	protected $form_data;

	/**
	 * Status messages about form submission
	 *
	 * @var array|string
	 */
	protected $form_messages;

	/**
	 * Sanitized form data
	 *
	 * @var array
	 */
	protected $sanitized_data;

	/**
	 * Plugin options
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Error loggging
	 *
	 * @var array|string
	 */
	protected $errors;

	/**
	 * To: email address
	 *
	 * @var string
	 */
	protected $email_to;

	/**
	 * From: email address
	 *
	 * @var string
	 */
	protected $email_from;

	/**
	 * Cc: email address
	 *
	 * @var string
	 */
	protected $email_cc;

	/**
	 * Bcc: email address
	 *
	 * @var string
	 */
	protected $email_bcc;

	/**
	 * Class contructor, sets up variables
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'themeable_contact_form', array( $this, 'form_shortcode' ) );

		$this->form_data = array();

		$this->options = get_option( 'themeable-contact-form' );

		if ( is_array( $this->options ) && array_key_exists( 'from_name', $this->options ) && '' !== $this->options['from_name'] && array_key_exists( 'email_from', $this->options ) && '' !== $this->options['email_from'] ) {
			/* translators: 1. name 2. email address */
			$this->email_from = sprintf( __( '%1$s <%2$s>', 'themeable-contact-form' ), $this->options['from_name'], $this->options['email_from'] );
		} else {
			/* translators: 1. name 2. email address */
			$this->email_from = sprintf( __( '%1$s <%2$s>', 'themeable-contact-form' ), get_option( 'blogname' ), get_option( 'admin_email' ) );
		}

		$this->email_cc  = '';
		$this->email_bcc = '';

		if ( is_array( $this->options ) && array_key_exists( 'send_emails_to', $this->options ) && '' !== $this->options['send_emails_to'] ) {
			$this->email_to = $this->options['send_emails_to'];
		} else {
			$this->email_to = get_option( 'admin_email' );
		}

		$this->template_loader = new TemplateLoader();
	}

	/**
	 * Form Processing
	 *
	 * @return int Status code of form processing.
	 */
	protected function process_contact_form() {

		$gump = new GUMP();

		$_POST = $gump->sanitize( $_POST ); // You don't have to sanitize, but it's safest to do so.

		// 'firstname' and 'lastname' are the honeypot fields.
		$rules = array(
			'firstname'           => 'exact_len,0',
			'lastname'            => 'exact_len,0',
			'tcf_contact_name'    => 'required|valid_name',
			'tcf_contact_email'   => 'required|valid_email',
			'tcf_contact_message' => 'required',
		);

		$filters = array(
			'tcf_contact_name'    => 'trim|sanitize_string',
			'tcf_contact_email'   => 'trim|sanitize_email',
			'tcf_contact_message' => 'trim|sanitize_string',
		);

		$validated   = $gump->validate( $_POST, $rules );
		$nonce_valid = $this->verify_nonce( $_REQUEST['tcf_contact_nonce'], 'contact_form' );

		if ( false === $nonce_valid || 0 < strlen( $_POST['firstname'] ) || 0 < strlen( $_POST['lastname'] ) ) {
			// bots.
			return 0;
		} elseif ( true === $validated && true === $nonce_valid ) {
			// now sanitize data for use.
			$this->sanitized_data = $gump->filter( $_POST, $filters );

			return 1;
		} else {
			$this->errors = $validated;

			return 2;
		}
	}

	/**
	 * Utility function to verify nonces
	 *
	 * @param string     $nonce Nonce that was used in the form to verify.
	 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
	 * @return bool
	 */
	protected function verify_nonce( $nonce, $action ) {
		$valid = wp_verify_nonce( $nonce, $action );
		if ( is_int( $valid ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Display the contact form
	 *
	 * @return void
	 */
	public function display_contact_form() {
		$tcf_options = get_option( 'themeable-contact-form', array() );
		$template    = 'contact-form';

		if ( array_key_exists( 'form_template', $tcf_options ) && '' !== $tcf_options['form_template'] ) {
			$template .= '-' . $tcf_options['form_template'];
		}

		$this->template_loader->set_template_data( $this->form_data, 'data' );
		$this->template_loader->get_template_part( $template );
	}

	/**
	 * Display status message
	 *
	 * @param string $message The message being printed.
	 * @return void
	 */
	public function display_message( $message ) {
		if ( $this->is_cli() ) {
			echo "\n";
			print_r( $message );
		} else {
			$this->template_loader->set_template_data( array( 'message' => $message ), 'data' );
			$this->template_loader->get_template_part( 'contact-message' );
		}
	}

	/**
	 * Shortcode for contact form
	 *
	 * @return void
	 */
	public function form_shortcode() {

		global $wp;

		$this->form_data['my_url'] = home_url( add_query_arg( array(), $wp->request ) );

		if ( ! empty( $_POST ) ) {
			$process_status = $this->process_contact_form();

			switch ( $process_status ) {
				case 0:
					// bots, fail silently.
					exit();
					break;
				case 1:
					// success, email and then return success message.

					$sent = $this->send_email();

					if ( $sent ) {
					$message = __( 'Your message has been sent successfully. Thank you', 'themeable-contact-form' );
				} else {
					$log_message  = "error sending email from contact form\n";
					$log_message .= 'date: ' . gmdate( DATE_ATOM ) . "\n";
					$log_message .= "from: {$this->sanitized_data['tcf_contact_name']} <{$this->sanitized_data['tcf_contact_email']}>\n";
					$log_message .= "message: {$this->sanitized_data['tcf_contact_email']}\n";

					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log( $log_message );
					}

					$message = __( 'There was an error in sending the email. Please try again later', 'themeable-contact-form' );
				}					$this->display_message( $message );
					exit();

					break;
				case 2:
					// validation errors, prepare error messages and display form.

					$field_names = array(
						'tcf_contact_name'    => __( 'Name', 'themeable-contact-form' ),
						'tcf_contact_email'   => __( 'Email', 'themeable-contact-form' ),
						'tcf_contact_message' => __( 'Message', 'themeable-contact-form' ),
					);

					$validation_messages = array(
						/* translators: required field name  */
						'validate_required'    => __( '<em>%s</em> is required', 'themeable-contact-form' ),
						/* translators: Email  */
						'validate_valid_email' => __( '<em>%s</em> must be a valid email address', 'themeable-contact-form' ),
						/* translators: Name  */
						'valid_name'           => __( '<em>%s</em> must be a valid name', 'themeable-contact-form' ),
					);

					$messages = array();

					foreach ( $this->errors as $error ) {
						$messages[] = sprintf( $validation_messages[ $error['rule'] ], $field_names[ $error['field'] ] );
					}

					$msg  = __( 'Your submission has the following errors:', 'themeable-contact-form' ) . "<br />\n";
					$msg .= implode( "<br />\n", $messages );

					$this->form_data['errors']  = $msg;
					$this->form_data['name']    = $_POST['tcf_contact_name'];
					$this->form_data['email']   = $_POST['tcf_contact_email'];
					$this->form_data['message'] = $_POST['tcf_contact_message'];

					$this->display_contact_form();

					break;
			}
		} else {
			$this->form_data['name']    = '';
			$this->form_data['email']   = '';
			$this->form_data['message'] = '';
			$this->form_data['errors']  = '';

			$this->display_contact_form();
		}
	}

	/**
	 * Send an email with the contact inquiry
	 *
	 * @return boolean Whether the email was sent successfully.
	 */
	protected function send_email() {
		$headers = array(
			'From:' . $this->email_from,
		);

		if ( '' !== $this->email_cc ) {
			$headers[] = 'Cc:' . $this->email_cc;
		}
		if ( '' !== $this->email_bcc ) {
			$headers[] = 'Bcc:' . $this->email_bcc;
		}

		/* translators: Name */
		$subject = sprintf( __( 'Contact Form email from: %s', 'themeable-contact-form' ), $this->sanitized_data['tcf_contact_name'] );
		/* translators: 1. Name 2. Email address */
		$message  = sprintf( __( 'from: %1$s <%2$s>', 'themeable-contact-form' ), $this->sanitized_data['tcf_contact_name'], $this->sanitized_data['tcf_contact_email'] );
		$message .= "\n" . __( 'message:', 'themeable-contact-form' ) . "\n";
		$message .= $this->sanitized_data['tcf_contact_message'];

		$mailed = wp_mail( $this->email_to, $subject, $message, $headers );

		return $mailed;
	}

	/**
	 * Return errors variable
	 *
	 * @return mixed
	 */
	public function return_errors() {
		return $this->errors;
	}


	/**
	 * Check if we're on the command line
	 *
	 * @return boolean
	 */
	public function is_cli() {
		if ( defined( 'STDIN' ) ) {
			return true;
		}

		if ( empty( $_SERVER['REMOTE_ADDR'] ) && ! isset( $_SERVER['HTTP_USER_AGENT'] ) && isset( $_SERVER['argv'] )
		&& count( $_SERVER['argv'] ) > 0 ) {
			return true;
		}

		return false;
	}
}
