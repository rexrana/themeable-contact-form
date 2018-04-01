<?php

namespace RexRana\ThemeableContactForm;

use \RexRana\ThemeableContactForm\TemplateLoader;
use \GUMP;

class ContactForm {

	protected $template_loader;
	protected $form_data;
	protected $form_messages;
	protected $sanitized_data;
	protected $errors;

	protected $email_to;
	public $email_from;
	public $email_cc;
	public $email_bcc;

	public function __construct()
	{

		add_shortcode( 'themeable_contact_form', array( $this, 'form_shortcode' ) );

		$this->form_data = array();
		$this->email_from = sprintf( __('%1$s <%2$s>', 'themeable-contact-form'), get_option( 'blogname' ), get_option( 'admin_email' ) );
		$this->email_cc = '';
		$this->email_bcc = '';
		$this->email_to = 'peter@rexrana.ca';

		$this->template_loader = new TemplateLoader();
	}

	protected function process_contact_form() {

		$gump = new GUMP();

		$_POST = $gump->sanitize($_POST); // You don't have to sanitize, but it's safest to do so.

		// 'firstname' and 'lastname' are the honeypot fields
		$rules   = array(
			'firstname'   => 'exact_len,0',
			'lastname'   => 'exact_len,0',
			'tcf_contact_name'    => 'required|valid_name',
			'tcf_contact_email'   => 'required|valid_email',
			'tcf_contact_message' => 'required',
		);

		$filters = array(
			'tcf_contact_name'    => 'trim|sanitize_string',
			'tcf_contact_email'   => 'trim|sanitize_email',
			'tcf_contact_message' => 'trim|sanitize_string'
		);


		$validated = $gump->validate($_POST, $rules);
		$nonce_valid = $this->verify_nonce( $_REQUEST['tcf_contact_nonce'], 'contact_form' );

		if ($nonce_valid === false || strlen($_POST['firstname']) > 0 || strlen($_POST['lastname']) > 0) {

			// bots
			return 0;

		}
		elseif ($validated === TRUE && $nonce_valid === TRUE)
		{

			// now sanitize data for use
			$this->sanitized_data = $gump->filter($_POST, $filters);

			return 1;

		}
		else
		{
			$this->errors = $validated;

			return 2;
		}

	}



	protected function verify_nonce($nonce, $action)
	{
		$valid = wp_verify_nonce( $nonce, $action );
		if( is_int( $valid) ) {
			return true;
		}
		return false;
	}

	public function display_contact_form( )
	{
		$tcf_options = get_option( 'themeable-contact-form', array() );
		$template = 'contact-form';

		if( array_key_exists('form_template', $tcf_options) && $tcf_options['form_template'] !== '')
		{
			$template .= '-' . $tcf_options['form_template'];
		}

		$this->template_loader->set_template_data( $this->form_data, 'data' );
		$this->template_loader->get_template_part( $template );

	}

	public function display_message( $message )
	{

		$this->template_loader->set_template_data( array('message' => $message), 'data' );
		$this->template_loader->get_template_part( 'contact-message' );

	}

	// Add Shortcode
	public function form_shortcode() {

		global $wp;

		$this->form_data['my_url'] = home_url(add_query_arg(array(),$wp->request));

		if (!empty($_POST))
		{

			$process_status = $this->process_contact_form();

			switch ($process_status) {
			    case 0:
                    // bots, fail silently
			        exit();
			        break;
			    case 1:
			        // success, email and then return success message

					$sent = $this->send_email();

					if($sent) {

						$message = __('Your message has been received. Thank you', 'themeable-contact-form');
					}
					else
					{

						$log_message = "error sending email from contact form\n";
						$log_message .= "date: ".date(DATE_ATOM)."\n";
						$log_message .= "from: {$this->sanitized_data['tcf_contact_name']} <{$this->sanitized_data['tcf_contact_email']}>\n";
						$log_message .= "message: {$this->sanitized_data['tcf_contact_email']}\n";

						write_log ( $log_message );

						$message = __('There was an error in sending the email. Please try again later', 'themeable-contact-form');
					}

					$this->display_message($message);
					exit();

			        break;
			    case 2:
					// validation errors, prepare error messages and display form

					$field_names   = array(
					'tcf_contact_name'    => __('Name', 'themeable-contact-form'),
					'tcf_contact_email'   => __('Email', 'themeable-contact-form'),
					'tcf_contact_message' => __('Message', 'themeable-contact-form'),
					);

					$validation_messages   = array(
					'validate_required'    => __('<em>%s</em> is required', 'themeable-contact-form'),
					'validate_valid_email'   => __('<em>%s</em> must be a valid email address', 'themeable-contact-form'),
					'valid_name'=> __('<em>%s</em> must be a valid name', 'themeable-contact-form'),
					);

					$messages = array();

					foreach ($this->errors as $error) {
						$messages[] = sprintf( $validation_messages[ $error['rule'] ], $field_names[ $error['field'] ] );
					}

					$msg = __( 'Your submission has the following errors:', 'themeable-contact-form' ) . "<br />\n";
					$msg .= implode("<br />\n", $messages);

					$this->form_data['errors'] = $msg;
					$this->form_data['name'] = $_POST['tcf_contact_name'];
					$this->form_data['email'] = $_POST['tcf_contact_email'];
					$this->form_data['message'] = $_POST['tcf_contact_message'];

					$this->display_contact_form( );


			        break;
			}


		} else {

			$this->form_data['name'] = '';
			$this->form_data['email'] = '';
			$this->form_data['message'] = '';
			$this->form_data['errors'] = '';

			$this->display_contact_form( );
		}



	}

	protected function send_email()
	{

		$headers = array(
		 'From:' . $this->email_from,
		);

		if($this->email_cc != '') {
			$headers[] = 'Cc:' . $this->email_cc;
		}
		if($this->email_bcc != '') {
			$headers[] = 'Bcc:' . $this->email_bcc;
		}

		$subject = sprintf( __('Contact Form email from: %s', 'themeable-contact-form'), $this->sanitized_data['tcf_contact_name']);

		$message = sprintf( __('from: %1$s <%2$s>', 'themeable-contact-form'), $this->sanitized_data['tcf_contact_name'], $this->sanitized_data['tcf_contact_email']);
		$message .= "\n" . __('message:', 'themeable-contact-form') . "\n";
		$message .= $this->sanitized_data['tcf_contact_message'];

		$mailed = wp_mail( $this->email_to, $subject, $message, $headers);

		return $mailed;

	}

	public function return_errors()
	{
		return $this->errors;
	}

}
