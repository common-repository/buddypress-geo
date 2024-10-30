<?php
/*
Plugin Name: BuddyPress Geo
Plugin URI: http://www.bravenewcode.com/buddypress-geo/
Description: Adds a geography based search to BuddyPress
Author: Duane Storey & Dale Mugford, Sponsored by Automattic
Version: 1.0.4
Author URI: http://www.bravenewcode.com/
*/

define( 'BP_GEO_VERSION', '1.0.4' );
define( 'BP_GEO_SLUG', 'geo' );
define( 'BP_GEO_DOMAIN', 'buddypress_geo' );
define( 'BP_GEO_DEFAULT_SEARCH', 1000 );

function buddypress_geo_plugin_init() {
    require( dirname( __FILE__ ) . '/buddypress-geo-bp.php' );
}

add_action( 'bp_init', 'buddypress_geo_plugin_init' );


function bp_geo_admin_head() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'buddypress-geo/buddypress-geo-bp.php' ) {
		echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';	
		echo "<link rel='stylesheet' type='text/css' media='screen' href='" . WP_PLUGIN_URL . "/buddypress-geo/css/admin.css'></link>";	
	}
}


function bp_geo_admin_init() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'buddypress-geo/buddypress-geo-bp.php' ) {	
		wp_enqueue_script( 'bp_geo_nonce', get_bloginfo('home') . "/?bp_geo_nonce=1", array( ) );	
		wp_enqueue_script( 'bp_geo_js', WP_PLUGIN_URL . "/buddypress-geo/js/geo.js", array( 'jquery', 'bp_geo_nonce' ) );			
	}
}


add_action( 'admin_head', 'bp_geo_admin_head' );
add_action( 'admin_init', 'bp_geo_admin_init' );

?>
