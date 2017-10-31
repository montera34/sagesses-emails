<?php
/*
Plugin Name: Sagesses emails
Description: This plugin allows you to send emails in a non interactive way with a random content to a list of contacts several times per day.
Version: 0.1
Author: Montera34
Author URI: https://montera34.com
License: GPLv3
*/

// VARIABLES
$subjects_count = 10; // Maximum number of subjects for the emails to choose from
$addresses_count = 20; // Maximum number of email addresses to send emails to

// ADD IMAGE SIZE FOR EMAIL CONTENT
add_action( 'init', 'sgs_emails_image_size' );
function sgs_emails_image_size() {
	add_image_size( 'sgs-emails', 500, 500, false );
	add_filter( 'image_size_names_choose', 'sgs_emails_image_size_names' );
}
function sgs_emails_image_size_names( $sizes ) {
	return array_merge( $sizes, array(
		'sgs-emails' => __('Sagesses Email plugin size','sgs_emails')
	) );
}


////
// PLUGIN PAGE IN DASHBOARD

// ADD PLUGIN OPTION PAGE TO DASHBOARD
add_action('admin_menu', 'sgs_emails_dashboard_page');
function sgs_emails_dashboard_page() {
	add_menu_page(__('Sagesses emails','sgs_emails'),'Sagesses emails ','moderate_comments','sagesses_emails', 'sgs_emails_dashboard_page_output','dashicons-email-alt',80);
}


// REGISTER PLUGIN SETTINGS
// using Settings API
add_action( 'admin_init', 'sgs_emails_register_settings' );
function sgs_emails_register_settings() {

	register_setting( 'sgs_emails_settings_group', 'sgs_emails_settings' );

	// post type settings
	add_settings_section( 'sgs_emails_settings_ptype_section', __('Post type','sgs_emails'), 'sgs_emails_settings_ptype_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_ptype', __('Post type for email content','sgs_emails'), 'sgs_emails_settings_ptype_callback', 'sagesses_emails', 'sgs_emails_settings_ptype_section' );

	// subjects list setting
	add_settings_section( 'sgs_emails_settings_subjects_section', __('Subjects','sgs_emails'), 'sgs_emails_settings_subjects_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_subjects', __('List of subjects','sgs_emails'), 'sgs_emails_settings_subjects_callback', 'sagesses_emails', 'sgs_emails_settings_subjects_section' );

	// email addresses list setting
	add_settings_section( 'sgs_emails_settings_addresses_section', __('Addresses','sgs_emails'), 'sgs_emails_settings_addresses_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_addresses', __('List of addresses','sgs_emails'), 'sgs_emails_settings_addresses_callback', 'sagesses_emails', 'sgs_emails_settings_addresses_section' );

}


// CALLBACK FUNCTIONS
// post type
function sgs_emails_settings_ptype_section_callback() {
	echo __('Choose a post type to feed the emails with content.','sgs_emails');
}

function sgs_emails_settings_ptype_callback() {
	global $wp_post_types;
	$args = array(
		'public' => true
	);
	$ptypes = get_post_types($args);
	$settings = (array) get_option( 'sgs_emails_settings' );
	$ptype = esc_attr( $settings['sgs_emails_settings_ptype'] );
	$options = '<option value=""></option>';
	foreach ( $ptypes as $pt ) {
		$options .= ( $pt == $ptype ) ? '<option value="'.$pt.'" selected>'.$pt.'</option>' : '<option value="'.$pt.'">'.$pt.'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_ptype]'>".$options."</select>";
}

// subjects list
function sgs_emails_settings_subjects_section_callback() {
	echo __('List of subjects for emails. When an email is sent, its subject will be chosen randomly from this list.','sgs_emails');
}

function sgs_emails_settings_subjects_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	global $subjects_count;
	$count = 0;
	while ( $count < $subjects_count ) {
		$subject = esc_attr( $settings['sgs_emails_settings_subjects'][$count] );
		echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_subjects][".$count."]' value='$subject' />";
		$count++;
	}
}

// addresses list
function sgs_emails_settings_addresses_section_callback() {
	echo __('List of addresses to send emails to.','sgs_emails');
}

function sgs_emails_settings_addresses_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	global $addresses_count;
	$count = 0;
	while ( $count < $addresses_count ) {
		$address = esc_attr( $settings['sgs_emails_settings_addresses'][$count] );
		echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_addresses][".$count."]' value='$address' />";
		$count++;
	}

}


// GENERATE OUTPUT
function sgs_emails_dashboard_page_output() { ?>
	<div class="wrap">
		<h2><?php _e('Sagesses send emails tool','sgs'); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'sgs_emails_settings_group' ); ?>
			<?php do_settings_sections( 'sagesses_emails' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

// end PLUGIN PAGE IN DASHBOARD
////


// CHOOSE SUBJECT FOR EMAIL
function sgs_emails_choose_subject() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$subjects = $settings['sgs_emails_settings_subjects'];
	$subject = '';
	while ( $subject == '' ) {
		$subject = $subjects[array_rand($subjects)];
	}
	return $subject;
}

// CHOOSE CONTENT FOR EMAIL
function sgs_emails_choose_content() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$pt = $settings['sgs_emails_settings_ptype'];

	$args = array(
		'post_type' => $pt,
		'showposts' => 1,
		'orderby' => 'rand'
	);
	$image = '';
	while ( $image == '' ) {
		$contents = get_posts($args);
		$content = $contents[0];
		if ( has_post_thumbnail($content->ID) ) {
			$image = get_the_post_thumbnail($content->ID,'sgs-emails');
		}
	}
	
	return $image;
}
?>
