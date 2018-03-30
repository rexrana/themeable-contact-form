<style>
.d-none {
	display: none;
 }
 .alert {
	 position: relative;
	 padding: .75rem 1.25rem;
	 margin-bottom: 1rem;
	 border: 1px solid transparent;
	 border-radius: .25rem;
 }

 .alert-danger {
     color: #721c24;
     background-color: #f8d7da;
     border-color: #f5c6cb;
 }
 .tcf-form input[type=text],
 .tcf-form input[type=email],
 .tcf-form textarea
  {
	  width: 100%;
  }
 .tcf-form label { display: block; }

</style>
<?php if( $data->errors ) : ?>
	<div class="alert alert-danger" role="alert"><?php echo $data->errors; ?></div>
<?php endif; ?>
<form class="tcf-form" method="post" action="<?php echo $data->my_url; ?>">

	<input type="hidden" name="action" value="contact_form">
	<?php wp_nonce_field( 'contact_form', 'tcf_contact_nonce' ); ?>

	<div class="d-none">
		<input type="text" name="firstname" value="">
		<input type="text" name="lastname" value="">
	</div>

<p>
	<label for="name"><?php _e('Name', 'themeable-contact-form') ?></label>
	<input type="text" name="tcf_contact_name" value="<?php echo $data->name; ?>" required>
</p>

<p>
	<label for="email"><?php _e('Email', 'themeable-contact-form') ?></label>
	<input type="email" name="tcf_contact_email" value="<?php echo $data->email; ?>">
</p>

<p>
	<label for="message"><?php _e('Message', 'themeable-contact-form') ?></label>
	<textarea name="tcf_contact_message" rows="8" cols="80" required><?php echo htmlentities($data->message, ENT_QUOTES, "UTF-8"); ?></textarea>

</p>
<p>
	<input type="submit" name="tcf_contact_submit" value="<?php _e('Submit', 'themeable-contact-form') ?>">
</p>

</form>
