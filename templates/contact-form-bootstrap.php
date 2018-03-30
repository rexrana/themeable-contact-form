<style>
input[name="tcf_another_field"] {
	display: none;
 }
</style>
<?php if( $data->errors ) : ?>
	<div class="alert alert-danger" role="alert"><?php echo $data->errors; ?></div>
<?php endif; ?>
<form method="post" action="<?php echo $data->my_url; ?>" class="needs-validation" novalidate>

	<input type="hidden" name="action" value="contact_form">
	<?php wp_nonce_field( 'contact_form', 'tcf_contact_nonce' ); ?>

	<div class="d-none">
		<input type="text" name="firstname" value="">
		<input type="text" name="lastname" value="">
	</div>

	<div class="form-group">
		<label for="name"><?php _e('Name', 'themeable-contact-form') ?></label>
		<input type="text" name="tcf_contact_name" value="<?php echo $data->name; ?>" class="form-control" required>
		<div class="invalid-feedback"><?php _e('Please enter your name', 'themeable-contact-form') ?></div>
	</div>

	<div class="form-group">
		<label for="email"><?php _e('Email', 'themeable-contact-form') ?></label>
		<input type="email" name="tcf_contact_email" value="<?php echo $data->email; ?>" class="form-control" required>
		<div class="invalid-feedback"><?php _e('Please enter a valid email', 'themeable-contact-form') ?></div>
	</div>

	<div class="form-group">
		<label for="message"><?php _e('Message', 'themeable-contact-form') ?></label>
		<textarea name="tcf_contact_message" rows="8" cols="80" class="form-control" required><?php echo htmlentities($data->message, ENT_QUOTES, "UTF-8"); ?></textarea>
		<div class="invalid-feedback"><?php _e('Please enter your message', 'themeable-contact-form') ?></div>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary"><?php _e('Submit', 'themeable-contact-form') ?></button>
	</div>

</form>

<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
