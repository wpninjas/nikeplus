<?php
/*
Plugin Name: Nike+
Plugin URI: http://wpninjas.net
Description: This plugin allows you to display your Nike PLus run stats on your website via widgets or shortcodes
Version: 1.0
Author: WP Ninjas
Author URI: http://wpninjas.net
*/

/**
 * Define constants
 **/
define('NIKE_PLUS_DIR', WP_PLUGIN_DIR.'/nikeplus/');
define('NIKE_PLUS_URL', WP_PLUGIN_URL.'/nikeplus/');

/**
 * Load plugin textdomain
 **/
load_plugin_textdomain('nikeplus', false, NINJA_NIKE_DIR . 'languages' );

/**
 * Include core files
 **/
require_once( NIKE_PLUS_DIR . 'inc/nikeplusphp.4.3.2.php' );
require_once( NIKE_PLUS_DIR . 'options/options.php' );
require_once( NIKE_PLUS_DIR . 'options/user.php' );
require_once( NIKE_PLUS_DIR . 'functions/post-type.php' );
require_once( NIKE_PLUS_DIR . 'functions/nike-data.php' );
require_once( NIKE_PLUS_DIR . 'functions/list-runs.php' );
require_once( NIKE_PLUS_DIR . 'functions/individual-totals.php' );
require_once( NIKE_PLUS_DIR . 'functions/personal-records.php' );
require_once( NIKE_PLUS_DIR . 'functions/team-totals.php' );

add_action( 'nikeplus_update_runs', 'nikeplus_update_runs' );

register_activation_hook( __FILE__, 'nikeplus_activation' );


function nikeplus_activation() {

    $opt = get_option( 'nikeplus_options' );
    $sched = wp_get_schedule( 'nikeplus_update_runs' );

    if( !isset( $opt['update_frequency'] ) ) {
        wp_schedule_event( time(), 'daily', 'nikeplus_update_runs' );
    } elseif( $opt['update_frequency'] != $sched ) {
        wp_clear_scheduled_hook( 'nikeplus_update_runs' );
        wp_schedule_event( time(), $opt['update_frequency'], 'nikeplus_update_runs' );
    }

}

function nikeplus_clear_event() {
    $next = wp_next_scheduled( 'nikeplus_update_runs' );
    wp_unschedule_event( $next, 'nikeplus_update_runs' );
}
//add_action('init', 'nikeplus_clear_event');
function nikeplus_view_schedule() {
    if( current_user_can( 'administrator' ) ) {
        $next = wp_next_scheduled( 'nikeplus_update_runs' );
        $sched = wp_get_schedule( 'nikeplus_update_runs' );
        echo date( 'l F jS Y \a\t h:i A', current_time('timestamp', 1 ) ) . '<br />';
        echo date( 'l F jS Y \a\t h:i A', $next ) . '<br />';
        echo $sched;
    }
}
//add_action( 'tha_entry_before', 'nikeplus_view_schedule' );

function nikeplus_admin_actions() {
  if( is_admin() )
    add_action( 'admin_init', 'nikeplus_runs_redirect' );
}
add_action( 'init', 'nikeplus_admin_actions' );

function nikeplus_add_my_stylesheet() {
    wp_register_style( 'nikeplus-style', NIKE_PLUS_URL . 'inc/style.css' );
    wp_enqueue_style( 'nikeplus-style' );
}
//add_action( 'wp_enqueue_scripts', 'nikeplus_add_my_stylesheet' );