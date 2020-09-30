# Themeable Contact Form #
**Contributors:** peterhebert  
**Tags:** contact form, contact, form, bootstrap, foundation  
**Requires at least:** 4.4  
**Tested up to:** 5.5.1  
**Stable tag:** 0.3.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

A simple contact form plugin that allows you to customize the template to match your theme

## Description ##

This plugin creates a simple contact form that allows you to customize the template to match your theme.

The contact form has only three fields, which is great for keeping things simple:

* name
* email
* message

All fields are required in order to submit the form.

Upon successful submission, a simple thank you message will be displayed to the visitor. This message can be customized on the settings page.

## Form Security ##

The plugin uses WordPress' [nonce field](https://developer.wordpress.org/reference/functions/wp_nonce_field/) functionality, as well two honeypot fields in order to prevent spam submissions.

All submitted data is validated and sanitized using the [GUMP](https://github.com/Wixel/GUMP) PHP input validation class.

## Form Templates ##

The form has three basic styles:

*   **Plain**  - only basic styles applied; uses the browsers' native field validation along side the server side validation
*   **Bootstrap** - [Bootstrap 4](https://getbootstrap.com/docs/4.0/components/forms/) markup and client side validation
*   **Foundation** - [Foundation 6 for Sites](https://foundation.zurb.com/sites/docs/forms.html) markup and Abide client-side validation

**Note** - the Bootstrap and Foundation templates only provide the HTML markup. Your theme must include either the Bootstrap 4 or Foundation 6 framework in order to make the form display and for the client-side validation to function properly.

In addition to these templates, you can override the form display with custom markup to match your theme. Simply copy the default template `templates/contact-form.php` to `contact-form/contact-form.php` within your theme, and then make sure to select 'Default' under 'Form Template' in the plugin settings page.

## Installation ##

1. Extract the plugin .zip and upload folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Settings > Contact Form' in the WordPress admin to customize your email and template settings.
4. Insert shortcode `[themeable_contact_form]` in your contact page to place form.

## Frequently Asked Questions ##


## Screenshots ##

## Changelog ##

### 0.3.1 ###
Updated composer dependencies, installation instructions.

### 0.3.0 ###
Properly retrieve saved plugin options for send email to.

### 0.2.0 ###
Properly retrieve saved plugin options for email from name and address.

### 0.1 ###
Initial plugin release.

