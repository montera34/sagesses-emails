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
$min_addresses_count = 20; // Maximum number of email addresses to send emails to

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

	// deliveries per day
	add_settings_section( 'sgs_emails_settings_deliveries_section', __('Deliveries','sgs-emails'), 'sgs_emails_settings_deliveries_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_deliveries_per_day', __('Deliveries per day','sgs-emails'), 'sgs_emails_settings_deliveries_per_day_callback', 'sagesses_emails', 'sgs_emails_settings_deliveries_section' );

	// send probability
	add_settings_section( 'sgs_emails_settings_probability_section', __('Send probability','sgs-emails'), 'sgs_emails_settings_probability_section_callback', 'sagesses_emails' );
	add_settings_field( 'sgs_emails_settings_probability', __('Probability 1','sgs-emails'), 'sgs_emails_settings_probability_callback', 'sagesses_emails', 'sgs_emails_settings_probability_section' );
	add_settings_field( 'sgs_emails_settings_probability_2', __('Probability 2','sgs-emails'), 'sgs_emails_settings_probability_2_callback', 'sagesses_emails', 'sgs_emails_settings_probability_section' );
	add_settings_field( 'sgs_emails_settings_probability_3', __('Probability 3','sgs-emails'), 'sgs_emails_settings_probability_3_callback', 'sagesses_emails', 'sgs_emails_settings_probability_section' );
	add_settings_field( 'sgs_emails_settings_probability_4', __('Probability 4','sgs-emails'), 'sgs_emails_settings_probability_4_callback', 'sagesses_emails', 'sgs_emails_settings_probability_section' );

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

// deliveries per day
function sgs_emails_settings_deliveries_section_callback() {
	echo __('Deliveries per day. This setting creates the cron jobs to trigger the each delibery.','sgs-emails');
}

function sgs_emails_settings_deliveries_per_day_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_deliveries_per_day'] );
	$probs = array(
		array(
			'l' => __('0','sgs-emails'),
			'v' => '0'
		),
		array(
			'l' => __('1','sgs-emails'),
			'v' => '1'
		),
		array(
			'l' => __('2','sgs-emails'),
			'v' => '2'
		),
		array(
			'l' => __('3','sgs-emails'),
			'v' => '3'
		),
		array(
			'l' => __('4','sgs-emails'),
			'v' => '4'
		),
		array(
			'l' => __('5','sgs-emails'),
			'v' => '5'
		)
	);
	$options = '';
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_deliveries_per_day]'>".$options."</select>";
}

// send probability
function sgs_emails_settings_probability_section_callback() {
	echo __('Choose the probability to send emails at each send time.','sgs-emails');
}

$probs = array(
	array(
		'l' => __('0% - Sends are halted','sgs-emails'),
		'v' => '0'
	),
	array(
		'l' => __('8%','sgs-emails'),
		'v' => '8'
	),
	array(
		'l' => __('10%','sgs-emails'),
		'v' => '10'
	),
	array(
		'l' => __('11%','sgs-emails'),
		'v' => '11'
	),
	array(
		'l' => __('15%','sgs-emails'),
		'v' => '15'
	),
	array(
		'l' => __('17%','sgs-emails'),
		'v' => '17'
	),
	array(
		'l' => __('20%','sgs-emails'),
		'v' => '20'
	),
	array(
		'l' => __('25%','sgs-emails'),
		'v' => '25'
	),
	array(
		'l' => __('30%','sgs-emails'),
		'v' => '30'
	),
	array(
		'l' => __('33%','sgs-emails'),
		'v' => '33'
	),
	array(
		'l' => __('40%','sgs-emails'),
		'v' => '40'
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

function sgs_emails_settings_probability_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_probability'] );
	global $probs;
	$options = '';
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_probability]'>".$options."</select>";
}

function sgs_emails_settings_probability_2_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_probability_2'] );
	global $probs;
	$options = '';
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_probability_2]'>".$options."</select>";
}

function sgs_emails_settings_probability_3_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_probability_3'] );
	global $probs;
	$options = '';
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_probability_3]'>".$options."</select>";
}

function sgs_emails_settings_probability_4_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = esc_attr( $settings['sgs_emails_settings_probability_4'] );
	global $probs;
	$options = '';
	foreach ( $probs as $p ) {
		$options .= ( $p['v'] == $prob ) ? '<option value="'.$p['v'].'" selected>'.$p['l'].'</option>' : '<option value="'.$p['v'].'">'.$p['l'].'</option>';
	}
	echo "<select name='sgs_emails_settings[sgs_emails_settings_probability_4]'>".$options."</select>";
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
	echo __('List of addresses to send emails to. If no more empty fields, save and 5 more will show up.','sgs-emails');
}

function sgs_emails_settings_addresses_callback() {
	$settings = (array) get_option( 'sgs_emails_settings' );
	global $min_addresses_count;
	$fill_count = 0;
	foreach ( $settings['sgs_emails_settings_addresses'] as $a ) {
		if ( $a == '' ) continue;
		$fill_count++;
	}
	$addresses_count = ( $fill_count >= $min_addresses_count ) ? $fill_count : $min_addresses_count;
	$free = $addresses_count - $fill_count;
	
	if ( $free < 5 ) $addresses_count += 5 - $free;
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
function sgs_emails_dashboard_page_output() { 
	sgs_emails_set_wpcron();
	sgs_emails_sort_addresses();
?>
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


// SORT EMAIL ADDRESSES
function sgs_emails_sort_addresses() {
	//function _remove_empty_internal($value) { return !empty($value) || $value === 0; }
	$settings = (array) get_option( 'sgs_emails_settings' );
	$addresses = $settings['sgs_emails_settings_addresses'];
	$sorted = array_map('trim', $addresses);
	//array_filter($sorted, '_remove_empty_internal');
	rsort($sorted);
	$settings['sgs_emails_settings_addresses'] = $sorted;
	update_option('sgs_emails_settings',$settings);
	return;
}

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
function sgs_emails_choose_image($email_address,$user,$contents_all,$exclude_ids) {
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
				'post_status' => 'publish',
				'post__not_in' => $exclude_ids
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
			}
			else {
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

	add_user_meta( $user->ID, '_sgs_emails_log', $content->ID );

	// check how many images has already received the user
	$contents_check = $contents_all;
	$u_log_check = get_user_meta($user->ID,'_sgs_emails_log');
	if ( !empty($contents_check) && count($contents_check) == count($u_log_check) )
		add_user_meta( $user->ID, '_sgs_emails_ended', 1, true );

	return $image;
}

// COMPOSE AND SEND EMAIL
function sgs_emails_compose_and_send($email_address,$user,$contents_all,$exclude_ids) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$from = $settings['sgs_emails_settings_from'];
	$from_name = $settings['sgs_emails_settings_from_name'];
	$replyto = $settings['sgs_emails_settings_replyto'];
	$replyto_name = $settings['sgs_emails_settings_replyto_name'];

	$to = $email_address;
	$subject = sgs_emails_choose_subject();

	$image = sgs_emails_choose_image($to,$user,$contents_all,$exclude_ids);
	if ( $image == false )
		return;

	$related_file = $image['path'];
	$related_cid = sgs_emails_random_string(); //will map it to this UID
	$related_name = $image['filename']; //this will be the file name for the attachment

	include "email-template.php";
	$body = $email_template;

	$sgs_emails_phpmailer = function(&$phpmailer)use($related_file,$related_cid,$related_name,$from,$from_name,$replyto,$replyto_name){

	$phpmailer->SMTPKeepAlive = true;
	$phpmailer->IsHTML(true);
	$phpmailer->AddEmbeddedImage($related_file, $related_cid, $related_name);
	$phpmailer->From = $from;
	$phpmailer->FromName = $from_name;
	$phpmailer->AddReplyTo($replyto, $replyto_name);
	};

	add_action( 'phpmailer_init',$sgs_emails_phpmailer);
	$sent = wp_mail( $to, $subject, $body);
	remove_action('phpmailer_init', $sgs_emails_phpmailer);

	return $sent;
}

// DETERMINE WHEN TO SEND EMAIL
function sgs_emails_if_send($address,$contents_all,$log) {
	$settings = (array) get_option( 'sgs_emails_settings' );
	$prob = $settings['sgs_emails_settings_probability'];
	$count_contents = count($contents_all);
	if ( $count_contents > 0 ) {
		$count_log = count($log);
		$d = ($count_log / $count_contents);
		if ( $d < 0.5 && $d >= 0.25 )
			$prob = $settings['sgs_emails_settings_probability_2'];
		elseif ( $d < 0.75 && $d >= 0.5 )
			$prob = $settings['sgs_emails_settings_probability_3'];
		elseif ( $d < 1 && $d >= 0.75 )
			$prob = $settings['sgs_emails_settings_probability_4'];
	} else { return 0; }
		
	$n = array();
	for ( $i = 0; $i < 100; $i++ ) {
		$n[] = ( $i < $prob ) ? 1 : 0; 
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
	$pt = $settings['sgs_emails_settings_ptype'];
	$addresses = $settings['sgs_emails_settings_addresses'];
	$args = array(
		'post_type' => $pt,
		'showposts' => -1,
		'post_status' => 'publish'
	);
	$contents = get_posts($args);
	foreach ( $addresses as $a ) {
		// get current user by email
		$u = get_user_by('email',$a);
		if ( $u == false ) // if user does not exist
			continue;

		$u_ended = get_user_meta($u->ID,'_sgs_emails_ended');
		if ( $u_ended[0] == 1 )
			continue;

		$u_log = get_user_meta($u->ID,'_sgs_emails_log');
		if ( ! empty($u_log) ) {
			foreach ( $u_log as $l ) {
				$exclude_ids[] = $l['ID'];
			}
		}
		else { $exclude_ids = array(); }

		if ( sgs_emails_if_send($a,$contents,$u_log) !== 1 )
			continue;
		$sent = sgs_emails_compose_and_send($a,$u,$contents,$exclude_ids);
	}
	return;
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
$times = array(
	strtotime('tomorrow 09:30'),
	strtotime('tomorrow 10:30'),
	strtotime('tomorrow 14:00'),
	strtotime('tomorrow 14:30'),
	strtotime('tomorrow 15:30')
);

register_activation_hook( __FILE__, 'sgs_emails_set_wpcron' );
function sgs_emails_set_wpcron() {
	global $times;
	$settings = (array) get_option( 'sgs_emails_settings' );
	if ( array_key_exists('sgs_emails_settings_deliveries_per_day',$settings) )
		$dpd = esc_attr( $settings['sgs_emails_settings_deliveries_per_day'] );
	else
		return;

	for ( $i = 0; $i < 5;$i++ ) {
		$job = 'sgs_emails_set_cron_'.$i;
		// Use wp_next_scheduled to check if the event is already scheduled
		$timestamp = wp_next_scheduled( $job );
		if ( $timestamp == false && $i < $dpd ) {
			// Schedule the event for right now, then to repeat daily using the hook 'sgs_emails_create_cron_send'
			wp_schedule_event( $times[$i], 'daily', $job );
			//add_action( $job, 'sgs_emails_action_per_address');
		} elseif ( $timestamp != false && $i >= $dpd ) {
			wp_clear_scheduled_hook( $job );
		}
	}
	return;
}

//Hook our function, sgs_emails_action_per_address, into the action sgs_emails_scheduled_send
add_action( 'sgs_emails_set_cron_0', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_1', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_2', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_3', 'sgs_emails_action_per_address');
add_action( 'sgs_emails_set_cron_4', 'sgs_emails_action_per_address');

// unhook cron jobs
register_deactivation_hook( __FILE__, 'sgs_emails_unset_wpcron' );
function sgs_emails_unset_wpcron() {
	global $times;
	$settings = (array) get_option( 'sgs_emails_settings' );
	if ( array_key_exists('sgs_emails_settings_deliveries_per_day',$settings) )
		$dpd = esc_attr( $settings['sgs_emails_settings_deliveries_per_day'] );
	else
		return;

	for ( $i = 0;$i < $dpd;$i++ ) {
		$job = 'sgs_emails_set_cron_'.$i;
		$timestamp = wp_next_scheduled( $job );
		if( $timestamp == false ) {
			wp_clear_scheduled_hook( $job );
			//remove_action( $job, 'sgs_emails_action_per_address');
		}
	}

}

// end CRON TASKS
////
?>
