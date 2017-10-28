<?php
/*
Plugin Name: Sagesses emails
Description: This plugin allows you to send emails in a non interactive way with a random content to a list of contacts several times per day.
Version: 0.1
Author: Montera34
Author URI: https://montera34.com
License: GPLv3
*/

// ADD PLUGIN OPTION PAGE TO DASHBOARD
add_action('admin_menu', 'sgs_emails_dashboard_page');
function sgs_emails_dashboard_page() {
	add_menu_page(__('Sagesses emails','sgs_emails'),'Sagesses emails ','moderate_comments','sagesses_emails', 'sgs_emails_dashboard_page_output','dashicons-email-alt',80);

	//add_submenu_page('options-general.php',__('','sgs'),'Notification email','manage_options','notifica_email', 'm34_notifica_options_page');
	//add_options_page( 'Notification email','Notification email','manage_options','notifica_email', 'm34_notifica_options_page' );
}

// REGISTER PLUGIN SETTINGS
// using Settings API
add_action( 'admin_init', 'sgs_emails_register_settings' );
function sgs_emails_register_settings() {
	register_setting( 'sgs_emails_settings_group', 'sgs_emails_settings' );
	add_settings_section( 'sgs-emails-settings-subjetcs-section', __('List of subjects','sgs_emails'), 'sgs_emails_settings_subjects_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_subjects', __('Subjects list for emails','sgs_emails'), 'sgs_emails_settings_subjects_callback', 'sagesses_emails', 'sgs-emails-settings-subjetcs-section' );
}

// CALLBACK FUNCTIONS
function sgs_emails_settings_subjects_section_callback() {
	echo __('Description of this settings section...','sgs_emails');
}
function sgs_emails_settings_subjects_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$subjects = esc_attr( $settings['sgs_emails_settings_subjects'] );
	echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_subjects]' value='$subjects' />";
}

// GENERATE OUTPUT
function sgs_emails_dashboard_page_output() { ?>
	<div class="wrap">
		<h2><?php _e('Sagesses send emails tool','sgs'); ?></h2>
		<form method="post" action="sagesses_emails.php">
			<?php settings_fields( 'sgs_emails_settings_group' ); ?>
			<?php do_settings_sections( 'sagesses_emails' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}


?>
