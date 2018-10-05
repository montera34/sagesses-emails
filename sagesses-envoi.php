<?php
/*
Plugin Name: Sagesses emails
Description: This plugin allows you to send emails in a non interactive way with a random content to a list of contacts several times per day.
Version: 0.1
Author: Montera34
Author URI: https://montera34.com
License: GPLv3
Text Domain: sgs-emails
Domain Path: /lang/
*/

// VARIABLES
$subjects_count = 10; // Maximum number of subjects for the emails to choose from
$addresses_count = 30; // Maximum number of email addresses to send emails to

// LOAD PLUGIN TEXT DOMAIN
// FOR STRING TRANSLATIONS
add_action( 'plugins_loaded', 'sgs_emails_load_textdomain' );
function sgs_emails_load_textdomain() {
	load_plugin_textdomain( 'sgs-emails', false, plugin_basename( dirname( __FILE__ ) ) . '/lang/' ); 
}

// ADD IMAGE SIZE FOR EMAIL CONTENT
add_action( 'init', 'sgs_emails_image_size' );
function sgs_emails_image_size() {
	add_image_size( 'sgs-emails', 500, 500, false );
	add_filter( 'image_size_names_choose', 'sgs_emails_image_size_names' );
}
function sgs_emails_image_size_names( $sizes ) {
	return array_merge( $sizes, array(
		'sgs-emails' => __('Sagesses Email plugin size','sgs-emails')
	) );
}


////
// PLUGIN PAGE IN DASHBOARD

// ADD PLUGIN OPTION PAGE TO DASHBOARD
add_action('admin_menu', 'sgs_emails_dashboard_page');
function sgs_emails_dashboard_page() {
	add_menu_page(__('Sagesses emails','sgs-emails'),'Sagesses emails ','moderate_comments','sagesses_emails', 'sgs_emails_dashboard_page_output','dashicons-email-alt',80);
}


// REGISTER PLUGIN SETTINGS
// using Settings API
add_action( 'admin_init', 'sgs_emails_register_settings' );
function sgs_emails_register_settings() {

	register_setting( 'sgs_emails_settings_group', 'sgs_emails_settings' );

	// post type settings
	add_settings_section( 'sgs_emails_settings_ptype_section', __('Post type','sgs-emails'), 'sgs_emails_settings_ptype_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_ptype', __('Post type for email content','sgs-emails'), 'sgs_emails_settings_ptype_callback', 'sagesses_emails', 'sgs_emails_settings_ptype_section' );

	// send probability
	add_settings_section( 'sgs_emails_settings_probability_section', __('Send probability','sgs-emails'), 'sgs_emails_settings_probability_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_probability', __('Probability','sgs-emails'), 'sgs_emails_settings_probability_callback', 'sagesses_emails', 'sgs_emails_settings_probability_section' );

	// subjects list settings
	add_settings_section( 'sgs_emails_settings_subjects_section', __('Subjects','sgs-emails'), 'sgs_emails_settings_subjects_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_subjects', __('List of subjects','sgs-emails'), 'sgs_emails_settings_subjects_callback', 'sagesses_emails', 'sgs_emails_settings_subjects_section' );

	// email addresses list settings
	add_settings_section( 'sgs_emails_settings_addresses_section', __('Addresses','sgs-emails'), 'sgs_emails_settings_addresses_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_addresses', __('List of addresses','sgs-emails'), 'sgs_emails_settings_addresses_callback', 'sagesses_emails', 'sgs_emails_settings_addresses_section' );

	// email from and reply-to settings
	add_settings_section( 'sgs_emails_settings_headers_section', __('Email headers','sgs-emails'), 'sgs_emails_settings_headers_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_from', __('From field (email address)','sgs-emails'), 'sgs_emails_settings_headers_from_callback', 'sagesses_emails', 'sgs_emails_settings_headers_section' );
	add_settings_field( 'sgs_emails_settings_from_name', __('From field (name)','sgs-emails'), 'sgs_emails_settings_headers_from_name_callback', 'sagesses_emails', 'sgs_emails_settings_headers_section' );
	add_settings_field( 'sgs_emails_settings_replyto', __('Reply-to field (email address)','sgs-emails'), 'sgs_emails_settings_headers_replyto_callback', 'sagesses_emails', 'sgs_emails_settings_headers_section' );
	add_settings_field( 'sgs_emails_settings_replyto_name', __('Reply-to field (name)','sgs-emails'), 'sgs_emails_settings_headers_replyto_name_callback', 'sagesses_emails', 'sgs_emails_settings_headers_section' );

}


// CALLBACK FUNCTIONS
// post type
function sgs_emails_settings_ptype_section_callback() {
	echo __('Choose a post type to feed the emails with content.','sgs-emails');
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

// send probability
function sgs_emails_settings_probability_section_callback() {
	echo __('Choose the probability to send emails at each send time.','sgs-emails');
}

function sgs_emails_settings_probability_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_probability'] );
	$options = '';
	$probs = array(
		array(
			'l' => __('0% - Sends are halted','sgs-emails'),
			'v' => '0'
		),
		array(
			'l' => __('25%','sgs-emails'),
			'v' => '25'
		),
		array(
			'l' => __('33%','sgs-emails'),
			'v' => '33'
		),
		array(
			'l' => __('50%','sgs-emails'),
			'v' => '50'
		),
		array(
			'l' => __('66%','sgs-emails'),
			'v' => '66'
		),
		array(
			'l' => __('75%','sgs-emails'),
			'v' => '75'
		),
		array(
			'l' => __('100%','sgs-emails'),
			'v' => '100'
		)
	);
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_probability]'>".$options."</select>";
}

// subjects list
function sgs_emails_settings_subjects_section_callback() {
	echo __('List of subjects for emails. When an email is sent, its subject will be chosen randomly from this list.','sgs-emails');
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
	echo __('List of addresses to send emails to.','sgs-emails');
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

// email from and reply-to headers
function sgs_emails_settings_headers_section_callback() {
	echo __('Headers for outgoing emails sent by this plugin.','sgs-emails');
}

function sgs_emails_settings_headers_from_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$value = esc_attr( $settings['sgs_emails_settings_from'] );
	echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_from]' value='$value' />";
}

function sgs_emails_settings_headers_from_name_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$value = esc_attr( $settings['sgs_emails_settings_from_name'] );
	echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_from_name]' value='$value' />";
}

function sgs_emails_settings_headers_replyto_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$value = esc_attr( $settings['sgs_emails_settings_replyto'] );
	echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_replyto]' value='$value' />";
}

function sgs_emails_settings_headers_replyto_name_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$value = esc_attr( $settings['sgs_emails_settings_replyto_name'] );
	echo "<input type='text' name='sgs_emails_settings[sgs_emails_settings_replyto_name]' value='$value' />";
}

// GENERATE OUTPUT
function sgs_emails_dashboard_page_output() { ?>
	<div class="wrap">
		<h2><?php _e('Sagesses send emails tool','sgs-emails'); ?></h2>
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

// GENERATE RANDOM STRING
function sgs_emails_random_string($length = 16) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[mt_rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

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
function sgs_emails_choose_image($email_address) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$pt = $settings['sgs_emails_settings_ptype'];

	$image_id = '';
	while ( $image_id == '' ) {
		$content_id = sgs_emails_current_get($email_address,'next');
		if ( $content_id == '' ) {
			$args = array(
				'post_type' => $pt,
				'showposts' => 1,
				'orderby' => 'rand',
				'post_parent' => 0,
				'post_status' => 'publish'
			);
		}
		else {
			$args = array(
				'post_type' => $pt,
				'p' => $content_id,
				'post_status' => 'publish'
			);
		}

		$contents = get_posts($args);
		$content = $contents[0];
		if ( has_post_thumbnail($content->ID) ) {
			$image['id'] = get_post_thumbnail_id($content->ID);
			$image['alt'] = __('Image','sgs-emails');
			$image_data = wp_get_attachment_metadata($image['id']);
			$image_subdir = ( preg_match('/\d{4}\/\d{2}/',$image_data['file'],$matches ) == 1 ) ? trailingslashit($matches[0]) : '';
			$upload_dir = wp_get_upload_dir();
			$image_dir = trailingslashit($upload_dir['baseurl']);
			$image_dir_path = trailingslashit($upload_dir['basedir']);
			$image_email = $image_data['sizes']['sgs-emails'];
			if ( is_array($image_email) && count($image_email) == 4 ) {
				$image['url'] =  $image_dir . $image_subdir . $image_email['file'];
				$image['path'] = $image_dir_path . $image_subdir . $image_email['file'];
				$image['width'] = $image_email['width'];
				$image['height'] = $image_email['height'];
				$image['mime-type'] = $image_email['mime-type'];
				$image['filename'] = $image_email['file'];
			} else {
				$image['url'] =  $image_dir . $image_data['file'];
				$image['path'] = $image_dir_path . $image_data['file'];
				$image['width'] = $image_data['width'];
				$image['height'] = $image_data['height'];
				$image['mime-type'] = get_post_mime_type( $image['id'] );
				$image['filename'] = $image_data['file'];
			}
			$image['subdir'] = $image_subdir;
			$image_id = $image['id'];

			$args = array(
				'post_type' => $pt,
				'showposts' => -1,
				'post_parent' => $content->ID,
				'order' => 'ASC',
				'orderby' => 'menu_order',
				'post_status' => 'publish'
			);
			$children = get_posts($args);

			if ( count($children) >= 1 ) {
				$ids[] = $content->ID;
				foreach ( $children as $ch ) {
					$ids[] = $ch->ID;
				}
				sgs_emails_current_update($email_address,$ids,1);
			}
			else {
				sgs_emails_current_update($email_address);
			}
		}
		else {
			$content_id = '';
			sgs_emails_current_delete($email_address);
		}
	}
	return $image;
}

// COMPOSE AND SEND EMAIL
function sgs_emails_compose_and_send($email_address) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$from = $settings['sgs_emails_settings_from'];
	$from_name = $settings['sgs_emails_settings_from_name'];
	$replyto = $settings['sgs_emails_settings_replyto'];
	$replyto_name = $settings['sgs_emails_settings_replyto_name'];

//	$boundary = sgs_emails_random_string();
//	$boundary_alt = sgs_emails_random_string();
//	$boundary_rel = sgs_emails_random_string();
//	$newline  = "\r\n";

	$to = $email_address;
	$subject = sgs_emails_choose_subject();
	//add_filter( 'wp_mail_from', 'sgs_mail_from' );
	//add_filter( 'wp_mail_from_name', 'sgs_mail_from_name' );
	//$headers[] = 'Reply-To: '.$replyto_name.' <'.$replyto.'>' . "\r\n";
	// To send HTML mail, the Content-type header must be set
//	$headers[]  = 'MIME-Version: 1.0' . "\r\n";
//	$headers[] = 'Content-type: text/html; charset=UTF-8' . "\r\n";
//	$headers[] = 'Content-type: multipart/related' . "\r\n";
//	$headers[] = 'boundary="'.$boundary_alt.'"' . "\r\n";

	$image = sgs_emails_choose_image($to);
//	$img_b64 = base64_encode(file_get_contents($image['url']));
	//$file = $image['url']; //phpmailer will load this file
	$related_file = $image['path'];
	$related_cid = sgs_emails_random_string();; //will map it to this UID
	$related_name = $image['filename']; //this will be the file name for the attachment

	include "email-template.php";
	$body = $email_template;
	// $message = "Testing";
//global $phpmailer;
	$sgs_emails_phpmailer = function(&$phpmailer)use($related_file,$related_cid,$related_name,$from,$from_name,$replyto,$replyto_name){

	$phpmailer->SMTPKeepAlive = true;
	$phpmailer->IsHTML(true);
//	$headers = 'Content-type: multipart/alternative\n';
//	$headers .= 'MIME-Version: 1.0\n';
//	$phpmailer->AddCustomHeader($headers);
	$phpmailer->AddEmbeddedImage($related_file, $related_cid, $related_name);
	$phpmailer->From = $from;
	$phpmailer->FromName = $from_name;
	$phpmailer->AddReplyTo($replyto, $replyto_name);
	};
	add_action( 'phpmailer_init',$sgs_emails_phpmailer);
	$sent = wp_mail( $to, $subject, $body);
	remove_action('phpmailer_init', $sgs_emails_phpmailer);
	//remove_filter( 'wp_mail_from', 'sgs_wp_mail_from' );
	//remove_filter( 'wp_mail_from_name', 'sgs_mail_from_name' );
//$body= rtrim(chunk_split(base64_encode($message)));


//	$headers =
//	'Content-Type: multipart/related; boundary="'.$boundary_rel.'"'.$newline.
//	'MIME-Version: 1.0'.$newline.
//	'From: '.$from_name.' <'.$from.'>'.$newline.
//	'Reply-To: '.$replyto_name.' <'.$replyto.'>'.$newline.$newline

//	"Content-Type: multipart/related; boundary=\"$boundary_rel\"$newline".
//	"MIME-Version: 1.0$newline".
//	"From: $from_name <$from>$newline".
//	"Reply-To: $replyto_name <$replyto>$newline$newline"
       
       //          "Content-Type: multipart/alternative;".
  //         "$boundary$newline".
//	"Content-Type: text/html; charset=UTF-8$newline".
////	"Content-Transfer-Encoding: base64$newline$newline";
//	"Content-Transfer-Encoding: 7bit$newline$newline";
	;

//$sent = mail($to,$subject,$body,$headers);
//$sent = mail($to,$subject,$body);
//mail($to,$subject,"the content");
//echo 'HAR!';
//	require_once ABSPATH . WPINC . '/class-phpmailer.php';
//	global $phpmailer;
//	$mail = new PHPMailer();
//	return $image['path'];
	return $sent;
	//return $img_b64;

}

// DETERMINE WHEN TO SEND EMAIL
function sgs_emails_if_send() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = $settings['sgs_emails_settings_probability'];
	switch ($prob) {
	case 0:
		$n = array(0);
		break;
	case 25:
		$n = array(0,0,0,1);
		break;
	case 33:
		$n = array(0,0,1);
		break;
	case 50:
		$n = array(0,1);
		break;
	case 66:
		$n = array(0,1,1);
		break;
	case 75:
		$n = array(0,1,1,1);
		break;

	case 100:
		$n = array(1);
		break;
	}
	$send = $n[array_rand($n)];
	return $send;
}

// SET CUSTOM MAIL FROM
function sgs_mail_from( $original_email_address ) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$from = $settings['sgs_emails_settings_from'];
	//Make sure the email is from the same domain 
	//as your website to avoid being marked as spam.
	return $from;
}

function sgs_mail_from_name( $original_email_from ) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$from_name = $settings['sgs_emails_settings_from_name'];
	return $from_name;
}

// ACTION PER ADDRESS
function sgs_emails_action_per_address() {
	$dw = date('w');
	$ch = date('dm');
	// if saturday or sunday, do nothing
	if ( $dw == '0' || $dw == '6' )
		return;
	// if christmas holidays, do nothing
	if ( $ch == '2412' || $ch == '2512' || $ch == '2612' || $ch == '2712' || $ch == '2812' || $ch == '2912' || $ch == '3012' || $ch == '3112' || $ch == '0101' || $ch == '0201' )
		return;

	$settings = (array) get_option( 'sgs_emails_settings' );
	$addresses = $settings['sgs_emails_settings_addresses'];
	foreach ( $addresses as $a ) {
		if ( sgs_emails_if_send() !== 1 )
			continue;

		$sent = sgs_emails_compose_and_send($a);
	}
}


////
// CURRENT SERIES UPDATE, GET AND DELETE FUNCTIONS

function sgs_emails_current_update($address,$new_current=0,$new_next=0) {
	$series = (array) get_option( 'sgs_emails_current_series' );
	if ( array_key_exists($address,$series) ) {
		$current = $series[$address]['current'];
		$next = $series[$address]['next'];
	}

	if ( $new_current == 0 && $new_next == 0 ) {
		$new_next = ( $next == '' ) ? 1 : $next;
		$series[$address]['next'] = ++$new_next;
		if ( $series[$address]['next'] == count($current) ) {
			$updated= sgs_emails_current_delete($address);
		}
		else {
			$updated = update_option('sgs_emails_current_series',$series);
		}
	}
	else {
		$series[$address]['current'] = $new_current;
		$series[$address]['next'] = $new_next;
		$updated = update_option('sgs_emails_current_series',$series);
	}
	return $updated;
}

function sgs_emails_current_get($address,$format) {
	$series = (array) get_option( 'sgs_emails_current_series' );
	if ( ! array_key_exists($address,$series) )
		return false;

	$log = $series[$address];

	if ( $format == 'next' ) {
		if ( $log['next'] == '' )
			$requested_log = '';

		$requested_log = $log['current'][$log['next']];
	}
	elseif ( $format == 'current') {
		if ( $log['current'] == '' )
			$requested_log = '';

		$requested_log = $log['current'];
	}
	return $requested_log;
}

function sgs_emails_current_delete($address) {
	$series = get_option('sgs_emails_current_series');
	$series[$address]['next'] = '';
	$series[$address]['current'] = '';
	$updated = update_option('sgs_emails_current_series',$series);
	return $updated;
}

////
// CRON TASKS
register_activation_hook( __FILE__, 'sgs_emails_set_wpcron' );
function sgs_emails_set_wpcron() {

	$time1 = strtotime('tomorrow 09:30');
	$time2 = strtotime('tomorrow 10:30');
	$time3 = strtotime('tomorrow 14:00');
	$time4 = strtotime('tomorrow 14:30');
	$time5 = strtotime('tomorrow 15:30');

	// Use wp_next_scheduled to check if the event is already scheduled
	$timestamp1 = wp_next_scheduled( 'sgs_emails_set_cron_1' );
	$timestamp2 = wp_next_scheduled( 'sgs_emails_set_cron_2' );
	$timestamp3 = wp_next_scheduled( 'sgs_emails_set_cron_3' );
	$timestamp4 = wp_next_scheduled( 'sgs_emails_set_cron_4' );
	$timestamp5 = wp_next_scheduled( 'sgs_emails_set_cron_5' );

	// If $timestamp == false schedule daily backups since it hasn't been done previously
	// Schedule the event for right now, then to repeat daily using the hook 'sgs_emails_create_cron_send'
	if( $timestamp1 == false )
		wp_schedule_event( $time1, 'daily', 'sgs_emails_set_cron_1' );
	if( $timestamp2 == false )
		wp_schedule_event( $time2, 'daily', 'sgs_emails_set_cron_2' );
	if( $timestamp3 == false )
		wp_schedule_event( $time3, 'daily', 'sgs_emails_set_cron_3' );
	if( $timestamp4 == false )
		wp_schedule_event( $time4, 'daily', 'sgs_emails_set_cron_4' );
	if( $timestamp5 == false )
		wp_schedule_event( $time5, 'daily', 'sgs_emails_set_cron_5' );

}

//Hook our function, sgs_emails_action_per_address, into the action sgs_emails_scheduled_send
add_action( 'sgs_emails_set_cron_1', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_2', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_3', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_4', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_5', 'sgs_emails_action_per_address');

// end CRON TASKS
////
?>
