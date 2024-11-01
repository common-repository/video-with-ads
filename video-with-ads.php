<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
   Plugin Name: video-with-ads
   Description:  A plugin to show virtual YouTube video on the page and able to realize a video watching experience with 					several advertisements, with 'skip ad in 5 sec' button on the screen, just like YouTube. 
   Author: Ramlal Solanki
   Author URI: https://about.me/ramlal
   Version: 1.0
*/ 
//Define directory path
define( 'RCODE_VWA_DIR_PATH', dirname(__FILE__).'/' );
define( 'RCODE_VWA_URL_PATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );

//Call css and js
function rcodehub_vwa_custom_scr() {
    wp_register_style('rcodehub_vwa_custom_scr', plugins_url('assets/css/style.css',__FILE__ ));
    wp_enqueue_style('rcodehub_vwa_custom_scr');
}
add_action( 'admin_init','rcodehub_vwa_custom_scr'); 



/**
 * Enqueue a script with jQuery as a dependency and style.
 */
function RCODE_VWA_scripts_method() {
    wp_enqueue_script( 'RCODE_VWA-custom-script', RCODE_VWA_URL_PATH . 'assets/js/vastvideoplugin.js', array( 'jquery' ) );
    //Call css
    wp_register_style('RCODE_VWA_css', RCODE_VWA_URL_PATH.'assets/css/style.css',__FILE__ );
    wp_enqueue_style('RCODE_VWA_css');

}
add_action( 'wp_enqueue_scripts', 'RCODE_VWA_scripts_method' );



 //Create database table  
 function rcode_vwa_create_required_database_table(){
    global $wpdb;
    $charset_collate= $wpdb->get_charset_collate();
	$table_ads		= $wpdb->prefix . 'rcode_ads';
	$sql_ads		= "CREATE TABLE $table_ads (
							id int(11) NOT NULL AUTO_INCREMENT,
							ads_url varchar(250) NOT NULL,
							ads_time varchar(25) NOT NULL,
							UNIQUE KEY id (id)
	) $charset_collate;";
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	dbDelta($sql_ads);
}
register_activation_hook( __FILE__, 'rcode_vwa_create_required_database_table' ); 

//Menu setup
add_action('admin_menu', 'rcode_vwa_plugin_setup_menu'); 
function rcode_vwa_plugin_setup_menu(){
	
	add_menu_page('Video Settings', 'Video Settings', 
				'manage_options', 'rcode_admin_slug', 'rcode_vwa_settings_fun');
							
	add_submenu_page( 'rcode_admin_slug', 'Ads List', 'Ads List',
				'manage_options', 'rcode_settings_slug','rcode_vwa_ads_fun');	

}
//Call setting page
function rcode_vwa_settings_fun(){
	include('video.php');
}
//Call ads list page
function rcode_vwa_ads_fun(){
	include('ads-list.php');
}


// -----------------------------------Shortcode implementation------------------------------------
function rcode_vwa_video_with_ads($atts) {
	// turn on output buffering to capture script output
	ob_start();
	// include file (contents will get saved in output buffer)
	include_once(RCODE_VWA_DIR_PATH . 'front-video.php');
	// save and return the content that has been output
	$content = ob_get_clean();
	return $content;
}
//register the Shortcode handler
add_shortcode('rcode_video_ads', 'rcode_vwa_video_with_ads');
//-------------------------------------------------XXXXXXXXXXXX---------------------------------------------

//Delete ads
add_action( 'wp_ajax_nopriv_delete_ads_action', 'delete_ads_action' );
add_action( 'wp_ajax_delete_ads_action', 'delete_ads_action' );
function delete_ads_action(){
	global $wpdb;
	if (isset( $_POST["pippin_adsDelete_nonce"] ) && wp_verify_nonce($_POST['pippin_adsDelete_nonce'], 'pippin-adsDelete-nonce')) {
		$ads_id	 = sanitize_text_field($_POST['ads_id']);
		if($ads_id) { 
			$ads_table  = $wpdb->prefix."rcode_ads";
		    $wpdb->query("DELETE  FROM ".$ads_table." WHERE id = ".$ads_id);
		}
	}
	die();
}
