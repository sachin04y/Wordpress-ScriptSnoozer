<?php
/**
 * Plugin Name: Script Snoozzzer
 * Description: Lazy Loads or On-Demand Loads the Styles and JS files from 3rd party plugins and themes.
 * Author: Sachin Yadav
 * Version: 1.0.0
 * Text Domain: szzz
 *
 * @package SZZZ
 */

( ! defined( 'SZZZ_PLUGIN' ) ) && define( 'SZZZ_PLUGIN', __FILE__ );
( ! defined( 'SZZZ_DIR' ) ) && define( 'SZZZ_DIR', plugin_dir_path( __FILE__ ) );
( ! defined( 'SZZZ_URL' ) ) && define( 'SZZZ_URL', plugin_dir_url( __FILE__ ) );

/*Run only at Back end */
( is_admin() ) && ( require_once SZZZ_DIR . '/includes/class-szzz-config.php' );

/*Run only at Front end */
( ! is_admin() ) && ( require_once SZZZ_DIR . '/includes/class-szzz-action.php' );

/* Plugin Links */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$links[] = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=script_snoozer_settings' ) ) . '">Settings</a>';
	return $links;
} );
/* //Plugin Links */