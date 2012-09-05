<?php
/**
 * @package Signup Builder
 * @version 1.0
 */
/*
Plugin Name: Signup Builder
Plugin URI: http://wordpress.org/extend/plugins/account-builder/
Description: Customizable frontend wordpress controls!.
Author:Ola Apata
Author URI: http://fb-520.com
Version: 1.0
License: GPL2 Licence
License URI: license.txt
*/


// Check if user has update function
global $wpdb,$user_identity, $user_ID, $update_message,$disable_text;
$signup_builder_table = $wpdb->prefix . "signup_builder";
register_activation_hook(__FILE__, "install_signup_builder");
define('SB_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('DISPLAY_NAME', 'Signup Builder');
define('SB_PLUGIN_DIR', dirname(__FILE__));

//Include the files including the Install, Update and Delete Functions
include_once(dirname(__FILE__) .  "/signup-builder-actions.php");
include_once(dirname(__FILE__) .  "/signup-builder-functions.php");
if (file_exists(dirname(__FILE__) .  "/signup-builder-p.php") ){
	include_once(dirname(__FILE__) .  "/signup-builder-p.php");
}
include_once(dirname(__FILE__) .  "/signup-builder-dashboard-widget.php");
include_once(dirname(__FILE__) .  "/signup-builder-sc.php");

function custom_signup() {
    // add_menu_page('Front GUI', 'Front GUI', 'add_users', 'signup-builder/signup-builder.php', 'show_signup',   plugins_url('signup-builder/images/star.png'));
	 
	 add_menu_page('Signup Builder', 
              'Signup Builder',
              8, 
              'signup-builder/signup-builder.php', 
              'show_signup', 
              plugins_url('signup-builder/images/star.png'));
	
	 
	 check_create_post();
	 
}

function load_scripts(){
	
	// wp_enqueue_script('jquery');
	 wp_deregister_script( 'jquery' );
     wp_enqueue_script('signup-builder-cb-js', '/wp-content/plugins/signup-builder/colorbox/colorbox/jquery.colorbox.js');	 
	 wp_enqueue_style( 'signup-builder-css', '/wp-content/plugins/signup-builder/css/signup-builder.css');
	 wp_enqueue_style( 'signup-builder-cb-css', plugins_url( 'signup-builder/3rdparty/colorbox/colorbox/colorbox.css' ) );
	 wp_enqueue_script('jquery');	
	  
	 	 
}


function _update_alert($message, $type){	
	return "<div class='msg_updated'>".$message."</div>";	
}
//add_action("init", "install_signup_builder");
//add_action("init","load_scripts");
add_action("admin_menu", "custom_signUp");
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
//add_action('wp_head', 'check_verification', $user_ID);
add_action('wp_head', 'load_top_widget', $user_ID);
add_action('wp_print_scripts', 'load_scripts');
//add_action('wp_print_styles', 'load_scripts');
	
	function premium_msg(){
	  global $disable_text, $readonly;
	
	  $file_name = dirname(__FILE__).'/signup-builder-access.php';
	 if (file_exists($file_name) ){
	  $mess = '<h3 style="color:green">'. DISPLAY_NAME.': Premium Version</h2>';
	  }else{
	  $mess = '<h3 style="color:green">*Upgrade to Premium for professional control and feel <a target="_blank"  href="http://plugins.onlinewebshop.net">Example</a> here</h3>';
	  $disable_text = "(<span style=color:green>*Upgrade</span>)";
	  }
	  
	  return $mess;
	
	}

function remove_admin_bar(){
 /* Disable WordPress Admin Bar for all users but admins. */
if (!current_user_can('administrator')):
  show_admin_bar(false);
endif;
}