<form method="post" action="<?php echo $data->my_url; ?>" data-abide novalidate>
	<?php if( $data->errors ) : ?>
		<div data-abide-error class="alert callout" style="display: none;">
			<?php echo $data->errors; ?>
		</div>
	<?php endif; ?>

	<input type="hidden" name="action" value="contact_form">
	<?php wp_nonce_field( 'contact_form', 'tcf_contact_nonce' ); ?>

	<div style="display: none">
		<input type="text" name="firstname" value="">
		<input type="text" name="lastname" value="">
	</div>

	<div class="row">
      <div class="small-12 columns">
        <label><?php _e('Name', 'themeable-contact-form') ?>
			<input type="text" name="tcf_contact_name" value="<?php echo $data->name; ?>" required>
          <span class="form-error"><?php _e('Please enter your name', 'themeable-contact-form') ?></span>
        </label>
      </div>

	  <div class="small-12 columns">
		  <label for="email"><?php _e('Email', 'themeable-contact-form') ?>
	  		<input type="email" name="tcf_contact_email" value="<?php echo $data->email; ?>" required pattern="email">
			<span class="form-error"><?php _e('Please enter a valid email', 'themeable-contact-form') ?></span>
	  	</label>
	  </div>

	  <div class="small-12 columns">
		  <label for="message"><?php _e('Message', 'themeable-contact-form') ?>
			  <textarea name="tcf_contact_message" rows="8" cols="80" required><?php echo htmlentities($data->message, ENT_QUOTES, "UTF-8"); ?></textarea>
			  <span class="form-error"><?php _e('Please enter your message', 'themeable-contact-form') ?></span>
		  </label>
	  </div>

    </div>

	<div class="row">
		<button class="button" type="submit" value="<?php _e('Submit', 'themeable-contact-form') ?>"><?php _e('Submit', 'themeable-contact-form') ?></button>
	</div>
</form>
