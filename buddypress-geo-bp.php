<?php

add_action( 'plugins_loaded', 'bp_geo_setup_root_component', 2 );
add_action( 'init', 'bp_geo_init' );
add_action( 'xprofile_screen_edit_profile', 'bp_screen_edit_profile' );
add_action( 'bp_nav_items', 'bp_geo_nav_items' );

function bp_geo_nav_items() { 
	$settings = bp_geo_get_settings(); 
	
	if ( !$settings['menu-show'] ) return;
	
	?>
	
	<li<?php if ( bp_is_page( $settings['menu-slug'] ) ) : ?> class="selected"<?php endif; ?>><a href="<?php echo get_option('home') ?>/<?php echo $settings['menu-slug']; ?>" title="<?php echo $settings['menu-name']; ?>"><?php echo $settings['menu-name']; ?></a></li>
	
	<?php 		
}

include( 'geo-members.php' );

global $bp_geo_edit_page;
$bp_geo_edit_page = false;

function bp_screen_edit_profile( $something ) {
	global $bp;
	
	if ( $bp->current_component == "profile" && $bp->current_action = "edit" ) {
		$settings = bp_geo_get_settings();
		
		if ( $bp->action_variables[0] == "group" ) {
			$group_id = $bp->action_variables[1];
			
			if ( $settings['group'] == $group_id ) {
				// we're editing the correct group	
				global $bp_geo_edit_page;
				$bp_geo_edit_page = true;
			}	
		}
	}
}

function bp_geo_get_settings( $return_defaults = false ) {
	$settings = array(
		'location' => false,
		'group' => false,
		'info' => false,
		'menu-name' => __( "Geo", BP_GEO_DOMAIN ),
		'menu-slug' => BP_GEO_SLUG,
		'menu-show' => 1,
		'show-about' => 1,
		'units' => 'kms'
	);
	
	if ( $return_defaults ) {
		return $settings;
	}
	
	$saved_settings = get_site_option( 'bp_geo_settings', false );
	if ( $saved_settings ) {
		foreach( $saved_settings as $key => $setting ) {
			$settings[ $key ] = $setting;
		}	
	}

	return $settings;
}

function bp_geo_init() {
	global $bp_geo_settings;
	global $wpdb;
	
	$bp_geo_settings = array();
	
	$table_name = bp_geo_get_table_name();
	
	$settings = bp_geo_get_settings();
	if ( $settings ) {
		$bp_geo_settings = $settings;
	}
	
	// Do database update
	if ( isset( $_GET['bp_geo_index'] ) ) {
		if ( wp_verify_nonce( $_GET["_ajax_nonce"], BP_GEO_DOMAIN ) ) {
			// We're golden!
			
			$cur_user = (int)$_GET["bp_geo_index"];
			if ( $cur_user == 0 ) {
				$sql = "DELETE FROM {$table_name} WHERE id > 0";
				$wpdb->query( $sql );
			}
			
			if ( isset( $_GET['bp_geo_last_lat'] ) && isset( $_GET['bp_geo_last_lon'] ) && isset( $_GET['bp_geo_last_user'] ) && $cur_user ) {
				$sql = $wpdb->prepare( "INSERT INTO {$table_name} (lat, lon, user_id) VALUES (%0.7f, %0.7f, %d)", $_GET['bp_geo_last_lat'], $_GET['bp_geo_last_lon'], $_GET['bp_geo_last_user'] );
				$wpdb->query( $sql );
			}
			
			$total_users = bp_geo_get_the_location_num_users();
			
			$done = false;
			$skipped = 0;
			$user_location = false;
			$user_id = 0;
			while ( true ) {
				if ( $cur_user >= $total_users ) {
					$done = true;
					break;
				}
				
				$sql = $wpdb->prepare( "SELECT ID FROM {$wpdb->users} LIMIT 1 OFFSET %d", $cur_user );
				$result = $wpdb->get_row( $sql );
				if ( $result ) {
					$user_id = $result->ID;
					$user_location = geo_member_get_location( $result->ID );
				}
				
				if ( $user_location ) {
					break;	
				} else {
					$skipped++;
					$cur_user++;
				}
			}
			
			if ( $done ) {
				$status = 0;
			} else {
				$status = 1;
			}
			
			echo '{"userNum": ' . $user_id . ',"userNumPlusOne": ' . ($cur_user + 1) . ',"userLocation": "' . $user_location . '", "nextUser": ' . ($cur_user + 1) . ', "doneStatus": ' . $status . ', "skipped": ' . $skipped . '}';
		}
		die;
	} else if ( isset( $_GET["bp_geo_nonce"] ) ) {
		header( "Content-type: text/javascript" );
		echo( "var bpGeoNonce = '" . wp_create_nonce( BP_GEO_DOMAIN ) . "';" );
		die;	
	}
	
	if ( isset( $_POST["_wpnonce"] ) && is_admin() ) {		
		// Check the nonce
		$nonce = $_POST['_wpnonce'];
		if ( wp_verify_nonce( $nonce, BP_GEO_DOMAIN ) ) {	
			if ( isset( $_POST["install-db"] ) ) {
				bp_geo_check_install();
			} else if ( isset( $_POST["remove-db"] ) ) {
				$sql = "DROP TABLE {$table_name}";
				$wpdb->query( $sql );
			} else if ( isset( $_POST["reset"] ) ) {
				$bp_geo_settings = bp_geo_get_settings( true );
				
				update_site_option( 'bp_geo_settings', $bp_geo_settings );
			} else if ( isset( $_POST['Submit'] ) ) {
				$bp_geo_settings['location'] = strip_tags( $_POST['location'] );
				$bp_geo_settings['group'] = strip_tags( $_POST['group'] );
				$bp_geo_settings['info'] = strip_tags( $_POST['info'] );
				$bp_geo_settings['menu-name'] = strip_tags( $_POST['menu-name'] );
				$bp_geo_settings['menu-slug'] = strip_tags( $_POST['menu-slug'] );
				$bp_geo_settings['menu-show'] = isset( $_POST['menu-show'] );
				$bp_geo_settings['show-about'] = isset( $_POST['show-about'] );
				$bp_geo_settings['units'] = strip_tags( $_POST['units'] );
				
				update_site_option( 'bp_geo_settings', $bp_geo_settings );
			}	
		}
	}
}

function buddypress_geo_show_list() {
	$bp->is_directory = true;
	
	do_action( 'geo_directory_geo_setup' );
	bp_core_load_template( 'directories/geo/index' );	
}


function bp_geo_setup_root_component() {
	global $bp;
	$settings = bp_geo_get_settings();
	
	bp_core_add_root_component( $settings['menu-slug'] );
}

function bp_geo_get_table_name() {
	global $wpdb;
	return $wpdb->base_prefix . "bp_user_geo";		
}

function bp_geo_has_table() {
   global $wpdb;

   $table_name = bp_geo_get_table_name();	
   	
	return ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name );	
}

function bp_geo_check_install() {
   global $wpdb;

   $table_name = bp_geo_get_table_name();	
   
   $charset = '';
   if ( $wpdb->charset ) {
   	$charset = " DEFAULT CHARSET={$wpdb->charset}";	
   }
   
   if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
   	$sql = "CREATE TABLE " . $table_name . " (
		  `id` int(11) NOT NULL auto_increment,
		  `user_id` int(11) default 0,
		  `lat` decimal(10,7) default 0,
		  `lon` decimal(10,7) default 0,
		  PRIMARY KEY  (`id`),
		  KEY `user_index` (`user_id`),
		  KEY `lon_key` (`lon`),
		  KEY `lat_index` (`lat`)
		) {$charset};";
			
		// now create the database
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );      	
   }

}

function bp_setup_geo() {	
	global $bp;
	$settings = bp_geo_get_settings();
		
	if ( $bp->current_component == $settings['menu-slug'] && empty( $bp->current_action ) ) {
	        $bp->is_directory = true;
	
	        do_action( 'geo_directory_geo_setup' );
	        bp_core_load_template( 'directories/geo/index' );
	}	
}

add_action( 'wp', 'bp_setup_geo', 2 );


function bp_geo_wp() {
	global $wpdb;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'bp_geo_js', WP_CONTENT_URL . "/plugins/buddypress-geo/js/geo.js", array( 'jquery' ) );

	
}

function bp_geo_head() {
	global $bp;
	global $bp_geo_edit_page;
	$settings = bp_geo_get_settings();
	
	if ( $bp_geo_edit_page || $bp->current_component == $settings["menu-slug"] ) {
		echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
		echo '<script type="text/javascript">var bpGeoLocation = "' . get_bloginfo( 'home' ) . '/' . $settings["menu-slug"] . '"; </script>';
	}
	
	echo "<link rel='stylesheet' type='text/css' media='screen' href='" . WP_PLUGIN_URL . "/buddypress-geo/css/style.css'></link>";

}

function bp_geo_updated_profile() {
	$latitude = false;
	$longitude = false;
	
	if ( bp_has_profile() ) {
		while ( bp_profile_groups() )  {
			bp_the_profile_group();	
			
			if ( bp_profile_group_has_fields() ) {
				while ( bp_profile_fields() ) {
					bp_the_profile_field();	
					
					if ( bp_field_has_data() ) {
												
						if ( bp_get_the_profile_field_name() == "Latitude" ) {
							$latitude = (float)strip_tags( bp_get_the_profile_field_value() );							
						} else if ( bp_get_the_profile_field_name() == "Longitude" ) {
							$longitude = (float)strip_tags( bp_get_the_profile_field_value() );
						}
					}
				}
			}
		}
	}
	
	if ( $latitude == false || $longitude == $false ) {
		return;	
	}
	
	global $bp;
	global $wpdb;
	$user_id = $bp->displayed_user->id;
	
   $table_name = bp_geo_get_table_name();

	$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d", $user_id ) );
	if ( !$result ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$table_name} (user_id, lat, lon) VALUES (%d, %0.5f, %0.5f)", $user_id, $latitude, $longitude ) );	
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE {$table_name} SET lat = %0.7f, lon = %0.7f WHERE user_id = %d", $latitude, $longitude, $user_id ) );	
	}
}

add_action( 'admin_menu', 'geo_plugin_menu' );

function geo_plugin_menu() {
	if ( !is_site_admin() )
		return false;
			
	add_submenu_page( 'bp-general-settings', __( "Geo Search Setup", BP_GEO_DOMAIN ), __( "Geo Search Setup", BP_GEO_DOMAIN ), 'administrator', __FILE__, "geo_plugin_options" );
}

function geo_plugin_options() {
	$groups = array();
	$fields = array();
	
	if ( bp_has_profile() ) {  
		while ( bp_profile_groups() )  {
			global $group;
			
			bp_the_profile_group();	
			
			$one_group = array();
			$one_group['id'] = $group->id;
			$one_group['name'] = $group->name;
			
			$groups[ $group->id ] = $one_group;
					
			if ( bp_profile_group_has_fields() ) {
				while ( bp_profile_fields() ) {
					bp_the_profile_field();	
					
					if ( bp_field_has_data() ) {
						global $field;
						
						$one_field = array();
						$one_field['id'] = $field->id;
						$one_field['name'] = $field->name;
						
						$fields[] = $one_field;
					}
				}
			}
		}  
	}
	
	include( 'html/options.php' );
}


function bp_geo_get_the_location_num_users() {
	global $wpdb;
	$total = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) as c FROM {$wpdb->users}" ) );
	if ( $total ) {
		return $total->c;	
	}
}

function bp_geo_get_the_location_num_geo_users() {
	global $wpdb;
	$table_name = bp_geo_get_table_name();
	$total = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) as c FROM {$table_name}" ) );
	if ( $total ) {
		return $total->c;	
	}
}

add_action( 'wp', 'bp_geo_wp' );
add_action( 'wp_head', 'bp_geo_head' );

add_action( 'xprofile_updated_profile', 'bp_geo_updated_profile' );

register_activation_hook( __FILE__, 'bp_geo_check_install' );

?>