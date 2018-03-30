<?php
use \RexRana\ThemeableContactForm\ContactForm;

class ContactFormTest extends WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

		$_POST = array();
		$_REQUEST = array();

        $this->class_instance = new ContactForm();

    }

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object &$object    Instantiated object that we will run method on.
	 * @param string $methodName Method name to call
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	public function invokeMethod(&$object, $methodName, array $parameters = array())
	{
	    $reflection = new \ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);

	    return $method->invokeArgs($object, $parameters);
	}

    public function test_verify_nonce()
    {

		// create a nonce with the same action our production code uses
		$tcf_contact_nonce = wp_create_nonce('contact_form');

		// create a dummy nonce that should fail
		$dummy_nonce = 'skjfhkwuee';

		// vars to test
		// $shouldbetrue = $this->class_instance->verify_nonce($tcf_contact_nonce, 'contact_form');
		$shouldbetrue = $this->invokeMethod($this->class_instance, 'verify_nonce', [$tcf_contact_nonce, 'contact_form']);


		// $shouldbefalse = $this->class_instance->verify_nonce($dummy_nonce, 'contact_form');
		$shouldbefalse = $this->invokeMethod($this->class_instance, 'verify_nonce', [$dummy_nonce, 'contact_form']);

		$this->assertTrue( $shouldbetrue );
		$this->assertFalse( $shouldbefalse );

    }

	public function test_process_contact_form_should_pass()
	{

		$_POST = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => 'This is my message',
			'firstname' => '',
			'lastname' => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce('contact_form');

		$processed = $this->invokeMethod($this->class_instance, 'process_contact_form');

		$this->assertEquals( 1, $processed );

	}

	public function test_process_contact_form_no_email()
	{
		// no email address, should return errors
		$_POST = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => '',
			'tcf_contact_message' => 'This is my message',
			'firstname' => '',
			'lastname' => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce('contact_form');

		$processed = $this->invokeMethod($this->class_instance, 'process_contact_form');

		$this->assertEquals( 2, $processed );

		// test error messages
		$errors = $this->class_instance->return_errors();
		$find_error = $this->find_error_message($errors, 'tcf_contact_email', 'validate_required');

		$this->assertTrue( $find_error );

	}

	public function test_process_contact_form_no_name()
	{
		// no email address, should return errors
		$_POST = array(
			'tcf_contact_name'    => '',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => 'This is my message',
			'firstname' => '',
			'lastname' => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce('contact_form');

		$processed = $this->invokeMethod($this->class_instance, 'process_contact_form');

		$this->assertEquals( 2, $processed );

		// test error messages
		$errors = $this->class_instance->return_errors();
		$find_error = $this->find_error_message($errors, 'tcf_contact_name', 'validate_required');

		$this->assertTrue( $find_error );

	}

	public function test_process_contact_form_no_message()
	{
		// no email address, should return errors
		$_POST = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'jsmith@domain.net',
			'tcf_contact_message' => '',
			'firstname' => '',
			'lastname' => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce('contact_form');

		$processed = $this->invokeMethod($this->class_instance, 'process_contact_form');

		$this->assertEquals( 2, $processed );

		// test error messages
		$errors = $this->class_instance->return_errors();
		$find_error = $this->find_error_message($errors, 'tcf_contact_message', 'validate_required');

		$this->assertTrue( $find_error );

	}


	public function test_process_contact_form_invalid_email()
	{
		// no email address, should return errors
		$_POST = array(
			'tcf_contact_name'    => 'John Smith',
			'tcf_contact_email'   => 'ssdf',
			'tcf_contact_message' => 'This is my message',
			'firstname' => '',
			'lastname' => '',
		);
		$_REQUEST['tcf_contact_nonce'] = wp_create_nonce('contact_form');

		$processed = $this->invokeMethod($this->class_instance, 'process_contact_form');

		$this->assertEquals( 2, $processed );

		// test error messages
		$errors = $this->class_instance->return_errors();
		$find_error_email_invalid = $this->find_error_message($errors, 'tcf_contact_email', 'validate_valid_email');

		$this->assertTrue( $find_error_email_invalid );

	}


	public function find_error_message($errors, $fieldname, $rule)
	{
		foreach ($errors as $err) {

			if ($err['field'] == $fieldname && $err['rule'] == $rule) {
				return true;
			}

		}
		return false;
	}



}
