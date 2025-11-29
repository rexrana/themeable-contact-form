<?php
/**
 * Contact Form Test Class
 *
 * @package Themeable_Contact_Form
 */

use RexRana\ThemeableContactForm\ContactForm;

/**
 * Test case for ContactForm class.
 */
class ContactFormTest extends WP_UnitTestCase {

	/**
	 * Instance of ContactForm class.
	 *
	 * @var ContactForm
	 */
	protected $class_instance;

	/**
	 * Set up test fixtures.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$_POST    = array();
		$_REQUEST = array();

		$this->class_instance = new ContactForm();
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object $object     Instantiated object that we will run method on.
	 * @param string $method_name Method name to call.
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	public function invoke_method( &$object, $method_name, array $parameters = array() ) {
		$reflection = new \ReflectionClass( get_class( $object ) );
		$method     = $reflection->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( $object, $parameters );
	}

	/**
	 * Get private property.
	 *
	 * @param object $object        Object instance.
	 * @param string $property_name Property name.
	 * @return ReflectionProperty
	 */
	public function get_private_property( &$object, $property_name ) {
		$reflection = new \ReflectionClass( get_class( $object ) );
		$property   = $reflection->getProperty( $property_name );
		$property->setAccessible( true );

		return $property;
	}

	/**
	 * Test verify_nonce method.
	 *
	 * @return void
	 */
	public function test_verify_nonce() {
		// Create a nonce with the same action our production code uses.
		$tcf_contact_nonce = wp_create_nonce( 'contact_form' );

		// Create a dummy nonce that should fail.
		$dummy_nonce = 'skjfhkwuee';

		// Test valid nonce.
		$shouldbetrue = $this->invoke_method( $this->class_instance, 'verify_nonce', array( $tcf_contact_nonce, 'contact_form' ) );

		// Test invalid nonce.
		$shouldbefalse = $this->invoke_method( $this->class_instance, 'verify_nonce', array( $dummy_nonce, 'contact_form' ) );

		$this->assertTrue( $shouldbetrue );
		$this->assertFalse( $shouldbefalse );
	}

	/**
	 * Test process_contact_form method with valid data.
	 *
	 * @return void
	 */
	public function test_process_contact_form_should_pass() {
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => 'This is my message',
			'firstname'           => '',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 1, $processed );
	}

	/**
	 * Test process_contact_form method with no email.
	 *
	 * @return void
	 */
	public function test_process_contact_form_no_email() {
		// No email address, should return errors.
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => '',
			'tcf_contact_message' => 'This is my message',
			'firstname'           => '',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 2, $processed );

		// Test error messages.
		$errors     = $this->class_instance->return_errors();
		$find_error = $this->find_error_message( $errors, 'tcf_contact_email', 'required' );

		$this->assertTrue( $find_error );
	}

	/**
	 * Test process_contact_form method with no name.
	 *
	 * @return void
	 */
	public function test_process_contact_form_no_name() {
		// No name, should return errors.
		$_POST                         = array(
			'tcf_contact_name'    => '',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => 'This is my message',
			'firstname'           => '',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 2, $processed );

		// Test error messages.
		$errors     = $this->class_instance->return_errors();
		$find_error = $this->find_error_message( $errors, 'tcf_contact_name', 'required' );

		$this->assertTrue( $find_error );
	}

	/**
	 * Test process_contact_form method with no message.
	 *
	 * @return void
	 */
	public function test_process_contact_form_no_message() {
		// No message, should return errors.
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => '',
			'firstname'           => '',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 2, $processed );

		// Test error messages.
		$errors     = $this->class_instance->return_errors();
		$find_error = $this->find_error_message( $errors, 'tcf_contact_message', 'required' );

		$this->assertTrue( $find_error );
	}

	/**
	 * Test process_contact_form method with invalid email.
	 *
	 * @return void
	 */
	public function test_process_contact_form_invalid_email() {
		// Invalid email address, should return errors.
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'ssdf',
			'tcf_contact_message' => 'This is my message',
			'firstname'           => '',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 2, $processed );

		// Test error messages.
		$errors                   = $this->class_instance->return_errors();
		$find_error_email_invalid = $this->find_error_message( $errors, 'tcf_contact_email', 'valid_email' );

		$this->assertTrue( $find_error_email_invalid );
	}

	/**
	 * Test process_contact_form method when honeypot is triggered.
	 *
	 * @return void
	 */
	public function test_process_contact_form_trip_honeypot() {
		// The 'firstname' honeypot field is filled in, process should fail.
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => '',
			'firstname'           => 'John',
			'lastname'            => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 0, $processed );

		// The 'lastname' honeypot field is filled in, process should fail.
		$_POST                         = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => '',
			'firstname'           => '',
			'lastname'            => 'Smith',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce( 'contact_form' );

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 0, $processed );
	}

	/**
	 * Test process_contact_form method with invalid nonce.
	 *
	 * @return void
	 */
	public function test_process_contact_form_invalid_nonce() {
		// Create a dummy nonce that should fail.
		$_REQUEST['tcf_contact_nonce'] = 'skjfhkwu983dee';

		// Invalid nonce, process should fail.
		$_POST = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => 'my message',
			'firstname'           => '',
			'lastname'            => '',
		);

		$processed = $this->invoke_method( $this->class_instance, 'process_contact_form' );

		$this->assertEquals( 0, $processed );
	}

	/**
	 * Test send_email method.
	 *
	 * @return void
	 */
	public function test_send_email_should_pass() {
		$from = $this->get_private_property( $this->class_instance, 'email_from' );
		$from->setValue( $this->class_instance, 'peter@peterhebert.com' );

		$cc = $this->get_private_property( $this->class_instance, 'email_cc' );
		$cc->setValue( $this->class_instance, 'hebert.pj@gmail.com' );

		$bcc = $this->get_private_property( $this->class_instance, 'email_bcc' );
		$bcc->setValue( $this->class_instance, 'peter@rexrana.ca' );

		$data = $this->get_private_property( $this->class_instance, 'sanitized_data' );
		$data->setValue(
			$this->class_instance,
			array(
				'tcf_contact_name'    => 'John Smith',
				'tcf_contact_email'   => 'jsmith@domain.net',
				'tcf_contact_message' => 'my message',
			)
		);

		$sent = $this->invoke_method( $this->class_instance, 'send_email' );
		$this->assertTrue( $sent );
	}

	/**
	 * Test display_message method.
	 *
	 * @return void
	 */
	public function test_display_message() {
		$message = 'This is a test of displaying a message';
		$this->invoke_method( $this->class_instance, 'display_message', array( $message ) );

		$this->expectOutputString( "\n{$message}" );
	}

	/**
	 * Find error message in errors array.
	 *
	 * @param array  $errors    Array of error messages.
	 * @param string $fieldname Field name to search for.
	 * @param string $rule      Rule to search for.
	 * @return bool True if error found, false otherwise.
	 */
	public function find_error_message( $errors, $fieldname, $rule ) {
		// Handle if errors is not an array.
		if ( ! is_array( $errors ) ) {
			return false;
		}

		foreach ( $errors as $err ) {
			// Handle different error formats.
			if ( is_array( $err ) ) {
				if ( isset( $err['field'] ) && isset( $err['rule'] ) &&
					$err['field'] === $fieldname && $err['rule'] === $rule ) {
					return true;
				}
			}
		}
		return false;
	}
}

