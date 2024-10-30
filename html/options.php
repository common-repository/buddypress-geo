<?php global $bp_geo_settings;  ?>
<div class="wrap" id="bnc-theme">	
	<div class="metabox-holder" id="bnc-head">
		<div class="postbox">
			<div id="bnc-head-colour">
				<div id="bnc-head-title">
					<?php _e( "BuddyPress Geo", BP_GEO_DOMAIN ); ?> <?php echo BP_GEO_VERSION; ?><br />
					<span id="bnc-sponsor">Sponsored by Automattic</span>
				</div>
				<div id="bnc-head-links">
					<ul>
						<li><a href="http://support.bravenewcode.com/forum/buddypress-geo" target="_blank"><?php _e( 'Support Forums', BP_GEO_VERSION ); ?></a> | </li>
						<li><a href="http://www.bravenewcode.com/buddypress-geo" target="_blank"><?php _e( 'BuddyPress Geo Homepage', BP_GEO_VERSION ); ?></a> | </li>
						<li><a href="http://www.bravenewcode.com/newsletter" target="_blank"><?php _e( 'Newsletter' ); ?></a> | </li>
						<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=paypal%40bravenewcode%2ecom&amp;item_name=BuddyPress%20Geo%20Beer%20Fund&amp;no_shipping=1&amp;tax=0&amp;currency_code=CAD&amp;lc=CA&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8" target="_blank"><?php _e( 'Donate', BP_GEO_VERSION ); ?></a></li>
					</ul>
				</div>				
				<div class="bnc-clearer"></div>
			</div> <!-- bnc-head-colour -->
		</div> <!-- postbox -->
		
		<form method="POST" action="">	
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( BP_GEO_DOMAIN ); ?>" />	
			<div class="postbox">
				<div class="bnc-clearer"></div>
				<h3><?php _e( "General Settings", BP_GEO_DOMAIN ); ?>	</h3>
				<div class="bnc-clearer"></div>			
				<div class="bnc-left-content">
					<h4><?php _e( "Profile Information", BP_GEO_DOMAIN ); ?></h4>
					<p><?php _e( "Use this section to configure which fields are to be used for the search functionality.", BP_GEO_DOMAIN ); ?></p>
					<p><?php echo sprintf( __( "Remember to create a textbox field named <strong>Latitude</strong> and a textbox field named <strong>Longitude</strong> in the same group that holds your other location information (currently this group is set to <strong>%s</strong>).  These fields should not be required and will ultimately be hidden to the user.", BP_GEO_DOMAIN ),  $groups[$bp_geo_settings['group']]["name"]); ?>
				</div>			
				
				<div class="bnc-right-content">
					<table class="form-table">
					
						<tr valign="top">
							<th>
								<label for="group"><?php _e( "Which group contains the location information?", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<select id="group" name="group">
									<?php foreach( $groups as $id => $group ) { ?>
									<option value="<?php echo $group['id']; ?>"<?php if ( $bp_geo_settings['group'] == $group['id'] ) echo " selected"; ?>><?php echo $group['name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						
						<tr valign="top">
							<th>
								<label for="location"><?php _e( "Which field represents each user's location?", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<select id="location" name="location">
									<?php foreach( $fields as $field ) { ?>
									<option value="<?php echo $field['id']; ?>"<?php if ( $bp_geo_settings['location'] == $field['id'] ) echo " selected"; ?>><?php echo $field['name']; ?></option>
									<?php } ?>
								</select>	
							</td>
						</tr>
						
						<tr valign="top">
							<th>
								<label for="location"><?php _e( "Which field represents a description for each user (i.e. 'About Me')?", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<select id="location" name="info">
									<?php foreach( $fields as $field ) { ?>
									<option value="<?php echo $field['id']; ?>"<?php if ( $bp_geo_settings['info'] == $field['id'] ) echo " selected"; ?>><?php echo $field['name']; ?></option>
									<?php } ?>
								</select>	
							</td>
						</tr>		
					</table>
				</div>
				<div class="bnc-clearer"></div>
			
				<div class="bnc-left-content">
					<h4><?php _e( "Menu Information", BP_GEO_DOMAIN ); ?></h4>
					<p>
						<?php _e( "Use this section to configure which fields are to be used for the search functionality.", BP_GEO_DOMAIN ); ?>
					</p>
					<?php $url = get_bloginfo('home') . '/' . $bp_geo_settings['menu-slug']; ?>
					<p><?php echo sprintf( __( "The geo search is currently located at<br /> <a href='%s' target='_blank'>%s</a>", BP_GEO_DOMAIN ), $url, str_replace( 'http://', '', $url ) ); ?>.</p>
				</div>	
							
				<div class="bnc-right-content">
					<table class="form-table">			
						
						<tr valign="top">
							<th>
								<label for="menu-slug"><?php _e( "Geo search menu slug", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<input type="text" id="menu-slug" name="menu-slug" value="<?php echo $bp_geo_settings['menu-slug']; ?>" />
							</td>
						</tr>		
												
						<tr valign="top">
							<th>
								<label for="menu-name"><?php _e( "Geo search menu name", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<input type="text" id="menu-name" name="menu-name" value="<?php echo $bp_geo_settings['menu-name']; ?>" />
							</td>
						</tr>		
				
							
					
					</table>													
				</div> <!-- bnc-right-content -->
				<div class="bnc-clearer"></div>
				
				<div class="bnc-left-content">
					<h4><?php _e( "Site Options", BP_GEO_DOMAIN ); ?></h4>
					<p>
						<?php _e( "Use this section to configure various options for the geo search.", BP_GEO_DOMAIN ); ?>
					</p>
					
				</div>	
							
				<div class="bnc-right-content">
					<table class="form-table">			
					
						<tr valign="top">
							<th>
								<label for="group"><?php _e( "Which units of measurement do you want to use for distance?", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
								<select id="group" name="units">
									<option value="miles"<?php if ( $bp_geo_settings['units'] == "miles" ) echo " selected"; ?>><?php _e( "Miles", BP_GEO_DOMAIN ); ?></option>
									<option value="kms"<?php if ( $bp_geo_settings['units'] == "kms" ) echo " selected"; ?>><?php _e( "Kilometers", BP_GEO_DOMAIN ); ?></option>
								</select>
							</td>
						</tr>
												
						<tr valign="top">
							<th>
								<input class="checkbox" type="checkbox" id="menu-show" name="menu-show"<?php if ( $bp_geo_settings['menu-show'] ) echo " checked"; ?> />
								<label for="menu-show"><?php _e( "Show menu item on main page", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
							</td>
						</tr>					
						
						<tr valign="top">
							<th>
								<input class="checkbox" type="checkbox" id="show-about" name="show-about"<?php if ( $bp_geo_settings['show-about'] ) echo " checked"; ?> />
								<label for="show-about"><?php _e( "Show profile descriptions", BP_GEO_DOMAIN ); ?></label>
							</th>
							<td>
							</td>
						</tr>																		
					</table>													
				</div> <!-- bnc-right-content -->	
				<div class="bnc-clearer"></div>
				
				<div class="bnc-left-content">
					<h4><?php _e( "Statistics", BP_GEO_DOMAIN ); ?></h4>
					<p><?php _e( "This section contains a few statistics about the current user location information.  When a user edits their profile, the associated user location information is either created or updated.", BP_GEO_DOMAIN ); ?></p>
					<p><?php _e( "For new installations, you may build the entire location information table manually. The build process <strong>may take a while</strong> for sites with a lot of users.", BP_GEO_DOMAIN ); ?></p>
					
				</div>	
							
				<div class="bnc-right-content">
					<?php if ( bp_geo_has_table() ) { ?>
					<p><?php echo sprintf( __( "Currently <strong><span id='bp-geo-num'>%d</span></strong> out of <strong>%d</strong> users have been indexed.", BP_GEO_DOMAIN), bp_geo_get_the_location_num_geo_users(), bp_geo_get_the_location_num_users() ); ?></p>
					<p><?php echo sprintf( __( "To build or rebuild the entire user location table, please click <a id='bp-geo-rebuild' href='#'><strong>here</strong></a>.  Prior to building, <br />please ensure the location field selected above contains real location information for each user.", BP_GEO_DOMAIN ) ); ?></p>	
					<p id='ajax-location' style='display:none'><?php _e( "Adding user located in <em></em>", BP_GEO_DOMAIN ) ?></p>
					<p id='ajax-done' style='display:none'><em><?php _e( "User location table successfully rebuilt", BP_GEO_DOMAIN ) ?></em></p>
					<?php } else { ?>
					<p class="warning"><?php _e( "The database table is currently missing.  Please deactivate and reactivate the plugin, <br/> or click <em>Install Database Table</em> below.", BP_GEO_DOMAIN ); ?></p>
					<?php } ?>										
				</div> <!-- bnc-right-content -->					
				
			</div> <!-- postbox -->
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
				<input type="submit" value="<?php _e( "Restore Defaults", BP_GEO_DOMAIN ); ?>" onclick="return confirm('<?php _e( "Restore default settings?", BP_GEO_DOMAIN ); ?>');" name="reset" id="bnc-button-reset" class="button-highlighted" />
				<?php if ( bp_geo_has_table() ) { ?>
				<input type="submit" value="<?php _e( "Remove Database Table", BP_GEO_DOMAIN ); ?>" onclick="return confirm('<?php _e( "Are you sure you want to remove the database table?", BP_GEO_DOMAIN ); ?>');" name="remove-db" class="button-highlighted" />
				<?php } else { ?>
				<input type="submit" value="<?php _e( "Install Database Table", BP_GEO_DOMAIN ); ?>" name="install-db" class="button-highlighted" />				
				<?php } ?>
			</p>			
		</form>
	</div>
</div>