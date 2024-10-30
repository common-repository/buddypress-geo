<?php

function bp_geo_deg2rad( $deg ) {
	$m_pi = 3.1415926535;
	return $deg*$m_pi/180.0;
}

function bp_geo_rad2deg( $rad ) {
	$m_pi = 3.1415926535;
	return $rad*180.0/$m_pi;
}

class Geo_Profiles_Template {
	var $current_item = -1;
	var $item_count;
	var $items;
	var $item;
	var $item_types;
	var $cur_type;
	
	var $in_the_loop;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	
	function geo_profiles_template( $user_id, $type, $per_page, $max, $within, $units ) {
		global $bp;
		global $wpdb;
		
		if ( !$user_id )
			$user_id = $bp->displayed_user->id;
			
		$this->pag_page = isset( $_REQUEST['geopage'] ) ? intval( $_REQUEST['geopage'] ) : 1;
		$this->pag_num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : $per_page;
		$this->user_id = $user_id;
		$this->cur_type = $type;
		 
		$table_name = bp_geo_get_table_name();
		
		$total_users = 0;
		$total = $wpdb->get_row( $wpdb->prepare( "SELECT count(*) as c FROM {$table_name} WHERE user_id != %d", $bp->loggedin_user->id ) );
		if ( $total ) {
			$total_users = $total->c;	
		}
						
		$lat = 0;
		$lon = 0;
		if ( $type == "distance" ) {
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d", $bp->loggedin_user->id ) );
			if ( $result ) {
				$lat = $result->lat;
				$lon = $result->lon;	
			}
		} else if ( $type == "near" ) {
			if ( isset( $_GET['lat'] ) && isset( $_GET['lon'] ) ) {
				$lat = (float)$_GET['lat'];
				$lon = (float)$_GET['lon'];
				
				if ( $lat > 90 || $lat < -90 ) {
					$lat = 0;
				}
				
				if ( $lon > 180 || $lon < -180 ) {
					$lon = 0;	
				}
			}
		}
		
		// Calculate a rough bounding box
		// !ds - Pretty sure the edge cases at +/-180 and +/- 90 will need work, but should be ok for most regions
		$m_pi = 3.1415926535;
		if ( $units == "miles" ) {
			$radius_earth = 3963;
		} else {
			$radius_earth = 6378;	
		}
		
		$max_lat = $lat + bp_geo_rad2deg( $within/($radius_earth) );
		$min_lat = $lat - bp_geo_rad2deg( $within/($radius_earth) );
		$max_lon = $lon + bp_geo_rad2deg( $within/($radius_earth)/cos( deg2rad( $lat ) ) );
		$min_lon = $lon - bp_geo_rad2deg( $within/($radius_earth)/cos( deg2rad( $lat ) ) );
				
		$sql = $wpdb->prepare( "SELECT count(*) as c FROM {$table_name} WHERE lat < %0.7f AND lat > %0.7f AND lon < %0.7f AND lon > %0.7f", $max_lat, $min_lat, $max_lon, $min_lon );
		$total = $wpdb->get_row( $sql );	
		if ( $total ) {
			$total_users = $total->c;	
		}
		
		$sql = $wpdb->prepare( "SELECT *, {$radius_earth} * 2 * ASIN(SQRT(POWER(SIN((%0.7f - dest.lat)*pi()/180 / 2), 2) + COS(%0.7f * pi()/180) * COS(dest.lat * pi()/180) * POWER(SIN((%0.7f - dest.lon) * pi()/180 / 2), 2))) AS distance FROM {$table_name} dest WHERE lat < %0.7f AND lat > %0.7f AND lon < %0.7f AND lon > %0.7f ORDER BY distance ASC LIMIT %d OFFSET %d", $lat, $lat, $lon, $max_lat, $min_lat, $max_lon, $min_lon, $this->pag_num, ( $this->pag_page - 1 ) * $this->pag_num );
				
		$data = $wpdb->get_results( $sql );
		$this->items = $data;		
		
		// Item Requests
		$this->total_item_count = (int)$total_users;	
		$this->item_count = count( $this->items );
				
		/* Remember to change the "x" in "xpage" to match whatever character(s) you're using above */
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'geopage', '%#%' ),
			'format' => '',
			'total' => ceil( (int) $this->total_item_count / (int) $this->pag_num ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 1			
		));
	}
	
	function has_items() {
		if ( $this->item_count )
			return true;
		
		return false;
	}
	
	function next_item() {
		$this->current_item++;
		
		if ( $this->current_item == 0 ) {
			$this->item = current( $this->items );
		} else {
			$this->item = next( $this->items );
		}
		
		return $this->item;
	}
	
	function rewind_items() {
		$this->current_item = -1;
		if ( $this->item_count > 0 ) {
			$this->item = reset( $this->items );
		}
	}
	
	function user_items() { 
		if ( $this->current_item + 1 < $this->item_count ) {
			return true;
		} elseif ( $this->current_item + 1 == $this->item_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_items();
		}

		$this->in_the_loop = false;
		return false;
	}
	
	function the_item() {
		global $item, $bp;

		$this->in_the_loop = true;
		$this->item = $this->next_item();
				
		if ( 0 == $this->current_item ) // loop has just started
			do_action('loop_start');
	}
}

function geo_profiles_has_items( $args = '' ) {
	global $bp, $geo_items_template;	
	$settings = bp_geo_get_settings();
	
	$defaults = array(
		'user_id' => false,
		'per_page' => 4,
		'max' => false,
		'type' => 'distance',
		'within' => BP_GEO_DEFAULT_SEARCH
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$geo_items_template = new Geo_Profiles_Template( $user_id, $type, $per_page, $max, $within, $settings["units"] );
		
	return $geo_items_template->has_items();
}

function geo_profiles_the_item() {
	global $geo_items_template;
	return $geo_items_template->the_item();
}

function geo_profiles_items() {
	global $geo_items_template;
	return $geo_items_template->user_items();
}

function geo_profiles_the_member_avatar() {
	echo geo_profiles_get_member_avatar();
}

function geo_profiles_get_member_avatar() {
	global $geo_items_template;	
	return apply_filters( 'geo_profiles_get_member_avatar', bp_core_fetch_avatar( array( 'item_id' => $geo_items_template->item->user_id ) ) );
}

function geo_profiles_the_site_member_name() {
	echo geo_profiles_get_site_member_name();
}

function geo_profiles_get_site_member_name() {
	global $geo_items_template;
	global $wpdb;
	
	$result = $wpdb->get_row( $wpdb->prepare( "SELECT display_name FROM {$wpdb->users} WHERE id = %d", $geo_items_template->item->user_id ) );
	if ( $result ) {
		return apply_filters( 'geo_profiles_get_site_member_name', $result->display_name );
	}
}

function geo_profiles_the_member_distance() {
	echo geo_profiles_get_member_distance();
}

function geo_profiles_get_member_distance() {
	global $geo_items_template;
	return apply_filters( 'geo_profiles_get_member_distance', number_format( $geo_items_template->item->distance ) );	
}

function geo_profiles_pagination_links() {
	echo geo_profiles_get_pagination_links();
}

function geo_profiles_get_pagination_links() {
	global $geo_items_template;
	return apply_filters( 'geo_profiles_get_pagination_links', $geo_items_template->pag_links );		
}

function geo_profiles_pagination_count() {
        global $bp, $geo_items_template;

        $from_num = intval( ( $geo_items_template->pag_page - 1 ) * $geo_items_template->pag_num ) + 1;
        $to_num = ( $from_num + ( $geo_items_template->pag_num - 1 ) > $geo_items_template->total_item_count ) ? $geo_items_template->total_item_count : $from_num + ( $geo_items_template->pag_num - 1) ;

        echo sprintf( __( 'Viewing member %d to %d (of %d members)', BP_GEO_DOMAIN ), $from_num, $to_num, $geo_items_template->total_item_count ); 
}

function geo_profiles_user_link() {
	echo 	geo_profiles_get_user_link();
}

function geo_profiles_get_user_link() {
	global $geo_items_template;
	return apply_filters( 'geo_profiles_get_user_link', bp_core_get_user_domain( $geo_items_template->item->user_id ) );
}
       
function geo_profiles_friendly_search_type( $sep ) {
	$settings = bp_geo_get_settings();
	
	if ( isset( $_GET['friendly'] ) ) {
		if ( !isset( $_GET['within'] ) ) {
			echo $sep . sprintf( __("Near %s", BP_GEO_DOMAIN ), htmlentities( strtoupper( $_GET['friendly'] ) ) );
		} else {
			$within = (int)$_GET['within'];
			if ( $within == 100000 ) {
				echo $sep . sprintf( __("Centered on %s", BP_GEO_DOMAIN ), strtoupper( htmlentities( $_GET['friendly'] ) ) );	
			} else {
				if ( $settings["units"] != "miles" ) {
					echo $sep . sprintf( __("Within %s kilometers of %s", BP_GEO_DOMAIN ), number_format( $within ), strtoupper( htmlentities( $_GET['friendly'] ) ) );
				} else {
					echo $sep . sprintf( __("Within %s miles of %s", BP_GEO_DOMAIN ), number_format( $within ), strtoupper( htmlentities( $_GET['friendly'] ) ) );	
				}
			}
		}
	}	
}

function geo_member_location() {
	echo geo_member_get_location();
}

function geo_member_get_location( $user_id = 0 ) {
	global $bp;
	global $geo_items_template;
	
	if ( $user_id == 0 ) {
		$user_id = $geo_items_template->item->user_id;
	}
	
	$location = '';
	$settings = bp_geo_get_settings();
	$location_field = xprofile_get_field( $settings['location'] );
	if ( $location_field ) {
		$location = xprofile_get_field_data( $location_field->name, $user_id );	
	}

	return apply_filters( 'geo_member_get_location', $location );
}

function geo_member_info() {
	echo geo_member_get_info();
}

function geo_member_get_info() {
	global $bp;
	global $geo_items_template;
	
	$settings = bp_geo_get_settings();
	if ( !$settings['show-about'] ) {
		return;
	}	
	
	$info = '';
	$settings = bp_geo_get_settings();
	$info_field = xprofile_get_field( $settings['info'] );
	if ( $info_field ) {
		$info = xprofile_get_field_data( $info_field->name, $geo_items_template->item->user_id );		
	}
	
	return apply_filters( 'geo_member_get_info', $info );
}	

function geo_profiles_miles_or_kms( $dist ) {
	$settings = bp_geo_get_settings();
	if ( $settings['units'] == "miles" ) {
		return sprintf( __( "%d miles away", BP_GEO_DOMAIN ), $dist );
	} else {
		return sprintf( __( "%d kilometers away", BP_GEO_DOMAIN ), $dist );
	}		
}

function geo_profiles_get_search_form() {
	$settings = bp_geo_get_settings();
	$distances = array( 10, 50, 100, 250, 1000 );
	
	$location = __( 'Near this location...', BP_GEO_DOMAIN );
	if ( isset( $_GET['friendly'] ) ) {
		$location = $_GET['friendly'];	
	}
	
	$distance = BP_GEO_DEFAULT_SEARCH;
	if ( isset( $_GET['within'] ) ) {
		$distance = (int)$_GET['within'];
	}	
	
	?>
	<form method="GET" action="" id="geo-form">
		<input type="text" name="geo-search" id="geo-search-field" value="<?php echo $location; ?>" onfocus="this.value=''" /><br /><br />
		
		<label for="geo-distance"><?php _e( "Distance", BP_GEO_DOMAIN ); ?></label>
		<select name="geo-distance" id="geo-distance">
			<?php foreach( $distances as $d ) { ?>
				<?php if ( $settings["units"] == "miles" ) { ?>
					<option value="<?php echo $d; ?>"<?php if ( $distance == $d ) echo " selected"; ?>><?php echo sprintf( __( "%s miles", BP_GEO_DOMAIN ), number_format( $d ) ); ?></option>
				<?php } else { ?>
					<option value="<?php echo $d; ?>"<?php if ( $distance == $d ) echo " selected"; ?>><?php echo sprintf( __( "%s kilometers", BP_GEO_DOMAIN ), number_format( $d ) ); ?></option>
				<?php } ?>
			<?php } ?>
		</select>
		<input type="submit" name="submit" id="geo-submit" value="<?php _e( 'Search', BP_GEO_DOMAIN ); ?>" />
	</form>
	<?php

}

        
?>